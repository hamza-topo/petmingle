# Critical business-flow test coverage

Issue #89 defines the release-level feature-test baseline for PetMingle.

This document tracks **behavioral coverage**, not only line coverage. The CI runner does not currently install a PHP coverage driver such as PCOV/Xdebug, so a raw percentage would be misleading as a release gate.

## Practical coverage target

For every critical category listed in Issue #89:

1. at least one persisted happy path must be covered;
2. every externally writable flow must have at least one applicable authorization or validation failure;
3. security-sensitive identity boundaries must be asserted explicitly;
4. side effects that matter to users must be covered at their boundary: database, event, queue, or mail;
5. the suite must pass against MySQL in GitHub Actions.

The release target is therefore **100% coverage of the critical-flow matrix below**.

A future line-coverage metric may supplement this target, but it must not replace behavioral coverage.

## Coverage matrix

| Critical flow | Happy-path coverage | Failure / boundary coverage | Status |
| --- | --- | --- | --- |
| Authentication and sign-out | `tests/Feature/Api/AuthTest.php` covers sign-up, sign-in, protected access and token revocation | invalid credentials, anonymous protected-route access, privileged signup-field rejection | Covered |
| Authorization failures | API authorization suites for User, Pet, Location, Block, Message and Taxonomy | ownership, unauthenticated, forbidden and spoofed-identity cases | Covered |
| Pet CRUD | `PetCreationContractTest`, `PetProfileUpdateContractTest`, `PetContractTest`, admin pet deletion | `PetAuthorizationTest`, upload validation/security, no-pet contracts | Covered |
| Like / dislike workflow | `RelationshipContractTest` covers create, replacement and idempotency | self-like, spoofed source/target fields, blocked interactions | Covered |
| Reciprocal matching | `CriticalRelationshipFlowTest` drives two real API likes through observer → reciprocal Match rows → MatchEvent → two queued mails | `MatchServiceTransactionTest` covers partial-state rollback; block policy prevents forbidden matching | Covered |
| Blocking | `BlockAuthorizationTest`, `BlockedInteractionTest`, `BlockObserverTest` | bidirectional contact denial, cleanup and restore/edit denial | Covered |
| Messaging | `MessagingContractTest`, send/read/seen/realtime feature tests | `MessageAuthorizationTest`, blocked-contact tests, private-channel authorization | Covered |
| Adoption | `AdoptionFlowTest` covers real admin POST → persisted Adoption → AdoptionEvent → two queued mails | `AdoptionAuthorizationTest` and actual admin middleware rejection | Covered |
| Location behavior | `LocationContractTest`, Discovery contract/filter suites | `LocationAuthorizationTest`, `NearbyDiscoverySecurityTest` | Covered |
| Admin protection | `AdoptionFlowTest` verifies actual admin middleware and successful admin mutation | anonymous → 401; non-admin → 403; policy tests remain separate | Covered |
| Queue, mail and event dispatch | reciprocal-match flow, adoption flow, `ObserverAfterCommitTest`, `ProcessNewsLettersTest`, `NewsLetterCommandDispatchTest` | rollback tests ensure after-commit side effects do not leak; queue dispatch is asserted independently | Covered |

## End-to-end matching invariant

The critical reciprocal-match test intentionally uses public API boundaries rather than direct repository calls:

```text
User A POST /likes
    ↓
Like A→B persisted
    ↓
User B POST /likes
    ↓
Like B→A persisted
    ↓
LikeObserver after commit
    ↓
MatchService transaction
    ↓
Match A→B + Match B→A
    ↓
MatchEvent
    ↓
2 queued ItsAMatch mails
```

This is the minimum regression path that must stay green for the core PetMingle relationship model.

## Adoption invariant

The admin adoption happy path exercises both the real middleware and the domain side effects:

```text
authenticated admin
    ↓
POST /admin/adoptions
    ↓
Adoption policy
    ↓
Adoption persisted
    ↓
AdoptionObserver after commit
    ↓
AdoptionEvent
    ↓
2 queued ItsAdoption mails
```

Anonymous and non-admin requests are tested through the actual `admin` middleware rather than only the policy.

## Queue invariant

The newsletter suite now covers both halves separately:

- `NewsLetterCommandDispatchTest`: command pushes `ProcessNewsLetters` to the queue;
- `ProcessNewsLettersTest`: job selects the correct persisted recipients and queues mail without duplicates.

This keeps scheduling/dispatch failures distinct from job-content failures.

## CI execution

GitHub Actions runs the Laravel suite against MySQL 8.

Focused local validation for Issue #89:

```bash
docker exec petmingle-app php artisan test \
  tests/Feature/Api/CriticalRelationshipFlowTest.php \
  tests/Feature/Admin/AdoptionFlowTest.php \
  tests/Feature/Jobs/NewsLetterCommandDispatchTest.php
```

Full release gate:

```bash
docker exec petmingle-app php artisan test
```

A change to a critical business flow should update this matrix when its behavior or test ownership changes.
