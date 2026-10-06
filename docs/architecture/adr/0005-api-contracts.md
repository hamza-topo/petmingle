# Versioned API and explicit contracts

Status: Accepted — records the current implementation.

Date: 2026-10-06

## Context

Database serialization and ambiguous identity fields can accidentally become browser contracts.

## Decision

Keep the existing /api/v.0 prefix. Use explicit resources for normalized read contracts and the standard success/error/pagination conventions. Maintain a core Postman collection plus domain-specific documentation.

## Consequences and limits

Do not rename v.0 without a migration plan. Preserve documented exceptions: authentication shape and raw message send response. Historical audits are not current schemas; changes need matching tests and documentation.

## Evidence

- `routes/api.php`
- `docs/api/README.md`
- `docs/api/API_CONVENTIONS.md`
- `docs/api/MESSAGING_CONTRACT.md`
