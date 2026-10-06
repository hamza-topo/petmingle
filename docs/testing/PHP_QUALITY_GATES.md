# PHP quality gates

PetMingle uses Laravel Pint for deterministic PHP formatting and Larastan/PHPStan for static analysis.

## Local commands

Run quality tooling in the application container:

```bash
docker compose exec -u www-data app composer format
docker compose exec -u www-data app composer format:test
docker compose exec -u www-data app composer analyse
docker compose exec -u www-data app composer quality
```

`composer format` changes files in place. The other commands are read-only checks.

## CI gates

Pull requests and pushes to `main` run:

```bash
composer format:test
composer analyse
```

A formatting regression or a new static-analysis error fails CI before the database test suite runs.

## Pint policy

`pint.json` declares the canonical Laravel preset.

Issue #90 established the initial formatting baseline by running Pint across the existing PHP codebase. StyleCI is no longer part of the repository; Pint is the single PHP formatting authority.

Do not introduce a second formatter with conflicting rules.

## Larastan / PHPStan policy

Static analysis is configured in `phpstan.neon.dist`.

Current boundary:

- Larastan 3.x / PHPStan 2.x;
- level 5;
- application code under `app/`;
- memory limit 1 GB.

This scope is intentionally narrower than the full repository so the gate is useful immediately and can be strengthened incrementally.

## Baseline policy

`phpstan-baseline.neon` captures only debt that existed when Issue #90 introduced the gate.

At creation it represented:

- 316 baseline rule groups;
- 357 existing errors;
- only files under `app/`.

The baseline is not permission to add new errors. PHPStan still reports any new error that does not match an existing baseline entry, so CI fails on new regressions.

Do not regenerate the baseline merely to make CI green. Prefer fixing the reported code. If an intentional architectural change genuinely requires baseline maintenance, review the baseline diff separately and explain why the debt changed.

When existing debt is fixed, remove the corresponding baseline entry or regenerate the baseline only after reviewing that it shrank or changed for an understood reason.

An intentional baseline refresh can be generated inside Docker with:

```bash
docker compose exec -u www-data app \
  vendor/bin/phpstan analyse \
  --generate-baseline=phpstan-baseline.neon \
  --memory-limit=1G
```

Review `phpstan-baseline.neon` before committing it.

## Progression

The next static-analysis improvement should reduce baseline debt before raising the global level or expanding analysis beyond `app/`. This keeps the gate monotonic: new code stays clean while legacy debt decreases over time.
