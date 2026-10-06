# Side effects after commit

Status: Accepted — records the current implementation.

Date: 2026-10-06

## Context

Notifications must not describe changes that a transaction later rolls back.

## Decision

External-side-effect observers use ShouldHandleEventsAfterCommit; broadcast events use ShouldDispatchAfterCommit where required. Register observers centrally. Database consistency work such as BlockObserver remains part of the database flow.

## Consequences and limits

Operate queue workers and realtime transport for asynchronous delivery. Delivery is not guaranteed merely by HTTP success; HTTP reads remain authoritative. After-commit handling does not provide an outbox or exactly-once delivery.

## Evidence

- `docs/EVENT_FLOWS.md`
- `tests/Feature/Events/ObserverAfterCommitTest.php`
- `tests/Feature/Api/RealtimeMessagingTest.php`
