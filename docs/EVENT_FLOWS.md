# Event and Observer Flows

PetMingle uses Eloquent observers for model lifecycle reactions and application events for broadcasting application state changes.

## Observer registration

All Eloquent observers are registered centrally in `EventServiceProvider`.

Current registrations:

- `Like` → `LikeObserver`
- `Block` → `BlockObserver`
- `Message` → `MessageObserver`
- `User` → `UserObserver`
- `Adoption` → `AdoptionObserver`
- `Blog` → `BlogObserver`
- `Species` → `SpeciesObserver`

Model classes are not registered through event-listener mappings.

## Transaction rule

Observers that trigger external or asynchronous side effects implement
`ShouldHandleEventsAfterCommit`.

This prevents notifications, emails, broadcasts, and cache invalidation from
running for database changes that are later rolled back.

Broadcast events that must not escape an uncommitted transaction implement
`ShouldDispatchAfterCommit`.

## Match flow

```text
Like persisted
    ↓
Database transaction committed
    ↓
LikeObserver
    ↓
MatchService::create()
    ↓
Reciprocal match records persisted
    ↓
MatchService::notify()
    ↓
MatchEvent
    ↓
MatchService::mail()
    ↓
Match emails queued
```

`LikeObserver` only creates a match when the reciprocal like exists.

Match creation remains transactional so both reciprocal match records are
created together or neither is persisted.

## Message flow

```text
Message persisted
    ↓
Database transaction committed
    ↓
MessageObserver
    ↓
MessageService::notify()
    ↓
MessageEvent
    ↓
Broadcast
```

Message broadcasting therefore occurs only after the message has been
successfully persisted.

## Adoption flow

```text
Adoption persisted
    ↓
Database transaction committed
    ↓
AdoptionObserver
    ↓
AdoptionService::setAdoption()
    ↓
AdoptionService::notify()
    ↓
AdoptionEvent
    ↓
AdoptionService::mail()
    ↓
Adoption emails queued
```

Neither the adoption broadcast nor its emails are triggered when the
persistence transaction is rolled back.

## User flow

```text
User created / deleted / restored
    ↓
Database transaction committed
    ↓
UserObserver
    ↓
UserService
    ↓
Welcome / goodbye / welcome-back email
```

User lifecycle emails are deferred until persistence succeeds.

## Species cache flow

```text
Species created / updated / deleted / restored / force deleted
    ↓
Database transaction committed
    ↓
SpeciesObserver
    ↓
Species cache invalidated
```

Cache invalidation is performed from observer handlers rather than from the
observer constructor.

## Block flow

`BlockObserver` performs a database consistency operation by removing the
corresponding like relationship.

Unlike notification and mail observers, this operation remains part of the
database-side application flow rather than being treated as an external
after-commit side effect.

## Testing

Transactional observer tests cover the critical match, message, and adoption
flows.

For each flow, tests verify that:

- side effects do not run before commit;
- side effects run after a successful commit;
- side effects do not run after rollback.
