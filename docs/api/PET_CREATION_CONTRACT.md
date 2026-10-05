# Pet creation API contract

Endpoint: `POST /api/v.0/pets`

Authentication: Laravel Sanctum bearer token.

Content type: `multipart/form-data` when an image is supplied. JSON is valid when no image is supplied.

## Supported request fields

| Field | Required | Type | Rules |
| --- | --- | --- | --- |
| `species_id` | yes | integer | Must reference an existing species |
| `race_id` | yes | integer | Must reference an existing race belonging to `species_id` |
| `name` | yes | string | Trimmed, 1–25 characters |
| `age` | yes | integer | 0–30 years |
| `image` | no | file | Valid JPG/JPEG/PNG, maximum 10 MB |
| `sexe` | no | integer/null | Legacy field; `0` or `1` |
| `color` | no | string/null | Legacy field; maximum 15 characters |
| `about` | no | string/null | Legacy field |

`user_id` is never trusted from the request. The created pet is always assigned to the authenticated account.

## React-only fields

The current React form also collects:

- `size`
- `traits`
- `energy`
- `playdate`

The Pet model has no approved persistence model for these values yet. They must not be fabricated into existing columns. If sent to the pet creation endpoint they are rejected with HTTP 422.

## Image shape

Creation accepts **one optional upload** using the `image` field.

The legacy `images` request field is not accepted for creation. Persisted media is returned as an array:

```json
{
  "images": []
}
```

or:

```json
{
  "images": ["pets/550e8400-e29b-41d4-a716-446655440000.jpg"]
}
```

Stored filenames are generated server-side and do not reuse the client filename. Full validation, visibility, replacement, and removal behavior is documented in `docs/api/PET_MEDIA_STORAGE.md`.

## Successful response

HTTP 201:

```json
{
  "success": true,
  "message": "Pet has been created.",
  "data": {
    "id": 42,
    "user_id": 10,
    "species_id": 3,
    "race_id": 7,
    "name": "Milo",
    "age": 4,
    "sexe": null,
    "color": null,
    "images": [],
    "about": null
  }
}
```

## Validation response

HTTP 422 uses the global API error contract:

```json
{
  "success": false,
  "message": "Validation failed.",
  "errors": {
    "race_id": [
      "The selected race does not belong to the selected species."
    ]
  }
}
```

Raw framework exceptions are not part of the public response contract.
