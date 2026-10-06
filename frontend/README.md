# PetMingle React frontend

React/TypeScript/Vite desktop app integrated with Laravel's Sanctum Bearer API. Authentication, pet creation/editing/media, account location, Discovery filters/interactions, relationships and Messaging use API adapters. Landing example profiles and missing-artwork placeholders remain intentional; billing and other deferred controls are unavailable.

## Local Docker workflow

Use the existing project containers for Laravel and the frontend. Copy frontend/.env.example to the frontend's local environment file and use the browser-facing API URL, normally http://localhost:8000/api/v.0. Docker service names are not browser URLs. The development frontend uses port 5174 on the host (5173 in the container); local standalone Vite uses port 5173. Keep the matching exact origins in backend CORS_ALLOWED_ORIGINS.

With Node 24 inside a container and frontend as its working directory:

```sh
npm ci
npm run dev -- --host 0.0.0.0
npm run typecheck
npm test -- --maxWorkers=1
```

For the local browser review build, use the desktop-review mode with the loopback API/media URLs documented in [the desktop review](../docs/ui/DESKTOP_REGRESSION_REVIEW.md). A normal npm run build now requires a deployment HTTPS API URL.

## Production

Read [FRONTEND_PRODUCTION.md](../docs/deployment/FRONTEND_PRODUCTION.md) for the public build settings, Docker/Nginx hosting, exact HTTPS CORS origins, private realtime configuration and deployment verification. Copy .env.production.example to .env.production.local, set the real browser URLs, then build. Values prefixed VITE_ are public and embedded at build time; never put tokens or server secrets there.

The production Dockerfile builds the bundle and serves it using the supplied Nginx SPA fallback. Direct navigation/refresh is checked in CI. Supported desktop widths are 1448, 1280 and 1120px. Mobile behavior and original photographic/brand assets remain separate work.

## Contracts and quality

- [API contract](../docs/api/FRONTEND_API_CONTRACT.md) and [API documentation](../docs/api/README.md).
- [Desktop regression review](../docs/ui/DESKTOP_REGRESSION_REVIEW.md).
- [Product behavior and deferred features](../docs/ui/PRODUCT_BEHAVIOR_REVIEW.md).
- [Accessibility review](../docs/ui/ACCESSIBILITY_REVIEW.md).

CI runs TypeScript, Vitest integration/persistence tests, public build configuration checks, production build, Chromium desktop captures and Nginx hosting checks alongside Laravel's test/quality gates. Historical Cycle documents describe earlier prototypes; they do not override current implemented behavior.
