# PetMingle API gap register

Cycle 8 · 2026-10-02 · `feat/react-api-integration`

Read with [FRONTEND_API_CONTRACT.md](FRONTEND_API_CONTRACT.md) for exact requests, responses, screen mappings and source links. Findings are source-backed; no exploit, mutating request, migration or integration was performed. Deployment/environment overrides and database contents remain unverified.

Each row has one primary classification from the requested taxonomy. “Backend response normalization” also covers correcting an existing endpoint's validation/query/response behavior; it does not imply that the fix is merely cosmetic. “Missing endpoint” means a capability is unavailable even where a registered stub exists. Priorities: **gate** blocks the affected integration; **decision** needs an explicit product/data contract; **adapter** is frontend-only once reliable data exists; **later** remains out of scope. Suggested resolutions are recommendations for a separately approved cycle, not changes made here.

## Authentication, authorization and security

| ID | Classification | Priority | Evidence and impact | Required decision or correction |
| --- | --- | --- | --- | --- |
| S01 | authorization/security problem | **Resolved — Cycle 9A** (original: gate: registration) | AuthController::signUp passes all input to AuthRepository/User::create; User permits `is_admin`, provider fields. A public registrant can supply privileged attributes, provided the upload path succeeds. | Server-side allowlist; privileged/provider fields must be assigned only by trusted flows. Frontend omission is insufficient. |
| S02 | authorization/security problem | gate: uploads | ImageTrait uses original client filename on public disk, ignoring generated name; collisions can overwrite another account's media. SignUp has no active avatar validation; scalar pet uploads bypass `images.*`. | Enforce actual file validation, unique controlled storage paths and ownership. Do not exploit bypass to make frontend uploads “work.” |
| S03 | authorization/security problem | **Resolved — Cycle 9A** (original: gate: message mutation) | MessageController::update authorizes the existing sender, then mass-assigns all input including sender/receiver/conversation/is_seen. Store correctly uses validated input and server sender. | Restrict content edits to allowed validated fields; enforce participant integrity/read semantics separately. |
| S04 | authorization/security problem | **Resolved — Issue #123** (original: gate: contact actions) | Contact policy now treats an active block in either direction as reciprocal for enforcement. Like and match creation reject blocked pairs; message reads/creates require an active reciprocal match and no block; blocked message edits/restores are denied. Block creation archives conversations/messages and removes likes/matches in both directions. Discovery already excludes blocked accounts. | Keep enforcement server-side. A future unblock flow must not implicitly restore archived relationship/contact state. |
| S05 | authorization/security problem | **Resolved — Cycle 9A** (original: gate: taxonomy writes) | SpeciesController/RaceController allow any authenticated account to mutate/delete/restore global options; no policy/admin middleware. | Restrict catalog mutations; frontend integration needs reads only. |
| S06 | authorization/security problem | **Resolved — Cycle 9A** (original: gate: network broadcasting) | MessageEvent exposes public Message on public `new-message`; MatchEvent uses public `new-match`. Default broadcaster is null; real exposure depends on deployment. | Participant-authorized channels and safe event payloads before enabling a network broadcaster. No realtime client should be added now. |
| S07 | authorization/security problem | gate: nearby scope | Near accepts arbitrary `user_id` as exclusion identity and unvalidated `perimeter`; filter route has no validation. This changes who is excluded and search range. SQL distance values are parameter-bound; no SQL-injection claim is made. | Assign authenticated account server-side, validate range/coordinates and define discovery visibility policy. |
| I01 | missing endpoint | **Resolved — Cycle 9B** (original: gate: all authenticated screens) | No read-only current-user/current-pet endpoint; login returns token only. Pet reads contain owner IDs but no authoritative current identity. | Agree an identity response with user ID, account fields and nullable current pet; never decode PAT prefix as a user ID. |
| A01 | backend response normalization | **Resolved — Cycle 9B** (original: gate: browser transport) | CORS config exists but bound Kernel lacks HandleCors, nginx lacks CORS headers, Vite has no proxy. Cookie support is additionally disabled/incomplete. | Choose and verify a supported browser transport. Retaining bearer auth is the smallest existing contract; cookie auth would be separate work. |
| A02 | backend response normalization | gate: registration | Avatar is optional in validation but unconditionally passed to `setFile(UploadedFile)`; omitted avatar causes type failure. No token returned by sign-up. | Agree avatar optionality and registration/login sequence; do not treat returned User as an authenticated session. |
| A03 | frontend adapter only | adapter | HTTP-200 legacy field errors use `data`; Pet Update has 422/data; ordinary validation has 422/errors; other errors have status/message. Species/races are unwrapped. | Endpoint-aware success/error normalization. Existing 200 status is not sufficient to signal save success. |
| A04 | deferred feature | later: social auth | API OAuth callback performs session login without PAT handoff; repository lookup includes a random password each time. | Separate social handoff/repeat-login review; not equivalent to password bearer auth. |
| A05 | data-model ambiguity | decision: auth lifecycle | Tokens are opaque, no refresh endpoint, configured expiration null; UI has no established auth state/storage policy. | Decide lifecycle/storage and revocation UX; retain no credentials. No auth configuration changed here. |

