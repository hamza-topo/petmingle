# PetMingle frontend/API contract audit

Cycle 8 audit, updated through Cycle 9B · 2026-10-02 · branch `feat/react-api-integration`

## Scope and conclusion

This document started as the Cycle 8 source audit. Cycle 9A hardened selected security boundaries; Cycle 9B establishes the current bearer-authentication, identity and browser transport contract. The four authenticated-looking React screens still use local fixtures. No React network client, auth UI, token storage, dependency or application-schema migration has been introduced.

Authentication and minimal identity are now available for the first React integration. Pet creation/media, Discovery proximity/filtering and Messaging still have the contract and security gaps described below. [API_GAPS.md](API_GAPS.md) retains the original findings and marks only resolved items.

Evidence includes routes, controllers, requests, repositories, models, policies, observers, migrations, configuration, frontend types and tests. Cycle 9B inspected Docker port mappings and checked live unauthenticated HTTP/preflight responses through nginx; bearer identity/revocation are tested on the isolated PHPUnit database. Production environment overrides, storage links, real user records and deployment migration state were not inspected.

## Sources of truth

| Area | Source |
| --- | --- |
| API route registration | [routes/api.php](../../routes/api.php), [RouteServiceProvider](../../app/Providers/RouteServiceProvider.php) |
| Auth and middleware | [AuthController](../../app/Http/Controllers/Api/AuthController.php), [AuthRepository](../../app/Repositories/AuthRepository.php), [Kernel](../../app/Http/Kernel.php), [bootstrap](../../bootstrap/app.php), [CORS](../../config/cors.php), [Sanctum](../../config/sanctum.php) |
| Pet contract | [PetController](../../app/Http/Controllers/Api/PetController.php), [Store](../../app/Http/Requests/Api/Pet/Store.php), [Update](../../app/Http/Requests/Api/Pet/Update.php), [Pet model](../../app/Models/Pet.php), [ImageTrait](../../app/Traits/ImageTrait.php) |
| Discovery | [LocationController](../../app/Http/Controllers/Api/LocationController.php), [LocationRepository](../../app/Repositories/LocationRepository.php), [Near resource](../../app/Http/Resources/Api/Location/Near.php), [PetFilter](../../app/Filters/PetFilter.php) |
| Relationships | [LikeRepository](../../app/Repositories/LikeRepository.php), [DislikeRepository](../../app/Repositories/DislikeRepository.php), [MatchRepository](../../app/Repositories/MatchRepository.php), [BlockObserver](../../app/Observers/BlockObserver.php) |
| Messaging | [MessageController](../../app/Http/Controllers/Api/MessageController.php), [MessageRepository](../../app/Repositories/MessageRepository.php), [ConversationRepository](../../app/Repositories/ConversationRepository.php), [Chat resource](../../app/Http/Resources/Api/Message/Chat.php), [IsAllowed](../../app/Rules/Api/Message/IsAllowed.php) |
| Ownership enforcement | [PetPolicy](../../app/Policies/PetPolicy.php), [LocationPolicy](../../app/Policies/LocationPolicy.php), [MessagePolicy](../../app/Policies/MessagePolicy.php), [UserPolicy](../../app/Policies/UserPolicy.php), [AuthServiceProvider](../../app/Providers/AuthServiceProvider.php) |
| Relational IDs | [2026 foreign-key migration](../../database/migrations/2026_09_27_170023_add_core_relationship_foreign_keys.php), preceding column normalization migration |
| Frontend expectations | [Discovery fixtures](../../frontend/src/features/discovery/discovery.fixtures.ts), [creation schema](../../frontend/src/features/profile-creation/profile.schema.ts), [Messaging fixtures](../../frontend/src/features/messaging/messaging.fixtures.ts), [Own Profile fixtures](../../frontend/src/features/own-profile/profile.fixtures.ts), [PetCard types](../../frontend/src/components/PetCard.tsx), [ReferenceAsset](../../frontend/src/assets/landingAssets.ts) |

## Transport and response conventions

All paths below are relative to `/api/v.0`. `A` means `auth:sanctum`; `P` means public. All share the API throttle (60 requests/minute, keyed by authenticated user or IP). Endpoint-specific ownership is additional to authentication.

Notation:

```ts
// Documentation notation, not a proposed client implementation.
type Envelope<T> = { success: true; message: string; data: T };
type LegacyValidation = {
  success: false; message: 'Validation errors'; data: Record<string, string[]>;
};
type StandardValidation = {
  success: false; message: 'Validation failed.'; errors: Record<string, string[]>;
};
type ApiError = { success: false; message: string };
```

Most controller successes use HTTP 200 and `Envelope<T>`, including creates. Sign-in is `{success:true,token,token_type:"Bearer"}`. Species/races return bare models/arrays: reads/updates use 200, newly created models use 201 through Laravel's router. Taxonomy deletes/restores return PHP booleans directly, resulting in a 200 text/html response (`1` or empty), rather than a JSON envelope; these are not frontend integration targets.

