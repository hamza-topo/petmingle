# PetMingle API guide

Base URL: `http://localhost:8000/api/v.0` in the default Docker environment. Replace the origin for your environment; preserve the literal `v.0` path.

## Start with Postman

Import [the core collection](petmingle.postman_collection.json) into Postman. Set `email` and `password` for an existing local account, run Sign in, and copy the returned `token` into the collection variable. Requests inherit Bearer authentication except Sign in. Credentials and tokens are blank in the committed collection. Do not export populated secrets.

Run Current identity to obtain the account ID and nullable current Pet ID. Set the collection IDs from actual records: species/race from taxonomy, target Pet from Discovery, receiver User from a match owner, conversation from the conversation list. Example IDs in linked documents are illustrative.

Use a disposable local database for creates, likes, dislikes and seen mutations. Requests do not automatically chain mutations. Sign out revokes the current token. This collection covers the core integration flow, not every legacy resource route, social login, administrator action or multipart media request.

## Authentication example

```bash
curl -X POST http://localhost:8000/api/v.0/sign-in \
  -H 'Accept: application/json' -H 'Content-Type: application/json' \
  -d '{"email":"your-local-account@example.test","password":"your-local-password"}'

curl http://localhost:8000/api/v.0/me \
  -H 'Accept: application/json' -H 'Authorization: Bearer <token>'
```

Sign in returns HTTP 200 with `{"success":true,"token":"<token>","token_type":"Bearer"}`. Invalid credentials return 401. Most data responses use `success`, `message`, `data`; sign in and sign out are exceptions. Read [authentication](../authentication.md) and [conventions](API_CONVENTIONS.md) for errors, pagination and identity domains.

## Supported domain contracts

| Flow | Contract |
| --- | --- |
| Create pet, optional single image | [Pet creation](PET_CREATION_CONTRACT.md) |
| Replace/remove media and storage paths | [Media](PET_MEDIA_STORAGE.md) |
| Own coordinates | [Account location](ACCOUNT_LOCATION_CONTRACT.md) |
| Nearby cards, radius and taxonomy filters | [Discovery](DISCOVERY_RESPONSE_CONTRACT.md), [visibility](NEARBY_DISCOVERY_SECURITY.md) |
| Like/dislike | [Interactions](PET_INTERACTION_CONTRACT.md) |
| Blocks and reciprocal contact permission | [Contact policy](BLOCK_CONTACT_POLICY.md) |
| Conversations, thread pagination, seen state | [Messaging](MESSAGING_CONTRACT.md) |
| Private events and reconnect | [Realtime](REALTIME_MESSAGING.md) |
| Supported profile counts | [Statistics](PET_PROFILE_STATISTICS.md) |

## Matching and messaging walkthrough

1. Authenticate, read `GET /me`, create a pet if needed, and explicitly save coordinates.
2. Read `POST /locations/nears` with optional radius/taxonomy fields.
3. Send `POST /likes` with `{"to_pet_id":42}`. Likes use Pet IDs; source identity is server-derived. A reverse like triggers transactional reciprocal match creation.
4. Read `GET /matches`. Messaging uses the counterpart's **User ID**, never its Pet ID.
5. Send `POST /messages` with `{"receiver_id":11,"content":"Hello"}`. Sender and conversation are server-derived. Current send success is HTTP 200 with a raw message under `data`; read serialization uses explicit message fields.
6. Load conversations and the bidirectional thread. Page 1 selects the newest window, returned oldest-to-newest within that page; prepend older pages.
7. Mark seen with `PUT /conversations/{id}/seen`; only incoming messages are updated. Refetch after reconnect. HTTP is authoritative.

Active reciprocal matches and no active block are required for thread reads/sends. Blocking prevents contact in either direction and soft-deletes associated relationship/contact records. There is no public unblock endpoint.

## Maintenance and validation

Update this guide, collection and domain contract whenever routes, payloads, status codes or authorization change. Domain contracts and current source supersede the historical audit in FRONTEND_API_CONTRACT.md and API_GAPS.md. Resource route registration alone does not promise a usable product endpoint.

Validate route registration and behavior through Docker:

```bash
docker compose exec -u www-data app php artisan route:list --path=api
docker compose exec -u www-data app php artisan test
docker compose exec -u www-data app composer quality
```

The Postman collection is manually runnable documentation; it is not a CI integration test suite. [Architecture and ADRs](../architecture/README.md) explain the implementation boundaries.
