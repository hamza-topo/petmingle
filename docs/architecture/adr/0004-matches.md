# Reciprocal match transaction

Status: Accepted — records the current implementation.

Date: 2026-10-06

## Context

A mutual pet relationship is stored as two directional rows; partial persistence is invalid.

## Decision

LikeObserver detects the reciprocal like. MatchService verifies InteractionPolicy and creates both directional rows inside DB::transaction before notifications. Blocks resolve pet owners and enforce contact rules in both directions.

## Consequences and limits

Either both match rows persist or neither does. Do not infer database-wide uniqueness or full concurrency guarantees from the transaction alone. Messaging permission additionally requires an active reciprocal match and no block.

## Evidence

- `app/Services/MatchService.php`
- `docs/api/BLOCK_CONTACT_POLICY.md`
- `tests/Feature/Services/MatchServiceTransactionTest.php`
