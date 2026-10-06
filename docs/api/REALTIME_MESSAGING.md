# Private realtime Messaging

Issue #128 enables optional private realtime delivery on top of the existing HTTP Messaging contracts.

Realtime is an enhancement, not a persistence boundary. HTTP remains authoritative for conversation lists, threads, sends and seen state.

## Transport

PetMingle uses the Pusher Channels protocol.

The backend includes a dependency-free Pusher-compatible broadcaster backed by the Guzzle client already present in Laravel. The frontend uses the browser WebSocket API directly with Pusher protocol v7.

Supported deployments:

- Pusher Cloud;
- a Pusher-compatible server such as Soketi.

Local Docker includes an optional Soketi service on port `6001`.

## Private channel

Every authenticated account subscribes to exactly one application channel:

```text
private-App.Models.User.{authenticatedUserId}
```

Authorization is performed through:

```http
POST /broadcasting/auth
Authorization: Bearer <token>
Content-Type: application/x-www-form-urlencoded

socket_id=123.456&channel_name=private-App.Models.User.10
```

Laravel derives the authenticated User from Sanctum and `routes/channels.php` authorizes only the matching User ID.

A client cannot choose another account's private channel successfully.

## Events

### New message

Event:

```text
new.message
```

Recipients:

- sender account;
- receiver account.

Payload:

```json
{
  "message": {
    "id": 77,
    "conversation_id": 101,
    "sender_id": 20,
    "receiver_id": 10,
    "content": "Hello",
    "is_seen": false,
    "created_at": "2026-10-06T12:30:00.000000Z",
    "updated_at": "2026-10-06T12:30:00.000000Z",
    "deleted_at": null
  }
}
```

React validates the payload and deduplicates by persisted message ID. This protects against:

- the same event being replayed;
- HTTP send success racing with the sender's own websocket event;
- reconnect delivery overlaps.

For an inactive conversation, an incoming event increments only that conversation's unread badge.

For the active conversation, React appends the message and uses the persisted Issue #127 seen endpoint.

If the event references a conversation not currently loaded, React refetches the authoritative conversation list.

### New match

Event:

```text
new.match
```

React validates the expected match envelope and refetches authoritative conversation state. No match record is fabricated locally.

### Typing

Typing publication uses:

```http
POST /api/v.0/messages/typing
Authorization: Bearer <token>
Content-Type: application/json

{
  "receiver_id": 20,
  "is_writing": true
}
```

The sender ID is never accepted from the browser. Laravel derives it from authentication and reuses the Issue #123 active Match / Block contact policy.

Private event:

```text
is-writing-to
```

Payload:

```json
{
  "sender_user_id": 10,
  "receiver_user_id": 20,
  "is_writing": true
}
```

Typing is best-effort and ephemeral. The receiver expires a positive typing state after three seconds if a stop event is missed.

## Reconnection

The browser reconnects with bounded exponential backoff:

```text
1s -> 2s -> 4s -> 8s -> 10s maximum
```

After a successful resubscription, React refetches:

1. conversation summaries;
2. the currently active thread.

This closes gaps from events missed while offline.

HTTP state wins after reconnect.

## Failure behavior

Realtime failure must not break persisted actions.

Backend broadcast events implement Laravel's rescue behavior, so an unavailable realtime transport does not turn a successful message or match write into an HTTP failure.

Frontend behavior:

- missing realtime configuration -> realtime disabled, HTTP remains available;
- websocket outage -> reconnect in background, HTTP remains available;
- private-channel 401/403 -> fail closed and stop reconnect attempts;
- malformed event payload -> ignore it;
- typing failure -> do not block sending.

## Local configuration

Backend `.env`:

```env
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=petmingle
PUSHER_APP_KEY=petmingle-key
PUSHER_APP_SECRET=petmingle-secret
PUSHER_APP_CLUSTER=mt1
PUSHER_HOST=soketi
PUSHER_PORT=6001
PUSHER_SCHEME=http
FORWARD_PUSHER_PORT=6001
```

Frontend environment:

```env
VITE_REALTIME_ENABLED=true
VITE_PUSHER_APP_KEY=petmingle-key
VITE_PUSHER_APP_CLUSTER=mt1
VITE_PUSHER_WS_HOST=localhost
VITE_PUSHER_WS_PORT=6001
VITE_PUSHER_WS_SCHEME=ws
```

For Pusher Cloud:

- use the real app ID/key/secret and cluster;
- omit `PUSHER_HOST` / `PUSHER_PORT`;
- use HTTPS/WSS;
- omit the frontend websocket host/port so the cluster endpoint is derived.

Secrets remain backend-only. Only the Pusher app key is browser-visible.

## Local startup

After updating environment values:

```bash
docker compose up -d --force-recreate app nginx soketi
docker exec petmingle-app php artisan optimize:clear
```

The existing frontend container must also be recreated when its Vite environment changes.

## Test boundary

Automated tests never contact Pusher or Soketi.

`phpunit.xml` forces `BROADCAST_DRIVER=null` for the ordinary suite. Dedicated broadcaster tests use a simulated Guzzle transport and verify:

- private auth signature;
- channel authorization;
- outbound event signing/body;
- typing contact rules.

Frontend tests use simulated WebSocket/auth dependencies and cover:

- private subscription;
- unauthorized fail-closed behavior;
- reconnect/resync;
- event deduplication;
- message, match and typing UI updates.
