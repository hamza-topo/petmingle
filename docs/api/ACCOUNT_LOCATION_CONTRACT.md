# Account location contract

PetMingle stores account locations as coordinates only.

## Ownership

Locations belong to the authenticated user through `locations.user_id`.

- `GET /api/v.0/locations` returns only the authenticated account's records.
- `POST /api/v.0/locations` ignores any client-supplied `user_id` and assigns the authenticated account.
- `PUT /api/v.0/locations/{id}` is owner-only through `LocationPolicy`.
- Cross-account view, update, delete, and restore operations are forbidden.

## Current location

Legacy data can contain more than one location for an account.

The frontend defines the current location deterministically as:

1. load the authenticated account locations;
2. order them by descending persisted ID;
3. choose the newest record that contains usable latitude and longitude.

Once a current location exists, explicit edits update that record rather than creating additional records.

If no usable location exists, the frontend shows **Location not set** and the first explicit save creates a new record.

## Coordinates

Supported fields:

| Field | Rule |
| --- | --- |
| `latitude` | required numeric value between -90 and 90 |
| `longitude` | required numeric value between -180 and 180 |

No city, region, postal code, address, reverse-geocoded label, or inferred place name is part of this contract.

The React frontend therefore displays coordinates directly, for example:

```text
31.629500, -7.981100
```

## User action boundary

The frontend does not automatically read browser/device geolocation.

Coordinates are persisted only after the authenticated user explicitly opens the account-location editor and submits latitude and longitude.

This keeps #116 independent from any future browser permission or geocoding product decision.

## Discovery dependency

The current coordinates are the account-location prerequisite for later real Discovery work.

Issue #116 does not claim that the existing fixture pet results were produced from those coordinates. Nearby-query normalization remains in the Discovery milestone.
