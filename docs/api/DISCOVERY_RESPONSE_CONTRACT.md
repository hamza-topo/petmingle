# Discovery response contract

Endpoint:

```text
POST /api/v.0/locations/nears
```

Authentication: Laravel Sanctum.

This is the canonical read contract for nearby Discovery cards. Security and visibility rules are defined in `NEARBY_DISCOVERY_SECURITY.md`.

## Request

Supported fields:

| Field | Required | Rule | Default |
| --- | --- | --- | --- |
| `radius_km` | no | integer 1–100 | 5 |
| `species_id` | no | existing active Species ID | all species |
| `race_id` | no | existing active Race ID; when `species_id` is supplied, the race must belong to that species | all races |
| `page` | no | integer >= 1 | 1 |
| `per_page` | no | integer 1–50 | 24 |

Requester identity and origin coordinates are server-owned and are never accepted from the client.

The supported taxonomy filters are explicit top-level fields. Legacy nested `filters` payloads remain prohibited.

## Success envelope

HTTP 200:

```json
{
  "success": true,
  "message": "Nearby pets.",
  "data": [
    {
      "owner": {
        "id": 7,
        "name": "Sarah"
      },
      "pet": {
        "id": 42,
        "owner_id": 7,
        "species_id": 3,
        "name": "Milo",
        "age_years": 4,
        "sex": 1,
        "race": {
          "id": 9,
          "species_id": 3,
          "name": "Labrador Retriever"
        },
        "images": [
          "pets/example.jpg"
        ],
        "about": "Friendly and curious."
      },
      "distance_km": 1.24,
      "is_new": true,
      "interaction": null
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 24,
    "total": 1
  },
  "links": {
    "first": "…",
    "last": "…",
    "prev": null,
    "next": null
  }
}
```

IDs in this example are illustrative. The contract guarantees their domains:

- `owner.id`: User/account ID
- `pet.id`: Pet ID
- `pet.owner_id`: User/account ID and must equal `owner.id`
- `pet.species_id`: Species ID
- `pet.race.id`: Race ID
- `pet.race.species_id`: Species ID

## Card fields

The API exposes only persisted or deterministically derived fields needed for supported Discovery cards:

- owner ID and name
- pet ID and owner ID
- species ID
- pet name
- age in years
- sex as legacy integer `0`, `1`, or `null`
- race ID, species ID, and race name
- persisted pet image paths
- persisted biography/about text
- numeric distance in kilometers
- creation-recency flag `is_new`
- authenticated pet relationship state: `null`, `liked`, or `disliked`

Image paths are storage paths, not browser URLs. The frontend must use the existing media URL adapter.

`distance_km` is numeric and rounded to two decimal places. It is not a formatted string and contains no unit suffix.

## Interaction state

`interaction` reflects the active persisted relationship from the authenticated pet to each target pet.

- `null`: no active like/dislike
- `liked`: active like
- `disliked`: active dislike

The field uses Pet-ID semantics and is intended to restore Discovery controls after refresh. Mutation details are defined in `PET_INTERACTION_CONTRACT.md`.

## Supported filtering semantics

- `radius_km` limits candidates by server-calculated distance.
- `species_id` limits candidates to pets persisted with that Species ID.
- `race_id` limits candidates to pets persisted with that Race ID.
- When both taxonomy IDs are supplied, validation rejects a race that does not belong to the supplied species.
- Filters are applied before pagination, so `meta.total` describes the filtered result set.

## Explicitly unsupported fields and filters

The response does **not** fabricate:

- pet verification
- personality traits
- energy/size metadata
- companion pets
- city/address labels
- target latitude/longitude
- profile view/favorite statistics
- featured ranking

Size, energy and personality filters are not part of this API contract because those values do not have approved persistence semantics.

## Pagination

Results are ordered by:

1. ascending distance;
2. ascending persisted location ID as a deterministic tie-break.

An empty result uses the same envelope:

```json
{
  "success": true,
  "message": "Nearby pets.",
  "data": [],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 24,
    "total": 0
  },
  "links": {
    "first": "…",
    "last": "…",
    "prev": null,
    "next": null
  }
}
```

Clients should use `meta` for paging state. Pagination URLs are convenience links; the request method remains POST.

## Errors

Validation/precondition failures use the global HTTP 422 contract:

```json
{
  "success": false,
  "message": "Validation failed.",
  "errors": {
    "field": ["Message"]
  }
}
```

Authentication and other global API errors retain the standard API error envelope.
