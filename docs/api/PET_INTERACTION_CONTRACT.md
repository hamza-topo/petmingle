# Pet interaction contract

Authenticated Discovery interactions use **Pet IDs**, never User IDs.

## Like

```text
POST /api/v.0/likes
```

Request:

```json
{
  "to_pet_id": 42
}
```

The source Pet ID is derived from the authenticated account's current pet.

Response:

```json
{
  "success": true,
  "message": "Like processed.",
  "data": {
    "id": 15,
    "from_pet_id": 8,
    "to_pet_id": 42,
    "interaction": "liked"
  }
}
```

## Dislike

```text
POST /api/v.0/dislikes
```

Uses the same request body and returns `interaction: "disliked"`.

## Semantics

- `from` and `to` request fields are prohibited.
- A pet cannot interact with itself.
- Target pets must exist and be active.
- Repeating the same active interaction is idempotent.
- Liking a currently disliked pet removes the active dislike.
- Disliking a currently liked pet removes the active like and any persisted match pair through the existing mismatch path.
- Authorization/block/contact rules beyond this interaction contract remain handled by the dedicated relationship-policy issues.

## Discovery state

Nearby Discovery responses include:

```json
{
  "interaction": null
}
```

or `"liked"` / `"disliked"` for the authenticated pet.

The frontend uses this persisted state on load/refresh and changes local state only after a successful mutation response.