Most custom API FormRequests throw an `HttpResponseException` containing **HTTP 200** `LegacyValidation`. Pet Update explicitly uses 422 with the same legacy body. Laravel's `Route::run()` catches these exceptions and returns their embedded responses. Consequently, the custom exception handler does **not** normalize these controller argument-validation responses. Block validation uses ordinary `ValidationException`: HTTP 422 `StandardValidation` through [Handler](../../app/Exceptions/Handler.php). Authentication/authorization/not-found/internal exceptions use HTTP 401/403/404/500 `ApiError`; throttling can return 429. A future adapter must check both status and `success`, support both field-error keys, and not treat a 200 validation failure as saved data.

Raw Eloquent results include IDs, database attributes, timestamps and `deleted_at`; they are not stable screen DTOs. Some uncast numeric/boolean database fields depend on driver hydration. Creates need not include database-default attributes until refreshed. The shapes below describe selected read fields and known transformations, not live response captures. Soft-deleted records are normally excluded.

## Identity mapping — never substitute user IDs for pet IDs

| Field/context | Identity | Source/meaning |
| --- | --- | --- |
| `User.id` / authenticated user | Human account ID | Sanctum token resolves a `User`; not a pet |
| `Pet.id` | Animal ID | Independent sequence from users |
| `Pet.user_id` | Owner's **User ID** | `Pet.owner()`; store overrides with authenticated user |
| `User.pet` | One related `Pet` | `hasOne`; can be null; not enforced as one row by a unique DB key |
| `Location.user_id` | **User ID** | Locations belong to accounts, not animals |
| `Like.from`, `Like.to` | **Pet IDs** | Origin pet / target pet; Store's `MatchUser` validates origin against `auth()->user()->pet->id` |
| `Dislike.from`, `Dislike.to` | **Pet IDs** | Same request/rule as Like |
| `MatchTable.from`, `.to` | **Pet IDs** | A reciprocal match is stored as two directional rows |
| `Block.from`, `.to` | **User IDs** | Origin assigned server-side; target validated against users |
| `Message.sender_id`, `.receiver_id` | **User IDs** | Sender assigned server-side on create; receiver is a human account |
| `Message.conversation_id` | Conversation ID | Neither user nor pet ID |
| `Conversation.first_user_id`, `.seconde_user_id` | **User IDs** | Keep the actual `seconde` spelling in any backend DTO |
| `Chat.pet_sender_id`, `.pet_receiver_id` | **Pet IDs** | Separate from sender/receiver user IDs |
| Frontend `ChatMessage.senderId` | Human participant ID | Compare to `currentOwnerId` to derive message direction |
| Frontend `ChatOwner.representedPetId` | Presentation association | Does not establish ownership |
| Frontend `OwnPet.ownerId` / `ProfileOwner.petIds` | Explicit user→pet relation | Must eventually come from authoritative server relationships |

Example: a target `Pet{id:42,user_id:7}` is liked with `to:42`, messaged with `receiver_id:7`, and blocked with `to:7`. A Sanctum token prefix is a token record identifier, **not** a user ID. Do not decode it to infer account identity.

The 2026 migrations explicitly reference pets for likes/dislikes/matches and users for blocks/messages/conversations. They do not enforce one pet per user, unique relationship pairs, or agreement between message participants and conversation participants. `Pet.locations()` incorrectly assumes `locations.pet_id`; the actual column is `user_id`. Use account locations conceptually; do not plan around that relation.

Frontend slugs such as `nala`, `profile-owner`, and `nala-pet-a` are fixture identifiers, not persisted IDs. Names are not safe join keys. Messaging's same `current-owner` represents different pets in different fixture conversations; its initial outgoing pet is Milo, whereas Own Profile shows Nala. Discovery and Own Profile show different cities for Nala. These are documented mockup identities, not evidence of backend ownership or multiple authenticated accounts. An integration needs one canonical current account/pet decision rather than guessing from the fixtures.

## Authentication contract

| Endpoint | Auth | Request | Result / constraint |
| --- | --- | --- | --- |
| `POST /sign-in` | P | JSON `email: required email`, `password: required string` | 200 `{success:true,token:string,token_type:"Bearer"}`; validly shaped bad credentials → 401; malformed payload → legacy validation |
| `POST /sign-up` | P | `name`, unique email, password 6–50 chars, `password_confirmation`; effectively multipart `avatar` too | 200 `Envelope<User>`; **no token**. Avatar rules are commented out but controller unconditionally requires an UploadedFile |
| `GET /me` | A | No body or identity selector | 200 `Envelope<{user:{id,name,email},pet:{id,user_id,name}\|null}>`; private/no-store |
| `POST /sign-out` | A | No body | 200 `{success:true,message}`; deletes current access token only |
| `GET /login/{provider}` | P | Provider `facebook`, `github`, or `google` | Stateless Socialite redirect |
| `GET /login/{provider}/callback` | P | Provider callback parameters | Auth::login; JSON `Envelope<User>` when requested, otherwise `/home` redirect; no PAT handoff |
| `PUT /remove-avatar/{userId}` | A + self | No body | `Envelope<User>` with removed avatar |
| `DELETE /disable-account/{userId}` | A + self | No body | Success message; revokes user's tokens and soft-deletes user |
| `PUT /enable-account/{userId}` | A + admin | No body | Success message; restores user |