## Pet/profile and shared data

| ID | Classification | Priority | Evidence and impact | Required decision or correction |
| --- | --- | --- | --- | --- |
| P01 | data-model ambiguity | gate: pet ownership | User.pet is hasOne, but pets.user_id has no unique constraint and Store allows multiple pets. Companion and fixture pet pairs suggest richer presentation. | Decide one/current/multiple-pet semantics before identity and creation. Never silently choose by fixture name. |
| P02 | backend response normalization | gate: pet creation | Migration requires sexe/color/about without defaults; Store does not validate them and React has no such fields. | Reconcile persistence requirements with approved form without inventing hidden values or redesigning the form. |
| P03 | backend response normalization | gate: photos | `images.*:image|size:1024` assumes children; controller accepts one UploadedFile; JSON cast receives scalar path. Arrays fail, scalar bypasses checks. Chat assumes images[0]. | Define one media/gallery contract and make validation/storage/serialization agree. `size` is exact 1024 KiB, not max. |
| P04 | frontend adapter only | adapter after P03 | View assets use src/alt/placeholder, backend returns storage paths; UI uses camelCase/string IDs and backend snake_case/numeric IDs. | Typed mapping, approved backend media URL base, null placeholders and explicit ID domains. No invented images. |
| P05 | frontend adapter only | adapter with validation agreement | Form name min 1/no max vs server 3–25; species/breed strings vs IDs; age digit string vs DB tiny integer. | Map catalog IDs, numeric age and field errors; align approved frontend constraints with server, never hardcode taxonomy IDs. |
| P06 | backend response normalization | gate: invalid pet payloads | Species/race IDs only integer-validated; no consistency check between race and species, age lacks bounds. FKs reject nonexistent IDs only if deployed; tiny integer limits vary by schema/driver. | Validate active IDs, species/race relationship and explicit age range before returning database errors. |
| P07 | data-model ambiguity | decision: rich pet attributes | No size, traits, energy, playdate preference, weight, spayed, good-with or favorite-activities columns/models in pet flow. Mockup form and profile require them. | Agree future persistence or explicit temporary unavailability; never silently discard fields while claiming profile saved. |
| P08 | data-model ambiguity | decision: verification/companion | users.is_verified is an account field; featured pet verified badge, companion identity and featured ranking have no equivalent. `is_new` is recency. | Define their meanings; do not equate account verification/newness to pet verification or infer ownership from companion photos. |
| P09 | backend response normalization | gate: no-pet accounts | Like/Dislike/Match list controllers dereference user.pet; Near/Chat resources dereference users/pets/races without guards. Soft-deleted parents also disappear from relations. | Explicit no-pet onboarding/empty states and null-safe reads instead of 500s. |
| P10 | backend response normalization | gate if relation used | Pet.locations() infers pet_id, absent from locations (which uses user_id). | Correct/avoid the relation under the agreed account-location model. Do not rely on it for profile reads. |
| P11 | data-model ambiguity | decision: fixture identities | Discovery Nala city differs from profile; Messaging's current owner represents different pets, and fixture IDs are unrelated slugs. | Bind future views to authoritative account/pet IDs and document deviations from static mockup copy. Do not join by names. |
| P12 | backend response normalization | before broader catalog use | Race show returns null if missing, Species show uses 404; race species_id is required by DB but not request-validated; race Update unique rule does not exclude itself. | Agree normalized not-found and catalog validation behavior; no frontend catalog writes needed. |

## Discovery, relationships and location

