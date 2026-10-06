# Dependency and asset audit

Completed for #91 against the Laravel 13 / React integration.

## Composer runtime dependencies

| Dependency | Current use |
| --- | --- |
| PHP ^8.3 | Framework minimum; development and CI containers run PHP 8.4. |
| guzzlehttp/guzzle | Pusher-compatible HTTP broadcasting transport and HTTP integrations. |
| jeroennoten/laravel-adminlte | Admin Blade layouts, plugins and published assets. |
| laravel/framework | Laravel runtime, database, queues, mail and routing. |
| laravel/sanctum | Bearer authentication and private broadcast authorization. |
| laravel/socialite | Provider login adapters. |
| laravel/tinker | Maintainer interactive console for the existing Laravel application. |
| laravel/ui | Auth::routes for the admin authentication routes. |
| spatie/laravel-sitemap | SiteMap command and AutoSiteMapEvent generation. |

The unused waleedahmad/pinterest-laravel API SDK, its provider registration and SDK configuration were removed. The website's Pinterest sharing widget and external links are independent of that SDK and remain in the static Blade site.

## Composer development dependencies

| Dependency | Current use |
| --- | --- |
| fakerphp/faker | Factories and database seed data. |
| mockery/mockery | Framework mocks and transport/failure tests. |
| nunomaduro/collision | Artisan test runner and CLI diagnostics. |
| phpunit/phpunit | Backend unit and feature suite. |
| spatie/laravel-ignition | Development error diagnostics. |
| laravel/pint | Formatting gate. |
| larastan/larastan | Static analysis gate and tracked legacy baseline. |

Sail was removed because this repository uses its own docker/php image and docker-compose.yml. Its sole unshared dependency, symfony/yaml, was also removed from the lockfile. Versions of retained dependencies were preserved.

## Frontend and Blade assets

- React, TypeScript and Vite in frontend/ are the actively maintained application. Its pinned lockfile is used by npm ci, CI and the deployment Dockerfile.
- Root npm commands forward to frontend/. The root package has no third-party dependencies; run npm run frontend:install before invoking root scripts.
- Vue's example component had no import, mount or Blade usage. Vue 2, Vue loaders, Laravel Mix, the root Sass build and their generated public/js/app.js, public/css/app.css and mix-manifest.json were removed.
- All application PHP/configuration and Blade views were checked for asset consumers. Public Blade pages load public/assets directly; admin pages load public/vendor assets directly. AdminLTE's generic bundled-asset template branches remain inactive through laravel_asset_bundling=false.
- Published third-party assets under public/assets and public/vendor remain because public/admin pages consume them. No replacement CSS reset or compiled React asset is injected into Blade.
- The optional frontend Compose profile uses Node 24 and serves Vite on host loopback port 5174. Root Mix watching and the Node 16 development service are retired.

## Reproduce checks in Docker

From the repository root:

```sh
cp frontend/.env.example frontend/.env.local
docker compose --profile frontend up -d node
docker compose run --rm node npm run typecheck
docker compose run --rm node npm test -- --maxWorkers=1
docker compose exec -u www-data app composer validate --strict
docker compose exec -u www-data app composer quality
docker compose exec -u www-data app php artisan test
docker compose exec -u www-data app composer audit
```

CI checks the root forwarding manifest, installs and typechecks using the Compose Node service, renders Blade asset consumers, and retains the complete Laravel, React, desktop and Docker/Nginx hosting checks. Production build configuration remains documented in docs/deployment/FRONTEND_PRODUCTION.md.
