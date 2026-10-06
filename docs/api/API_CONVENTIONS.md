# PetMingle API conventions

This document defines the HTTP and JSON conventions used by the current PetMingle API.

## Success envelope

Successful API responses that return application data use:

```json
{
  "success": true,
  "message": "Human-readable message.",
  "data": {}
}
```

Collection responses use the same envelope.

## Paginated responses

Paginated collections use:

```json
{
  "success": true,
  "message": "Human-readable message.",
  "data": [],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 10,
    "total": 0
  },
  "links": {
    "first": null,
    "last": null,
    "prev": null,
    "next": null
  }
}
```

## Error responses

General errors use:

```json
{
  "success": false,
  "message": "Resource not found."
}
```

Validation errors use:

```json
{
  "success": false,
  "message": "Validation failed.",
  "errors": {
    "field": [
      "Validation message."
    ]
  }
}
```

Raw exception messages and internal implementation details must not be exposed.

## HTTP status codes

| Outcome | Status |
| --- | --- |
| Successful read | `200 OK` |
| Successful update | `200 OK` |
| Successful delete | `200 OK` |
| Successful restore | `200 OK` |
| Successful resource creation | `201 Created` |
| Unauthenticated | `401 Unauthorized` |
| Forbidden | `403 Forbidden` |
| Missing resource | `404 Not Found` |
| Validation failure | `422 Unprocessable Entity` |
| Rate limited | `429 Too Many Requests` |
| Unexpected server failure | `500 Internal Server Error` |

Delete operations currently return a JSON confirmation envelope, therefore they intentionally use `200` rather than `204`.

## Resource serialization

Frontend-facing resources should use explicit Laravel API Resources rather than exposing arbitrary Eloquent serialization.

Current explicit resources include:

- Species
- Race
- Pet
- Location
- Like
- Dislike
- Match
- Block

This keeps public fields explicit and prevents database implementation details from becoming accidental API contracts.

## Identity semantics

PetMingle contains separate User and Pet identities.

- `User.id` identifies a human account.
- `Pet.id` identifies a pet.
- `Pet.user_id` references its owning User.
- Likes, dislikes and matches use Pet IDs.
- Blocks use User IDs.
- Messages and conversations use User IDs.

Frontend code must not substitute User IDs for Pet IDs or infer one from the other.

## Authentication

Protected API requests use Laravel Sanctum personal access tokens:

```http
Authorization: Bearer <token>
Accept: application/json
```

`GET /me` is the authoritative source for the authenticated User and current nullable Pet identity.

## Domain contracts

Current Discovery, media, relationships and Messaging contracts are indexed in [the API guide](README.md). Consult these contracts for endpoint-specific behavior and authentication/send response exceptions.
