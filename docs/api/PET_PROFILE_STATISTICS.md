# Pet profile statistics

Endpoint:

```text
GET /api/v.0/pets/{petId}/statistics
```

Authentication: Laravel Sanctum.

The endpoint is owner-only through `PetPolicy::view`.

## Supported counters

The current profile exposes only statistics backed by persisted domain data:

| Field | Meaning |
| --- | --- |
| `matches` | Active match rows where the current pet is the `from` pet |
| `likes_sent` | Active like rows sent by the current pet |

A reciprocal match is stored as a second row with the other pet in `from`, so it does not double the current pet's match count.

Soft-deleted relationship rows are excluded by the normal Eloquent relations.

## Response

```json
{
  "success": true,
  "message": "Pet profile statistics.",
  "data": {
    "matches": 3,
    "likes_sent": 5
  }
}
```

A pet with no persisted relationships returns zeroes.

## Unsupported counters

The previous UI fixture displayed:

- Profile views
- Favorites

Neither has an approved persisted backend contract. They are removed from the authenticated profile instead of displaying fabricated values.

Future analytics or inbound-like features must define their own persistence and privacy semantics before being presented as profile statistics.
