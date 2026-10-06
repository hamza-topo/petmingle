# React frontend deployment

The hostnames are deployment parameters. No production domain or live infrastructure is assumed by this repository. Serve the React app at the root of its own HTTPS origin and Laravel at a separate browser-facing HTTPS origin. Keep the existing Laravel Nginx host for Laravel; use frontend/docker/nginx.conf for the static frontend.

## Public build configuration

Copy frontend/.env.production.example to frontend/.env.production.local and set:

| Setting | Required value |
| --- | --- |
| VITE_API_BASE_URL | Actual HTTPS Laravel URL ending in /api/v.0 |
| VITE_MEDIA_BASE_URL | Optional HTTPS origin serving /storage; blank derives the API origin |
| VITE_REALTIME_ENABLED | false until the realtime service is deployed; true with a public app key enables it |
| VITE_PUSHER_APP_KEY | Public Pusher/Soketi app key, never the app secret |
| VITE_PUSHER_APP_CLUSTER | Public cluster name |
| VITE_PUSHER_WS_HOST | Optional public socket hostname without scheme/path |
| VITE_PUSHER_WS_PORT | Socket port, normally 443 |
| VITE_PUSHER_WS_SCHEME | wss for enabled deployment realtime |

Production builds fail if the API setting is empty, relative, HTTP, loopback, contains URL credentials/query/fragment, or uses the wrong prefix. Media URLs must be origins. Unknown VITE_ variables fail the build: all Vite-prefixed values are public, including values exposed through import.meta.env. Never put APP_KEY, DB passwords, Bearer tokens, OAuth client secrets or PUSHER_APP_SECRET in frontend files or build arguments. The public-key allowlist is deliberately narrow; update it together with a documented new public setting.

Vite embeds these values at **build time**. Changing environment variables on a running Nginx container does not reconfigure the bundle. Rebuild/redeploy after changing an API/media/socket URL. Existing shell/CI environment variables override dotenv files. A development .env.local may otherwise leak local settings into a deployment build: the guard rejects them. The dedicated desktop-review build mode permits HTTP loopback only for the existing local browser harness, and must not be deployed.

## Docker build and serve

From the repository root, set your actual public URLs (the values below are illustrative, not live deployments):

```sh
docker build -t petmingle-frontend:release \
  --build-arg VITE_API_BASE_URL=https://api.example.com/api/v.0 \
  --build-arg VITE_MEDIA_BASE_URL=https://api.example.com \
  --build-arg VITE_REALTIME_ENABLED=false \
  frontend
docker run --rm -p 127.0.0.1:8088:8080 petmingle-frontend:release
```

Put a TLS reverse proxy/load balancer in front of port 8080, using the actual frontend hostname and certificate. Forward all paths to the static container without rewriting them. The image build excludes local dotenv files, node_modules and prior build artifacts; no backend secrets belong in its context. The runtime image contains the built static files only. Tags are maintained upstream; pin approved image digests in your deployment system when releasing an immutable image.

Nginx serves /index.html with no-store and hashed /assets files with immutable caching. React Router destinations fall back to index.html so /signin, /discover, /profile, /pet/create, /messages and /matches survive direct navigation/refresh. Missing assets, /api/, /broadcasting/auth and /storage/ return errors rather than the SPA shell. Serve the app at the origin root; deployment under a subpath needs a coordinated Vite base and router basename change.

For another static host, configure equivalent root SPA rewrites, real 404s for missing assets/API/media, JavaScript MIME types and cache behavior. Do not use vite preview as the production server.

## Backend CORS and media

In the Laravel environment, set APP_ENV=production, APP_DEBUG=false and the actual HTTPS APP_URL. Replace CORS_ALLOWED_ORIGINS with a comma-separated list of **exact** HTTPS frontend origins, without trailing slashes, paths or wildcards:

```dotenv
CORS_ALLOWED_ORIGINS=https://app.example.com
```

This is an example; substitute the real chosen origin. Non-local/testing environments reject malformed, HTTP and wildcard entries. An empty list grants no browser access. Rebuild Laravel's configuration cache after changes, inside the backend container:

```sh
docker compose exec app php artisan config:clear
docker compose exec app php artisan config:cache
```

Only api/* and broadcasting/auth have CORS. GET/HEAD/POST/PUT/PATCH/DELETE/OPTIONS and Accept/Authorization/Content-Type are allowed. Credentials are disabled: the frontend uses a user-issued Bearer token, not Sanctum SPA cookies. Configure CORS in Laravel rather than adding conflicting wildcard headers in the reverse proxy. Proxies must pass OPTIONS and Authorization through to Laravel. With a single allowed origin the CORS library may emit that constant header on unrelated requests; a browser still denies access if it differs from its Origin.

Laravel must expose its existing public storage link under /storage and generate browser-facing HTTPS media URLs. Do not expose private storage or the Laravel repository root. Realtime needs WSS and authenticated private-channel authorization at the API origin's /broadcasting/auth; keep the server secret only in Laravel/Soketi.

## Verification and deployment handoff

CI checks the public build guard, unit/persistence tests, HTTPS-origin preflights for API and broadcasting/auth, a real Sanctum Bearer identity request, denied origins, the desktop matrix and an actual Nginx static container. The production test bundle uses reserved test hostnames. Chromium checks seven direct routes and reloads, the embedded API URL, script MIME/cache headers and rejection of missing/static API/media paths.

CI does not deploy public DNS/certificates or query a production database. After choosing hosts, complete these checks on the actual deployment:

1. Build using the real public URLs; verify the served bundle sends requests to the intended API, not localhost/container names.
2. From the actual HTTPS frontend, sign in, reload a protected route, then sign out. Check OPTIONS allows the exact frontend origin and Authorization; verify /me and private-channel authorization succeed with the user token.
3. Send a preflight from an unrelated Origin. It must not get a matching Allow-Origin header or credential permission.
4. Refresh each React destination, load a saved pet photo, and confirm missing JavaScript returns 404 rather than HTML. Verify WSS if enabled.

These deployed checks remain a release handoff until real hostnames, certificates and infrastructure are supplied.

References: [Vite environment and modes](https://vite.dev/guide/env-and-mode), [Vite production deployment](https://vite.dev/guide/build), [Nginx try_files](https://nginx.org/en/docs/http/ngx_http_core_module.html#try_files), [Laravel configuration caching](https://github.com/laravel/docs/blob/13.x/configuration.md).
