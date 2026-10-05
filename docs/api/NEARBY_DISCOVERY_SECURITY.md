# Nearby discovery security contract

Endpoint:

```text
POST /api/v.0/locations/nears
```

Authentication: Laravel Sanctum.

The legacy `POST /api/v.0/locations/filters` route is subject to the same nearby security boundary. Product filter semantics remain deferred to the Discovery filter work.

## Request contract

The client may supply only:

| Field | Required | Rule |
| --- | --- | --- |
| `radius_km` | no | integer from 1 through 100; defaults to 5 |

The following legacy fields are rejected:

- `user_id`
- `latitude`
- `longitude`
- `perimetre`
- legacy nested `filters`

Requester identity comes from the authenticated Sanctum user.

Discovery origin comes from the authenticated account's newest active location that has both latitude and longitude.

## Preconditions

Nearby discovery requires:

1. an authenticated account;
2. an active pet owned by that account;
3. at least one active account location with usable coordinates.

A missing pet or usable location returns the standard HTTP 422 validation envelope.

A newer location row with null coordinates does not mask an older usable location.

## Visibility rules

A nearby result is visible only when all of the following are true:

- it belongs to another user;
- the user is not blocked in either direction;
- the user exists and is not soft-deleted;
- the user has an active pet;
- that pet has an active race record required by the current discovery resource;
- the account has a usable active location;
- only that account's newest usable location participates;
- calculated distance is within the validated radius.

This issue does not define like/dislike/match visibility semantics beyond block exclusion. Those relationship rules remain tracked by their dedicated Discovery/interaction issues.

## Distance query

The Haversine calculation keeps latitude, longitude, and radius as bound SQL parameters.

The cosine input is clamped to the `[-1, 1]` domain before `acos` to avoid floating-point edge failures for identical or nearly identical coordinates.

## Product filters

The previous nested filter path mixed user-controlled coordinates with unvalidated filter input and a broken legacy filter implementation.

Until the normalized Discovery filter contract is implemented, nested legacy filters are rejected instead of silently applying unsafe or unreliable behavior.