| ID | Classification | Priority | Evidence and impact | Required decision or correction |
| --- | --- | --- | --- | --- |
| D01 | backend response normalization | gate: nearby cards/actions | Near response omits pet/user IDs, age and about. Pet index cannot be safely joined to it by names. | Include stable pet/owner IDs and required existing data in proximity result, or agree a reliable keyed composition. |
| D02 | backend response normalization | gate: radius filter | Request validates perimetre but repository consumes perimeter, default 5 km. Unvalidated override reaches query. | One named, bounded distance parameter with explicit units. |
| D03 | frontend adapter only | adapter after D01/D02 | Near distance is rounded text in km; UI expects numeric miles; sex is translated text, pet raw sexe is 0/1. | Normalize known units/enums; prefer server numeric distance when response is revised. Localize display at UI boundary. |
| D04 | backend response normalization | gate: filtering | LocationController passes a PetFilter object to Near; filter uses nonexistent Location.pet, malformed age indexing, race.race_id and inconsistent paths/predicate grouping. | Correct query/input/output contract before use; cannot be repaired by a client payload alone. |
| D05 | data-model ambiguity | decision: filter meanings | GET filters is a global name/description/is_free catalog; UI selects species/size/energy/personality. Most data fields do not exist. | Define supported filters and semantics; do not claim the generic catalog implements the UI. |
| D06 | data-model ambiguity | decision: current location | Multiple user coordinates, no current flag or city, nullable stored coordinates. Discovery/profile expect a city string. | Agree location selection and city source; no fabricated or external geocoding added in this cycle. |
| D07 | backend response normalization | before reliable nearby counts | DISTINCT includes distance, so multiple coordinates can duplicate a user's pet; query does not exclude seen/blocked relationships or guarantee pet presence. | Define one candidate per pet and visibility rules; derive count only from that scope. |
| D08 | frontend adapter only | adapter | Like/dislike list data.from/to become Pet objects due to eager relation names; create uses integer IDs; paginator.from/to are offsets. Matches use scalar IDs plus to_pet. | Endpoint-specific DTOs and explicit joins; normalize before use in UI state. |
| D09 | backend response normalization | gate: like/dislike mutation | Duplicate Like branch has no return with ?Like; repeated likes can error. Dislikes/pair matches lack unique-pair guarantees; self-target and active target not validated. | Agree idempotency, target rules, duplicate handling and matched-count semantics. Frontend disabling a button is insufficient. |
| D10 | missing endpoint | gate if undo required | Likes/dislikes DELETE methods are empty; no valid unlike/unsave/rewind contract. | Do not interpret mockup hearts as reversible persisted favorites until action semantics and endpoint exist. |
| D11 | missing endpoint | gate if location editing required | Registered Location update is empty. | Define update behavior if needed; do not use a no-op response as a saved location. |
| D12 | missing endpoint | decision: profile statistics | No view counter/stat endpoint, incoming-like summary, or defined favorites statistic. GET matches only supplies active directional rows. | Use only supported match count after duplicate policy; views/favorites stay unavailable until scoped separately. |
| D13 | data-model ambiguity | decision: badges/favorites | Discovery messages count, matches badge and profile totals have different scopes; “Save” hearts may mean favorites rather than matching likes. | Name/count each domain explicitly; do not map saves to likes or total messages to unread count automatically. |

## Messaging and deferred UI capabilities

| ID | Classification | Priority | Evidence and impact | Required decision or correction |
| --- | --- | --- | --- | --- |
| M01 | missing endpoint | gate: conversation list | Conversation model/repository exist but no list/detail API; no pair summaries, last message, participant bundle or unread count. | Participant-authorized conversation summaries/details contract. Match lists alone cannot establish message history/unread state. |
| M02 | backend response normalization | gate: thread | GET messages selects only current user's outgoing messages; receiver_id unvalidated, no ordering/conversation scope, paginator metadata discarded by embedded Chat resource. | Bidirectional participant-scoped thread with validated IDs, deterministic order and pagination metadata. |
| M03 | backend response normalization | gate: bubbles | Chat resource omits id/content/is_seen/raw timestamps; returns relative date strings, blank owner photos and fragile pet image indexing. | Stable message IDs, actual content, machine dates and explicit receipt/media fields. No frontend adapter can recover omitted content. |
| M04 | data-model ambiguity | gate: conversation consistency | Conversation lookup matches either orientation but lacks unique pair/atomic creation; message participants can disagree with conversation participants (see S03). Soft-delete recreation is possible. | Agree uniqueness/history semantics and enforce participant integrity/blocking. |
| M05 | missing endpoint | gate if unread/read UI enabled | is_seen column exists, but no authorized receiver read-marking endpoint or unread summary. Sender-only update is not a read-receipt API. | Define read scope and count response; keep local fixture receipts distinct from actual delivery/read claims. |
| M06 | frontend adapter only | adapter after M01–M03 | ChatMessage senderId is human identity; bubble direction compares currentOwnerId. UI activity labels are anchored to April 2024 fixtures. | Map authoritative sender IDs, actual timestamps and current clock; never infer direction from pet avatar/name. |
| M07 | deferred feature | later | Shared interests, playdate details/actions have no backend representation. | Keep out of initial Messaging integration; no fake persisted scheduling. |
| F01 | deferred feature | later | Plus prices, billing periods and benefits are local fixtures; no subscription/payment flow found. | Keep presentational; no checkout, billing library or fake subscription success. |
| F02 | deferred feature | later | Real-time delivery is not needed for the first HTTP contract; backend events already have security gaps. | Defer sockets/polling/Echo until HTTP reads, authorization and event privacy are settled. |
| F03 | missing endpoint | not an integration target | Full resource registration creates API create/edit routes whose methods are absent or empty; Like/Dislike show/update also empty, Message show/edit absent. | Treat as unsupported; do not wire UI navigation/actions to resource URLs based only on route registration. |
| F04 | deferred feature | later | Language setter changes runtime locale only, not user preference persistence. | Do not map creation playdate/energy preferences to language preferences or promise persistent locale settings. |

