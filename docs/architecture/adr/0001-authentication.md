# Sanctum Bearer authentication

Status: Accepted — records the current implementation.

Date: 2026-10-06

## Context

Clients need a coherent account identity and revocable API credential.

## Decision

Use Sanctum personal access tokens for protected API routes with auth:sanctum. Sign in returns a Bearer token; /me supplies the authoritative User and nullable Pet; sign out deletes the current access token.

## Consequences and limits

User and Pet IDs remain distinct. Clients handle 401 and a missing pet explicitly. Social provider routes exist but are not a substitute for the documented password Bearer flow.

## Evidence

- `app/Http/Controllers/Api/AuthController.php`
- `routes/api.php`
- `docs/authentication.md`
- `tests/Feature/Api/AuthenticatedIdentityTest.php`
