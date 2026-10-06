# Messaging API contract

Issue #124 defines the initial HTTP read contract for Messaging.

All identity fields below are numeric database IDs. User IDs and Pet IDs are never interchangeable.

## Conversation list

### Request

```http
GET /api/v.0/conversations?page=1&per_page=20
Authorization: Bearer <token>
Accept: application/json
```

- `page` is optional, minimum 1.
- `per_page` is optional, 1–50, default 20.
- The authenticated User is always the subject. Client `user_id` is prohibited.

### Ordering

Conversations are ordered by their latest active message timestamp descending, then conversation ID descending. Conversations without messages sort after conversations with messages.

### Response item

```json
{
  "id": 42,
  "current_user_id": 10,
  "participants": [
    {
      "user_id": 10,
      "name": "Current owner",
      "pet": {
        "id": 100,
        "user_id": 10,
        "name": "Nala",
        "species_id": 1,
        "race": {
          "id": 5,
          "species_id": 1,
          "name": "Labrador Retriever"
        },
        "age_years": 3,
        "sex": 1,
        "images": ["pets/nala.jpg"]
      }
    }
  ],
  "last_message": {
    "id": 900,
    "conversation_id": 42,
    "sender_user_id": 11,
    "receiver_user_id": 10,
    "content": "See you Saturday",
    "is_seen": false,
    "created_at": "2026-10-05T21:30:00.000000Z",
    "updated_at": "2026-10-05T21:30:00.000000Z"
  },
  "unread_count": 1
}
```

`participants` contains both human accounts. Their current Pet is nested separately so the frontend can render pet identities without confusing Pet IDs with User IDs.

`unread_count` counts active messages in that conversation where the authenticated User is the receiver and `is_seen = false`.

## Message thread

### Request

```http
GET /api/v.0/messages?receiver_id=11&page=1&per_page=30
Authorization: Bearer <token>
Accept: application/json
```

- `receiver_id` is a User ID.
- The sender/current User is derived from authentication. Client `sender_id` is prohibited.
- `page` is optional, minimum 1.
- `per_page` is optional, 1–50, default 30.
- Issue #123 contact rules still apply: the two accounts need an active reciprocal Match and no active block.

### Pagination and ordering

Pagination selects messages newest-first so page 1 is the most recent window.

Within each returned page, `data` is sorted oldest-to-newest. This lets the chat UI render the page directly in chronological order. Loading page 2 retrieves the preceding older window, which should be prepended by the client.

Both directions are included:

- current User → receiver;
- receiver → current User.

### Response item

```json
{
  "id": 900,
  "conversation_id": 42,
  "sender_user_id": 11,
  "receiver_user_id": 10,
  "content": "See you Saturday",
  "is_seen": false,
  "created_at": "2026-10-05T21:30:00.000000Z",
  "updated_at": "2026-10-05T21:30:00.000000Z"
}
```

Timestamps are machine-readable ISO 8601 values. Relative labels such as “Yesterday” are frontend presentation concerns.

## Pagination envelope

Both endpoints use the standard PetMingle paginated envelope:

```json
{
  "success": true,
  "message": "...",
  "data": [],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 20,
    "total": 0
  },
  "links": {
    "first": "...",
    "last": "...",
    "prev": null,
    "next": null
  }
}
```

## Seen/unread boundary

Issue #127 adds an explicit receiver-authorized mutation:

```http
PUT /api/v.0/conversations/{conversationId}/seen
Authorization: Bearer <token>
Accept: application/json
```

The request has no body. The authenticated User must be a participant in the active conversation and must still satisfy the Issue #123 contact policy.

Only active messages in that conversation where the authenticated User is `receiver_id` and `is_seen = false` are updated. Outgoing messages and messages in other conversations are never changed by this mutation.

Response:

```json
{
  "success": true,
  "message": "Conversation marked as seen.",
  "data": {
    "conversation_id": 42,
    "marked_count": 3,
    "unread_count": 0
  }
}
```

The frontend removes an unread badge only after this server response. Refresh then reloads `unread_count` from `GET /conversations`; there is no client-only unread source of truth.

Thread messages continue to expose `is_seen`, and conversation summaries continue to expose receiver-scoped `unread_count`.

## Realtime extension

Issue #128 adds private Pusher-compatible delivery for new messages, matches and typing while keeping every HTTP contract above authoritative.

See [REALTIME_MESSAGING.md](REALTIME_MESSAGING.md) for channel authorization, event payloads, reconnect behavior and local Soketi configuration.

## Out of scope

This contract does not add:

- conversation uniqueness migration;
- playdate/interests persistence;
- frontend fixture replacement.

Those remain separate tickets.