## Existing protections that must be preserved

- All non-auth API groups require Sanctum. Pet/location/message mutation policies and user self/admin policies are registered.
- Pet Store, Location Store, Message Store and Block Store assign origin/owner from the authenticated user. Pet Update uses validated input and cannot change owner.
- Message update/delete/restore require the sender; receivers/unrelated users cannot edit content under existing policies. The mass-assignment gap concerns fields an already-authorized sender can alter.
- Block target validates existing user/non-self. Likes validate the originating pet against the current user's pet. These checks do not solve the separate target/block/match gaps.
- The 2026 migrations add foreign keys, and API exception handling avoids exposing internal exception messages. Deployment of these migrations was not verified by this audit.
- Existing Auth, Pet/Location/Message/User/Block authorization, relational integrity, observer and transaction tests provide useful regression starting points. Reading them is not a substitute for tests covering the gaps above; no backend suite/migration was run here.

## Integration gates and next-cycle recommendation

Cycle 9B has established **transport + password bearer auth + authoritative identity**. The safest next integration cycle can now consume that limited contract in React; no network client or auth UI has been written yet. Taxonomy reads precede pet creation; then pet/profile and locations can establish the prerequisites for Discovery. Relationship mutations follow only after policy/idempotency decisions. Messaging is last because both its list and thread contracts are incomplete.

Only auth/identity is ready for the first React call; other domains retain their specific gates above. Do not solve missing fields by retaining fixture values inside otherwise real records: explicit unavailable/deferred fields are safer than mixed identities, false verification, fabricated counts or claimed persistence.

## Cycle 9A security hardening — 2026-10-02

The original findings above are retained as historical evidence. Only S01, S03, S05 and S06 are marked resolved in this cycle:

- **S01:** API signup uses validated form fields; the shared AuthRepository registration boundary permits only name/email/password/avatar and assigns `is_admin = false`. Provider identities and other internal attributes cannot be supplied through public API or Blade registration. User fillable remains compatible with trusted administrative/provider workflows. Regression tests exercise both public entry points.
- **S03:** Message updates retain the existing sender-only policy and pass only validated content to the repository. PUT/PATCH spoofing tests verify that sender, receiver, conversation and read status remain unchanged, alongside existing receiver/unrelated-user rejection tests. No message read contract or matching behavior was redesigned.
- **S05:** API Species/Race controllers apply the existing `admin` middleware to store/update/destroy/restore, covering POST, PUT, PATCH, DELETE and restore. Reads retain their existing authentication requirements. Tests exercise both administrator success and ordinary-member denial for every mutation, plus member reads.
- **S06:** Message, Match, Adoption and typing events now use private `App.Models.User.{userId}` channels. Match recipients are resolved from pet IDs to owner user IDs. Message/match/adoption payloads explicitly include only event record fields, preventing eager-loaded account relations from leaking. Event names and top-level payload keys remain unchanged. Channel authorization allows only the account itself (including for administrator accounts); the already-private sitemap channel permits administrators only.

The application broadcast provider remains **disabled in config/app.php**, as before. Enabling it would activate infrastructure outside this security scope; the local environment selects Pusher but its PHP SDK is not installed. This cycle installs nothing and does not silently switch the broadcaster. With the provider disabled, subscription authorization is unavailable and private channels fail closed. When explicitly enabled in a configured deployment, its auth route now uses `web` plus `auth:sanctum`; normal web/CSRF rules still apply. A future React bearer-only broadcasting handshake is not implemented here. Tests explicitly register the real provider using Laravel's Redis channel authorizer with a mocked connection factory, so no external service is contacted.