Password sign-in uses `Auth::attempt` through the default `web` guard, then explicitly issues `createToken('api')->plainTextToken`. The existing `/sign-in` path and `token` key remain unchanged; Cycle 9B adds only `token_type: "Bearer"`. Full profile data is not duplicated into login. There is no `/signin` alias, refresh endpoint or expiry metadata. Checked-in Sanctum expiration remains null; token storage/lifetime policy remains a future frontend decision.

Use `Accept: application/json` and `Authorization: Bearer <token>` on protected calls. `POST /sign-out` retains its existing behavior: it deletes only the token presented on the request, not other devices' tokens. Subsequent `/me` with that token returns 401, while a different token for the same account remains valid. Missing/invalid/revoked tokens return 401. Tests use real Sanctum personal access tokens, not `Sanctum::actingAs`, for this contract.

### Authenticated identity response (Cycle 9B)

```json
{
  "success": true,
  "message": "Authenticated identity.",
  "data": {
    "user": { "id": 7, "name": "Sarah", "email": "sarah@example.com" },
    "pet": { "id": 42, "user_id": 7, "name": "Nala" }
  }
}
```

These numbers are illustrative and intentionally different. `data.user.id` is the authenticated human account ID. `data.pet.id` is the animal ID; `data.pet.user_id` references `data.user.id`. The pet is obtained only through the authenticated account's existing `User::pet()` relation. Accounts without an active pet (including a soft-deleted pet) receive `pet: null`, not an error or an unrelated animal. Query parameters such as `id`, `user_id` and `include` cannot change the authenticated subject or expand the explicit allowlist. The response has `Cache-Control: private, no-store`.

Only the fields shown are returned. No hash/password, role, provider identity, verification/internal timestamps, access tokens, or arbitrary eager-loaded relations are serialized. Avatar/gallery URLs and detailed pet profile fields are deliberately outside this minimal identity contract until the existing media/profile gaps are resolved. `pet` is a singular object following the current hasOne model; the absence of a database uniqueness constraint remains P01, not a resolved multi-pet design. Existing data with multiple active pets inherits the current relation's selection behavior; the frontend must not infer a new active-pet rule.

Future frontend state needs the opaque token (under a separately agreed storage policy), auth status, authoritative user identity, nullable pet identity, and a no-pet/onboarding state. `GET /me` after login or reload replaces guesses from fixture names, token prefixes or mutations. Clear account-scoped local state on sign-out/401. Do not retain passwords or put tokens in Vite environment variables.

### Browser transport and environment (Cycle 9B)

The checked running Docker frontend publishes `127.0.0.1:5174` → container `5173`; Vite runs with `--host 0.0.0.0 --port 5173 --strictPort`. Laravel nginx publishes host port 8000. A non-Docker Vite run normally uses host 5173. Browser origins are host URLs, never Docker service names or the ephemeral `172.*` container IP.

`Illuminate\Http\Middleware\HandleCors` is now in the global HTTP kernel, before maintenance handling. Its `api/*` scope handles preflights before Sanctum authentication and adds CORS headers to API responses including unauthenticated errors. Allowed methods are GET/HEAD/POST/PUT/PATCH/DELETE/OPTIONS; allowed request headers are Accept/Authorization/Content-Type; preflight max age is 600 seconds. Cookie credentials remain disabled. `sanctum/csrf-cookie` is not in this bearer transport's CORS scope.

Backend `CORS_ALLOWED_ORIGINS` is a comma-separated list of exact origins, trimmed and empty entries removed. If absent, **APP_ENV=local only** defaults to:

- `http://localhost:5173`
- `http://127.0.0.1:5173`
- `http://localhost:5174`
- `http://127.0.0.1:5174`

Outside local, the unset default is an empty allowlist. The root `.env.example` explicitly lists local origins; production must replace them with deployed HTTPS frontend origins, e.g. `CORS_ALLOWED_ORIGINS=https://app.example.com`. An explicit empty value disables cross-origin access. Origin values contain scheme/host/port, no paths or trailing slash. Rebuild/clear Laravel's configuration cache through the normal deployment process after changes. CORS is a browser response-access policy, not a replacement for Sanctum authorization; nonbrowser requests still require valid tokens. With a single allowed origin the CORS library can emit that constant origin even on foreign-origin requests; the browser rejects the mismatch.

`frontend/.env.example` declares the currently unused public build-time contract:

```dotenv
VITE_API_BASE_URL=http://localhost:8000/api/v.0
```

Copy to ignored `frontend/.env.local` when preparing integration. Use the browser-reachable backend base including the version prefix, without trailing slash; change the host/port for the deployment and restart Vite/rebuild after changes. No component hardcodes an API origin and no code consumes this variable yet. No Vite proxy is required by the chosen direct bearer/CORS approach.

The API's stateful Sanctum middleware stays disabled; SPA cookies, `credentials: include`, and the CSRF-cookie handshake are **not** the selected React integration path. The `web` fallback remains for existing Laravel behavior. No stateful-domain/session setting was changed. Local nginx probes confirmed allowed preflight (204), readable unauthenticated `/me` (401 JSON), and lack of CORS permission for an unrelated origin. Production-origin behavior also has automated coverage.

