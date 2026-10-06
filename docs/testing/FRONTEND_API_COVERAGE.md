# Frontend API coverage

## Network boundary

`frontend/src/api/client.test.ts` exercises the real transport with simulated fetch responses: Bearer/JSON headers, multipart boundary ownership, cancellation forwarding, HTTP errors, legacy HTTP-200 error envelopes, non-JSON gateway errors, offline failures and missing configuration.

`frontend/src/api/adapters.integration.test.ts` keeps the adapters and transport real, simulating only fetch and deterministic URL configuration. It covers password sign-in/identity/logout, taxonomy/profile hydration, multipart creation, empty discovery/relationships, interaction/message/seen persistence, identity mismatches and malformed collections. Twelve core adapter operations each exercise 403, 422 and offline failure propagation.

Existing per-domain adapter tests and page/provider tests remain complementary. ProtectedRoute, PetRequiredRoute, AuthProvider, SignInPage and feature-page suites cover authenticated UI states and user actions.

## CI

The frontend job runs locked installation, TypeScript checks, the entire Vitest suite with one worker and the production build independently of Laravel/MySQL. No test requires a running backend.

Docker local commands:

```bash
docker exec petmingle-frontend npm run typecheck
docker exec petmingle-frontend npm test -- --maxWorkers=1
docker exec petmingle-frontend npm run build
```

## Remaining gaps

- Fetch is simulated: browser CORS, real server transport and deployment require separate verification.
- Page/provider tests commonly mock adapters. This suite does not constitute a complete browser end-to-end sign-in-to-message journey.
- Not every adapter validates every successful field at runtime. Malformed identity/authentication envelopes and schema completeness remain hardening work; current tests do not claim universal schema validation.
- Realtime has dedicated mocked transport tests, not live websocket-provider delivery.
- Visual regression and accessibility are tracked separately in #134 and #133.

Keep fixtures aligned with the current documented API and add network-boundary regression cases when a transport or contract bug is fixed.