The legacy `resources/js/app.js` public-channel listeners will no longer receive these private events. Its Echo bootstrap is already commented out; no active realtime client was introduced or modified. A future enabled client must subscribe to its authenticated account's private channel. No public compatibility broadcast is retained, since that would preserve the information leak.

Block create/list were already correctly scoped: the server assigns the authenticated actor, validates non-self targets and lists only the actor's blocks. Existing spoof/self-block tests are retained; new tests cover anonymous requests and attempted query-based list impersonation. No new block endpoint or policy abstraction was necessary.

**Still open:** S02 (upload validation/storage), S04 (reciprocal match/contact/block enforcement) and S07 (nearby query identity/range). Block endpoint authorization is not a claim that all later contact respects blocks. All other audit gaps retain their existing status. CORS, identity endpoint, sign-in response, Discovery, message response shape, schemas and React integration are unchanged.

Validation: the complete Docker Laravel suite passed **121 tests / 327 assertions**, including **30 added cases** (taxonomy data-provider cases included). Frontend passed **27/27** with `npm test -- --maxWorkers=1`; the first parallel run hit 5-second timeouts under load, and no frontend files or timeout settings were changed. `composer validate --no-check-publish` passed. `git diff --check` passed. No application-schema migration, dependency installation or commit was made.

## Cycle 9B auth, identity and transport — 2026-10-02

Only **I01** and **A01** are newly resolved. Original audit descriptions are retained above; Cycle 9A protections and resolved statuses remain intact.

- **I01:** protected `GET /api/v.0/me` returns an explicit envelope with `data.user = {id,name,email}` and `data.pet = {id,user_id,name} | null`. It uses the authenticated account's existing hasOne pet relation, ignores subject/expansion query parameters and sends private/no-store cache headers. No admin/provider/security attributes, tokens, hashes or timestamps are exposed. Missing pet is a supported state; P01's unenforced cardinality is not resolved.
- **A01:** global HandleCors is enabled for `api/*`, with explicit methods/headers, no cookie credentials, and configurable exact origins through `CORS_ALLOWED_ORIGINS`. Local defaults cover localhost/127.0.0.1 on 5173/5174; nonlocal environments without explicit configuration permit no cross-origin frontend. Running Docker publishes Vite at host 5174 and nginx at 8000. Production must replace the local example origins with deployed HTTPS origins. Browser-readable 401 responses and bearer preflights are covered by tests and local nginx probes.
- Sign-in retains `/sign-in` and `token`, adding only `token_type: "Bearer"`; no profile is duplicated. Sign-out retains current-token-only deletion. Tests prove revoked-token denial and continued validity of another device token. Missing/invalid tokens cannot access `/me`; caller-supplied user IDs cannot change its subject.
- Root `.env.example` documents the origin allowlist; `frontend/.env.example` reserves `VITE_API_BASE_URL=http://localhost:8000/api/v.0`. It is public configuration, never token storage. No client consumes it yet. SPA-cookie/stateful Sanctum flow is not selected, and no stateful-domain/session settings change.

The first auth-only React API call is locally unblocked at the backend/transport layer. A future implementation must choose token storage/lifecycle (A05 remains open), configure the deployed origins/API URL, and add auth/error/no-pet state deliberately. Signup upload limitations, remaining security gaps S02/S04/S07, rich pet/profile data, Discovery, Messaging and all other unmarked gaps remain unchanged. No schema migration, fixture replacement, fetch/axios call or frontend dependency was introduced.


## Issue #123 block/contact enforcement — 2026-10-05

S04 is resolved by a single server-side interaction policy and explicit transition rules in [BLOCK_CONTACT_POLICY.md](BLOCK_CONTACT_POLICY.md).

- Blocks are symmetric for enforcement even though the stored block has a directional creator/target.
- Likes cannot be created across an active block.
- Match creation is gated by reciprocal active likes and the absence of a block.
- Message thread reads and message creation require an active reciprocal match and the absence of a block.
- A blocked sender cannot edit or restore an old message.
- Creating a block archives active conversations/messages and removes likes/matches in both directions.
- Discovery keeps its existing bidirectional block exclusion.
- No unblock endpoint is introduced. Removing a block in a future authorized flow will not automatically restore old likes, matches, conversations or messages.

Issue #124 remains responsible for normalizing Messaging conversation/thread response contracts; #123 only establishes the contact authorization boundary.