Social login is not interchangeable with password API login: its API callback lacks a usable token handoff and uses session login on the API group. The repository also includes a freshly randomized password in `firstOrCreate` lookup criteria, making repeated provider sign-ins unreliable. Defer social integration pending its own contract/security review. Cycle 9A removed public registration privilege assignment through an explicit signup allowlist and server-assigned `is_admin = false`; see resolved S01 in the gap register.

## Screen-to-endpoint matrix

All existing endpoints in this matrix require **A**. Request and DTO details follow the matrix; `none` means no existing endpoint, not a proposed implemented route.

| Screen / required frontend data | Existing endpoint and method | Request | Backend result | Expected frontend shape / mismatch |
| --- | --- | --- | --- | --- |
| All authenticated headers: account/pet | `GET /me` | None | `Envelope<{user:{id,name,email},pet:{id,user_id,name}\|null}>` | User and Pet IDs/name available; avatar/media remains deferred; I01 resolved |
| Discovery: basic pet cards and featured details | `GET /pets`, `GET /pets/{petId}` | None | `Envelope<Pet[]>` / `Envelope<Pet>` | `FeaturedDiscoveryPet`/`NearbyPet`: name, breed, age, images, about partly derivable; no featured ranking, owner name, city, distance, traits, companion or pet verification |
| Discovery: breed/species labels | `GET /races`, `GET /species` | None | Bare `Race[]`, `Species[]` | Join numeric IDs, not label/slugs; race list can be filtered by `species_id` locally |
| Discovery: current coordinates | `GET /locations` | None | `Envelope<Location[]>`, current user only | UI has city string; no preferred/current location selection or city field |
| Discovery: nearby cards/distance | `POST /locations/nears` | Optional `radius_km`, `page`, `per_page`; requester/location derived server-side | Paginated normalized nearby-card envelope; see `DISCOVERY_RESPONSE_CONTRACT.md` | Stable owner/pet/taxonomy IDs, age, race, images, about, numeric `distance_km`; unsupported traits/verification/city remain absent |
| Discovery: selected filters/results | `GET /filters`; `POST /locations/filters` | None; nested legacy filter object | Generic filter catalog; broken filtering path | UI species/size/energy/personality do not correspond to catalog or legacy age/race/color filter |
| Discovery: like / dislike actions | `POST /likes`, `POST /dislikes` | `{from: petId,to: petId}` | `Envelope<Like>` / `Envelope<Dislike>` on first valid create | No current UI server action; requires authoritative IDs and relationship semantics/security fixes |
| Discovery: prior selections | `GET /likes`, `GET /dislikes` | Optional `page` | `Envelope<Paginator<RelationWithPets>>` | Pages of outgoing relationships; neither incoming likes nor defined saved favorites |
| Discovery/header and profile: matches | `GET /matches`, `GET /mismatches` | None | `Envelope<MatchWithPet[]>` / `Envelope<Match[]>` | Active directional rows can supply a count/target pet; require current pet and deduplication policy |
| Discovery/account blocking | `GET /blocks`, `POST /blocks` | None; `{to:userId,cause?,why?}` | `Envelope<Block[]>` / `Envelope<Block>` | User IDs, not card Pet IDs; exclusion enforcement incomplete |
| Pet Creation: select options | `GET /species`, `GET /races` | None | Bare arrays | Replace static species/breed values with ID-backed options only after an approved integration |
| Pet Creation: form save | `POST /pets` | Multipart pet fields; see below | `Envelope<Pet>` | Cannot faithfully submit current schema: missing DB-required fields, inconsistent image contract, unsupported traits/preferences |
| Pet Creation / Own Profile: later editing | `PUT/PATCH /pets/{petId}` | Validated partial pet fields | `Envelope<Pet>`; owner policy | No additional UI implied; unsupported profile fields still cannot persist |
| Own Profile: animal details/gallery | `GET /pets/{petId}` + `GET /races` (or race detail) | None | Raw pet and breed | `OwnPet` partly derivable; pet ID and minimal owner identity come from `/me`; media still requires normalization |
| Own Profile: location | `GET /locations` | None | Own coordinates only | No city label; do not fabricate San Diego from coordinates |
| Own Profile: statistics | `GET /matches` only for matches | None | Active directional match rows | No views or favorites statistic; cannot call outgoing likes “Favorites” without a product decision |
| Messaging: conversation summaries | `GET /conversations?page=1&per_page=20` | Authenticated User; optional page/per_page | Paginated envelope of conversation summaries | Both User/Pet participants, last message and receiver-scoped unread count are explicit |
| Messaging: active thread | `GET /messages?receiver_id={userId}&page=1&per_page=30` | Receiver User ID; optional page/per_page | Paginated envelope of normalized message rows | Bidirectional; stable IDs/content/seen state/ISO timestamps; page 1 is the newest window and each page is chronological |
| Messaging: send | `POST /messages` | `{receiver_id:userId,content:string}` | `Envelope<Message>` | Raw sent message can map to `ChatMessage`, but authorization and reload/read path must be corrected first |
| Messaging: matched pet/details | `GET /matches` + pet/race reads | None / known IDs | Target pet in `to_pet`, match timestamp | Partial breed/age/sex/photo/matched date; owner identity, interests/playdate metadata absent |
| Messaging: read state | `PUT /conversations/{conversationId}/seen` | No body; authenticated participant is derived server-side | `Envelope<{conversation_id,marked_count,unread_count}>` | Clear the selected conversation badge only after success; refresh from `GET /conversations` remains authoritative |
| Own Profile: Plus | none | — | No subscription/payment implementation found | Presentational plans remain local/deferred |

