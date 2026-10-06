# Repository and service boundaries

Status: Accepted — records the current implementation.

Date: 2026-10-06

## Context

Persistence and business orchestration need identifiable owners without imposing a new rewrite.

## Decision

Repositories own queries/persistence. Services coordinate cross-record operations and interaction policy. Controllers own HTTP validation, authorization and responses. Preserve existing concrete repositories; this decision describes the current direction, not a universal service requirement.

## Consequences and limits

Avoid moving HTTP concerns into repositories or trusting client identity. Legacy controller orchestration can remain until a focused change justifies extraction.

## Evidence

- `app/Services/MatchService.php`
- `app/Services/InteractionPolicy.php`
- `app/Http/Controllers/Api/MessageController.php`
