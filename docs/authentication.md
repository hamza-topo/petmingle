# API Authentication

PetMingle API authentication is handled by Laravel Sanctum.

## Authentication flow

Clients authenticate using:

`POST /api/v.0/sign-in`

A successful authentication returns a personal access token.

Authenticated API requests must include the token using the Bearer authentication scheme:

`Authorization: Bearer <token>`

Protected routes use the `auth:sanctum` middleware.

## Logout

`POST /api/v.0/sign-out`

Logout revokes the access token used for the current request.

## Token storage

Sanctum personal access tokens are stored in the `personal_access_tokens` table.

The API no longer uses the legacy JWT middleware or Tymon JWTAuth integration.