### Additional route inventory and stubs

`Route::resources` registers full resource routes, including `/create` and `/{id}/edit`; registration is not proof of an implemented API action.

| Resource | Implemented actions beyond matrix | Unusable registered actions |
| --- | --- | --- |
| Pets | `DELETE /pets/{id}`, `PUT /pets/restore/{id}` → `Envelope<boolean>`, owner only | `GET /pets/create`, `GET /pets/{id}/edit`: absent methods |
| Locations | `POST /locations` latitude/longitude; `GET /locations/{id}`; delete/restore → boolean envelope; ownership enforced | PUT/PATCH update is empty; create/edit methods absent |
| Species / races | POST, PUT/PATCH, DELETE, PUT restore exist; **admin middleware since Cycle 9A** | create/edit methods absent; taxonomy writes are not required by these screens |
| Likes / dislikes | GET list and POST store only | create/show/edit/update/delete methods empty; no usable unlike/delete endpoint |
| Messages | PUT/PATCH content update, DELETE, PUT restore; sender policies | create method empty, show/edit absent |

The language routes (`GET /preferences/langs`, `GET /preferences/langs/current`, `PUT /preferences/langs/{lang}`) also exist under auth. They return an envelope with locale list/current locale/null respectively; `LangService::set` only changes the current application's locale, not a persisted user preference. They do not supply Discovery filters or profile preferences. Blade/admin routes are separate and are not substitutes for missing API endpoints.

## Backend request schemas and response shapes

### Pet, species and race

```ts
// Raw read shapes, with timestamps/deleted_at omitted here for readability.
type PetRow = {
  id: number; user_id: number; species_id: number; race_id: number;
  name: string; age: number; sexe: number; color: string; about: string;
  images: unknown; // JSON cast is declared; actual write path stores a scalar path.
};
type SpeciesRow = { id: number; name: string; description: string | null };
type RaceRow = { id: number; species_id: number; name: string };
```

Pet index/show do not eager-load owner/species/race. They return all active pets or one pet without a read ownership policy, pagination, filters, ordering guarantee or counts. The empty [Pet resource](../../app/Http/Resources/Pet.php) is not used by these controllers. `GET /races/{id}` eager-loads `species`; a missing race returns null from the repository, producing an empty 200 response rather than JSON null or a 404. Species show uses `findOrFail`.

Pet Store validates exactly: `species_id:required|integer`, `race_id:required|integer`, `name:required|min:3|max:25`, `age:required`, `images:required`, `images.*:image|size:1024`. It does not validate taxonomy existence/cross-species consistency, numeric age limits, or the other fields. The controller uses all request fields, overrides `user_id`, then uploads `file('images')` as **one** UploadedFile. Model fillable fields are `user_id,species_id,race_id,name,age,sexe,color,images,about`.

The pets migration makes **sexe, color and about non-null without defaults**, despite their absence from Store validation and from the React creation form. A payload consisting only of the advertised validated fields can therefore fail at insertion. `sexe` convention is 1 female / 0 male. Do not invent values for fields absent from the mockup.

Pet Update permits the same fields with `sometimes`, plus `sexe,color,about`; uses validated data and removes `user_id`. Ownership is checked before update/delete/restore. Image upload has the same mismatch as create. Do not assume file uploads through every PUT/PATCH transport work without a later transport test.

Taxonomy Store/Update only validate `name` (required, max 50; uniqueness on species Store and race Store/Update). Race `species_id` is fillable and DB-required but unvalidated; Species `description` is fillable and unvalidated. Cycle 9A restricts these global API mutations to administrators via the existing admin middleware. Use only read endpoints for frontend options.

### Pet Creation field compatibility

| React `PetProfileValues` | Backend mapping | Difference |
| --- | --- | --- |
| name: trimmed nonempty string | `name` | Backend minimum 3, maximum 25; frontend accepts shorter/longer |
| species: Dog/Cat/Other | `species_id` | Requires catalog ID; do not hardcode numeric values or assume “Other” exists |
| breed: nonempty display string | `race_id` | Requires race ID belonging to selected species; not arbitrary text |
| age: digit string (UI options 0–30) | `age` | Convert to number; backend only checks presence, DB tiny integer; schema itself has no frontend upper bound |
| size: Small/Medium/Large | none | No persistence field |
| traits: array of six allowed labels | none | No trait model/field; presentation labels/icons are not backend enums |
| energy: Low/Moderate/High energy | none | No field; Discovery's “Medium” also differs from form wording |
| playdate: Active play/Gentle play/Relaxed walks | none | No preference field |
| photo: nullable JPG/PNG File, max 10 MiB | `images` | Frontend optional vs required; singular file vs wildcard-array validation; backend `size:1024` is **exactly** 1024 KiB for each file, not a maximum |
| no UI field | `sexe,color,about` | Required by database but not form/Store rules |
| no client owner field | `user_id` | Correctly supplied by server; never trust client ownership |

