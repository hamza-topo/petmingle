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
| `image` | no | file | One JPG/PNG image, maximum 10 MB |
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

The legacy `images` request field is not accepted for creation. The response continues to expose `images` as an array because persisted pet media is represented as a collection:

```json
{
  "images": []
}
```

or:

```json
{
  "images": ["uploads/example.png"]
}
```

Filename generation, replacement/deletion rules, and storage hardening are tracked separately by issue #115.

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