Array uploads satisfying `images.*` reach `setFile(UploadedFile)` with an array and fail. A scalar upload has no wildcard children to validate and bypasses those image/size checks. `ImageTrait::upload` stores `uploads/<original filename>` on the public disk and returns a scalar path (or false); its random `setName()` is unused. Names can collide across accounts. Do not design a client workaround around this validation bypass.

The intended public URL is the backend storage base (`APP_URL/storage`) plus stored path, subject to the storage link/deployment. Relative paths must not resolve against the Vite origin. Normalize verified media to `ReferenceAsset` only after the backend decides one-image versus gallery representation; no stock replacements or invented gallery entries.

### Locations and Discovery filters

`POST /locations`: `{latitude:number[-90,90],longitude:number[-180,180]}`; controller supplies account `user_id`. Raw response is `Envelope<{id,user_id,latitude,longitude,created_at,updated_at,deleted_at}>`. Decimal coordinates are uncast and may serialize as strings. List/show are owner-scoped; update is a no-op. Multiple locations can exist per user; no current/default flag is present.

`POST /locations/nears` is now the normalized nearby Discovery read contract. Requester identity and origin coordinates are server-derived; the client may provide only validated radius/pagination inputs. Results are visibility-filtered, deterministically ordered, paginated, and expose stable identifiers/card fields. See [DISCOVERY_RESPONSE_CONTRACT.md](DISCOVERY_RESPONSE_CONTRACT.md) and [NEARBY_DISCOVERY_SECURITY.md](NEARBY_DISCOVERY_SECURITY.md). The legacy shape below is historical and must not be used for new frontend integration:

```ts
type NearPet = {
  user_name: string;
  pet_name: string;
  pet_sexe: string; // translated Female/Male, not an enum
  race: RaceRow;
  images: unknown;
  distance: string; // e.g. "1.2 km", rounded to 2 decimals
  is_new: boolean;
};
// Response: Envelope<NearPet[]>; no pagination metadata.
```

No pet/user IDs, age, description, city, traits, coordinates or verification flag are returned. `is_new` means creation recency, not verification. Soft-deleted/missing owners, pets or races are not guarded. Distinct `(user_id,distance)` rows can still repeat the same user's pet when multiple coordinates exist. Likes, dislikes and blocks are not excluded.

`POST /locations/filters` reads `{user_id?,filters:{latitude,longitude,perimetre,age:{min,max},race_id},color}` without a FormRequest. This is a description of code access, **not a working request schema**: PetFilter accesses nonexistent `Location.pet`, contains malformed `['filters'['age']]` indexing, reads `race.race_id` instead of `race.id`, combines predicates ambiguously, and returns itself instead of a collection to the resource. This endpoint must be corrected before use.

`GET /filters` returns `Envelope<{id,name,description,is_free,...timestamps}[]>`, a global catalog. It is neither current user selections nor a schema for the UI's four filter groups. Only species filtering could be derived locally from full pet/taxonomy rows today; size, energy and personality data are absent.

### Likes, dislikes, matches and blocks

Create Like/Dislike payload: `{from:currentPetId,to:targetPetId}`. `from` is checked against the authenticated user's pet; `to` is only required/integer (no self, active-target or block rule). Deployed foreign keys can reject nonexistent targets at DB level but do not implement these product rules.

First successful writes return raw `{id,from,to,created_at,updated_at,...}` under `data`. List endpoints use 10-item Laravel pagination:

```ts
// Envelope.data, not the top-level envelope:
type RelationshipPage = {
  current_page: number; data: Array<{
    id: number; from: PetRow | null; to: PetRow | null;
    created_at: string; updated_at: string; deleted_at: string | null;
  }>;
  per_page: number; total: number; last_page: number;
  first_page_url: string; last_page_url: string;
  next_page_url: string | null; prev_page_url: string | null;
  path: string; from: number | null; to: number | null; links: unknown[];
};
```

The `from`/`to` relation names collide with scalar attributes. Eloquent merges loaded relations after attributes, so list rows expose **pet objects**, while creates expose numeric IDs. This needs deliberate DTO normalization. The paginator's own `from/to` are item offsets, a third unrelated meaning.

LikeRepository's duplicate branch lacks a return despite `?Like`; repeated active likes can raise a return-type error rather than return idempotent success. Dislikes are not deduplicated. A new Like checks for the reverse Like and creates two match rows transactionally, then broadcasts and queues mail. Dislike removes the outgoing like and both directional matches when the reverse like exists. The list controllers dereference `user.pet` without a no-pet guard.

`GET /matches`: `Envelope<Array<{id,from:PetId,to:PetId,to_pet:PetRow|null,created_at,updated_at,deleted_at}>>`. Only rows originating from the current pet are returned; do not divide the count by two. `/mismatches` returns soft-deleted originating rows without `to_pet`. There is no direct match-create endpoint or reliable unique-pair constraint.

Block create: `{to:UserId,cause?:integer|null,why?:string|null}`; target must exist and differ from self, origin is assigned server-side. List/create return raw block rows in envelopes. Creating a block soft-deletes conversations between accounts, resolves their pet IDs, and removes one like direction/match pair. It does not delete messages or persistently enforce exclusion in Discovery/Like/Message queries. Block creation alone is not proof that later contact is forbidden.

### Messaging

> **Current contract (Issue #124):** use [MESSAGING_CONTRACT.md](MESSAGING_CONTRACT.md) for conversation-list and thread reads. The legacy analysis below is retained as historical context and must not be used as the active frontend schema.

```ts
type MessageRow = {
  id: number; conversation_id: number; sender_id: number; receiver_id: number;
  content: string; is_seen: boolean | number;
  created_at: string; updated_at: string; deleted_at: string | null;
};
type ConversationRow = {
  id: number; first_user_id: number; seconde_user_id: number;
  created_at: string; updated_at: string; deleted_at: string | null;
};
type ChatRow = {
  conversation_id: number; sender_id: number; receiver_id: number;
  sender_name: string; receiver_name: string;
  sender_profile_pic: ''; receiver_profile_pic: '';
  pet_sender_id: number; pet_receiver_id: number;
  pet_sender_name: string; pet_receiver_name: string;
  pet_sender_profile_pic: string; pet_receiver_profile_pic: string;
  message_created_at: string; // diffForHumans, not an ISO timestamp
};
```

`GET /messages` uses generic Request, not the existing Message Index FormRequest. `receiver_id` is therefore unvalidated; omitting it reaches an integer repository argument and can fail. The query is only `sender_id = authenticated user AND receiver_id = requested user`; it does not retrieve the reverse direction, filter by conversation, impose chronological ordering, check blocks, or use MessagePolicy::view. It paginates at the model default (15), but embeds `new Chat(paginator)` inside a JSON envelope; the collection's `toArray`/`jsonSerialize` produces rows without paginator links/total.

**ChatRow contains no `id`, `content`, `is_seen`, or machine timestamp.** It cannot populate a message bubble, stable key, receipt, date grouping or deterministic ordering. Pet pictures use `images[0]`; a scalar path is indexed as a character rather than a gallery item. Missing pet relations are unguarded. These are server response defects, not client formatting tasks.

`POST /messages` validates only `receiver_id:required|integer|IsAllowed` and `content:required|string|max:1000`. It discards client sender/conversation fields via `validated()`, assigns authenticated sender, and automatically finds/creates the account-pair conversation in either orientation. The conversation ID is not a supported client input. Store returns a raw message with content and timestamps; `is_seen` default may be absent until the model is reloaded.

`IsAllowed` resolves both users' pets, then calls `LikeRepository::isMatch`. Despite that method's name, it checks **only the reverse Like** (receiver pet liked sender pet), not a reciprocal pair or MatchTable. Existing MessageAuthorizationTest intentionally sets up only that one like. It does not check blocks. A soft-deleted blocked conversation can be recreated. Conversation creation lacks a unique pair constraint/atomic get-or-create guarantee.

`PUT/PATCH /messages/{id}` validates content and enforces sender ownership. Cycle 9A limits updates to validated content, protecting sender/receiver/conversation/read state from client reassignment. Create assigns the sender server-side. Delete/restore require the original sender. No dedicated recipient read-marking endpoint exists.

MessageObserver dispatches MessageEvent after creation. Cycle 9A moved message/match/adoption/typing events to private account channels and restricts model payloads; match Pet IDs are resolved to owner User IDs. Issue #128 activates the provider with Sanctum bearer private-channel authorization, a Pusher-compatible transport, React WebSocket subscription, reconnect/resync and message-ID deduplication. HTTP remains the authoritative fallback. See [REALTIME_MESSAGING.md](REALTIME_MESSAGING.md).

## Frontend view models and required adapters

These are the current view-model requirements, not proposed backend schemas:

| View model | Required fields / mapping |
| --- | --- |
| `FeaturedDiscoveryPet` | string ID, name, breed, ageYears, numeric distanceMiles, location, verified, description, ReferenceAsset photo, photoCount, traits, companion. Pet name/age/about/race can map; missing fields must not be fabricated |
| `NearbyPet` | string ID, name, breed, photo, numeric distanceMiles, traits. Convert units only from a reliable value; `km / 1.609344` gives miles, but missing IDs cannot be reconstructed |
| `discoveryContext` | owner name/photo, location string, petCount, matches, messages. Different counts need explicit scopes: all pets ≠ nearby pets; messages badge ≠ total sent messages |
| `PetProfileValues` | name/species/breed/age/size/traits/energy/playdate/photo; compatibility table above is exhaustive |
| `Conversation` | ID, two pets, two human participants, currentOwnerId, optional matchedOn, preview, activityLabel, unreadCount, messages, interests. The DB pair is human; pet avatars are separate presentation data |
| `ChatMessage` | string ID, human senderId, content, ISO-like timestamp, optional read receipt. Requires normalized message response before adapting |
| `OwnPet` / `ProfileOwner` | pet ownerId, ID, name, ageYears, breed, city, biography, gallery, traits; account ID/name/petIds. `about` can supply biography; `user_id` supplies ownerId, not owner name |
| Profile details/stats | age/breed supported; weight/spayed/good-with/activities unsupported. Matches partly derivable; views/favorites absent |
| `PlusPlan` | Static pricing, billing strings, benefits and recommendation. Keep outside API work |

`ReferenceAsset` holds `src:string|null`, alt text, placeholder and optional position. Approved neutral placeholders can remain for missing media; UI icons, badge tones and alt text are frontend presentation choices. They do not justify inventing persisted traits, verification or ownership. Database numeric IDs may be stringified at the view boundary, but user/pet/conversation domains must remain explicit.

## Minimum Discovery calls and Own Profile coverage

There is **no sufficient set of current calls** to faithfully reproduce full Discovery: nearby rows have no join IDs, several displayed fields have no data model, and filters are broken.

With an already known authenticated identity, a limited non-geographic pet listing can use **two reads**: `/pets` + `/races` (add `/species` for dynamic species options). This does not establish proximity, target city, featured ranking, companion, traits or verification. Do not join this list to `/locations/nears` by pet/user names.

With the available `/me` endpoint and a future normalized nearby response containing stable pet/owner IDs and basic card data, the intended minimum path is: identity → `/locations` if coordinates are not already available → one `/locations/nears` request. Save coordinates with `/locations` only on an explicit user action when none exist; do not invent a location. Add taxonomy reads when labels/options are not embedded. Match count needs `/matches`; outgoing selection state optionally needs all relevant `/likes` and `/dislikes` pages. The message badge still needs an unread-summary contract. These are conditional call budgets, not currently working integration code.

Own Profile can eventually reuse identity/current-pet state, read `/pets/{id}`, resolve its race, and read own locations plus `/matches`. That covers basic identity, biography, potential photos and an active-match count. `users.is_verified` exists but is an account property, not proof of pet verification. Pet reads do not eager-load owner details; `/me` now supplies the current owner ID/name/email separately. No city/geocoding contract, rich pet attributes, view counter, favorites semantics, gallery write contract, or subscriptions are available. An all-pets scan filtered by known user ID is technically possible but should not replace a proper current-pet contract, especially while multiple pets per account are unresolved.

## Recommended integration order and gates

1. **Preserve the completed security boundaries.** Cycle 9A resolved S01/S03/S05/S06; upload validation/storage (S02), contact/block policy (S04) and proximity scope (S07) remain gates for the affected features. Do not re-enable those unsafe writes as part of auth UI work.
2. **Integrate password bearer auth + authoritative identity next.** Cycle 9B now provides `/sign-in` token/type, `/me` identity, current-token `/sign-out` and direct browser CORS. No backend-contract blocker remains for an auth-only first React request in the verified local setup. The next cycle still must agree token storage/lifecycle, consume VITE_API_BASE_URL, implement auth state and handle 401/no-pet/error envelopes. No client code exists yet. Social auth remains deferred.
3. **Read taxonomy, then pet/profile.** Species/races must precede pet creation because creation requires their IDs. Establish current-pet ownership/cardinality, no-pet handling, upload shape and required-field decisions first. Basic existing-profile reads can proceed before creation once identity is known.
4. **Locations.** Define which coordinate is current and how a city label is represented. Resolve radius spelling, ownership scope and nullable relationships before proximity queries.
5. **Read-only Discovery.** Normalize nearby IDs and required fields, then adapt stable card data and supported filters. Explicitly defer unsupported traits/preferences/companion metadata rather than manufacturing data.
6. **Likes/dislikes/matches/blocks.** Resolve idempotency, target validation, block enforcement and saved-favorite semantics before mutation; preserve Pet IDs for relationships and User IDs for blocks. Refetch authoritative relationship state after writes when this phase is implemented.
7. **Messaging last.** Requires identity, pet mapping, relationship authorization, conversation listing, bidirectional ordered message content with pagination, safe update semantics and explicit read/unread behavior. Then integrate send; do not treat a successful write as a substitute for a working read contract.
8. **Deferred product capabilities only by separate scope:** Plus/billing, view/favorite analytics, playdates, rich pet preferences, social handoff and real-time delivery.

### Endpoints usable without changing their successful basic contract

Subject to configured transport and valid existing data: password sign-in/sign-out and `/me`; GET pets/list/detail; GET species/list/detail and races/list (race detail has null-not-found caveat); own-location list/show/create/delete/restore; owner-authorized non-image pet update/delete/restore; outgoing relationship lists and matches/mismatches when the user has a pet; block list. These are useful building blocks, **not** a claim that a whole screen is ready. Taxonomy reads use bare JSON, relationship lists need shape adaptation, and location creation needs an agreed UX source for coordinates.

Do not label registration, pet image creation/update, filtered Discovery, Messaging reads/writes, or relationship mutations production-ready merely because routes exist. Documented response, authorization and data-model gaps precede those integrations. No existing fixture was replaced during this audit.
