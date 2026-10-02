# PetMingle frontend/API contract audit

Cycle 8 · 2026-10-02 · branch `feat/react-api-integration`

## Scope and conclusion

This is a source-code audit, not an integration or a live API certification. The four authenticated React screens still use local fixtures; Landing remains outside the authenticated contract. No application code, configuration, database, dependencies, or fixtures were changed. No migrations, write requests, authentication attempts, or backend tests that reset databases were run.

The existing API supplies useful pet, taxonomy, location and relationship data, but cannot yet populate all four screens faithfully. The first blockers are authenticated identity, cross-origin transport, upload/schema inconsistencies, incomplete Discovery responses, and incomplete Messaging reads. Security findings must be resolved separately before enabling affected writes. [API_GAPS.md](API_GAPS.md) classifies each finding and its integration gate.

Evidence is the checked-in routes, controllers, requests, repositories, models, policies, observers, migrations, configuration, frontend types, and existing tests. Selected framework serialization/validation behavior was checked against the vendor source inside the existing PHP Docker container. Database migration *definitions* were inspected; their deployment state and existing row contents were not queried. Environment overrides, storage links, and live CORS headers were not verified.

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

Most controller successes use HTTP 200 and `Envelope<T>`, including creates. Sign-in is `{success:true,token}`. Species/races return bare models/arrays: reads/updates use 200, newly created models use 201 through Laravel's router. Taxonomy deletes/restores return PHP booleans directly, resulting in a 200 text/html response (`1` or empty), rather than a JSON envelope; these are not frontend integration targets.

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
| `POST /sign-in` | P | JSON `email: required email`, `password: required string` | 200 `{success:true,token:string}`; validly shaped bad credentials → 401; malformed payload → legacy validation |
| `POST /sign-up` | P | `name`, unique email, password 6–50 chars, `password_confirmation`; effectively multipart `avatar` too | 200 `Envelope<User>`; **no token**. Avatar rules are commented out but controller unconditionally requires an UploadedFile |
| `POST /sign-out` | A | No body | 200 `{success:true,message}`; deletes current access token only |
| `GET /login/{provider}` | P | Provider `facebook`, `github`, or `google` | Stateless Socialite redirect |
| `GET /login/{provider}/callback` | P | Provider callback parameters | Auth::login; JSON `Envelope<User>` when requested, otherwise `/home` redirect; no PAT handoff |
| `PUT /remove-avatar/{userId}` | A + self | No body | `Envelope<User>` with removed avatar |
| `DELETE /disable-account/{userId}` | A + self | No body | Success message; revokes user's tokens and soft-deletes user |
| `PUT /enable-account/{userId}` | A + admin | No body | Success message; restores user |

Password sign-in uses `Auth::attempt` through the default `web` guard, then explicitly issues `createToken('api')->plainTextToken`. **The implemented API login contract is Sanctum personal access tokens sent as `Authorization: Bearer <token>`**, with `Accept: application/json`. There is no refresh endpoint, expiry metadata, or user/pet object in sign-in. Checked-in Sanctum expiration is null and this creation call supplies no expiry. [Existing AuthTest](../../tests/Feature/Api/AuthTest.php) explicitly exercises bearer access and revocation; it was inspected, not rerun.

The presence of the `web` fallback in Sanctum does not mean SPA cookie authentication is configured. The API middleware has `EnsureFrontendRequestsAreStateful` commented out and no session middleware. CORS permits wildcard origins/headers/methods in its file but `HandleCors` is absent from the bound HTTP kernel. The checked-in nginx configuration adds no CORS headers and Vite has no proxy. Different localhost ports are different origins: direct browser Vite→Laravel bearer requests cannot be considered supported by the current checked-in setup. CORS middleware/proxy deployment needs a separate decision and verification.

Cookie auth additionally conflicts with `supports_credentials:false`; default stateful domains do not list Vite's 5173/5174 ports. Environment overrides were not read. Neither obtaining a CSRF cookie nor using `credentials: include` alone fixes this configuration. No switch to cookie auth is proposed in this cycle.

Future frontend auth state needs: opaque token (if retaining the existing mechanism), authentication status, authoritative user ID/name/avatar, optional current pet ID, and onboarding/no-pet state. Decide token storage/lifetime separately; never retain passwords or infer IDs from the token. Clear local account-scoped data on sign-out/401. **There is no read-only authenticated identity endpoint** (`/me` or equivalent). Sign-up's returned user does not solve fresh sign-ins or page reloads. Do not use avatar removal, public pet enumeration, or OAuth callbacks as identity lookups.

Social login is not interchangeable with password API login: its API callback lacks a usable token handoff and uses session login on the API group. The repository also includes a freshly randomized password in `firstOrCreate` lookup criteria, making repeated provider sign-ins unreliable. Defer social integration pending its own contract/security review. Public registration also currently mass-assigns `is_admin`; see S01 in the gap register.

## Screen-to-endpoint matrix

All existing endpoints in this matrix require **A**. Request and DTO details follow the matrix; `none` means no existing endpoint, not a proposed implemented route.

| Screen / required frontend data | Existing endpoint and method | Request | Backend result | Expected frontend shape / mismatch |
| --- | --- | --- | --- | --- |
| All authenticated headers: account/pet | none | — | No identity read | Account name/avatar/User ID/current Pet ID; I01 |
| Discovery: basic pet cards and featured details | `GET /pets`, `GET /pets/{petId}` | None | `Envelope<Pet[]>` / `Envelope<Pet>` | `FeaturedDiscoveryPet`/`NearbyPet`: name, breed, age, images, about partly derivable; no featured ranking, owner name, city, distance, traits, companion or pet verification |
| Discovery: breed/species labels | `GET /races`, `GET /species` | None | Bare `Race[]`, `Species[]` | Join numeric IDs, not label/slugs; race list can be filtered by `species_id` locally |
| Discovery: current coordinates | `GET /locations` | None | `Envelope<Location[]>`, current user only | UI has city string; no preferred/current location selection or city field |
| Discovery: nearby cards/distance | `POST /locations/nears` | `user_id`, latitude, longitude, optional `perimetre` | `Envelope<NearPet[]>` | Missing both IDs, age, about; formatted km vs numeric miles; cannot safely join by name |
| Discovery: selected filters/results | `GET /filters`; `POST /locations/filters` | None; nested legacy filter object | Generic filter catalog; broken filtering path | UI species/size/energy/personality do not correspond to catalog or legacy age/race/color filter |
| Discovery: like / dislike actions | `POST /likes`, `POST /dislikes` | `{from: petId,to: petId}` | `Envelope<Like>` / `Envelope<Dislike>` on first valid create | No current UI server action; requires authoritative IDs and relationship semantics/security fixes |
| Discovery: prior selections | `GET /likes`, `GET /dislikes` | Optional `page` | `Envelope<Paginator<RelationWithPets>>` | Pages of outgoing relationships; neither incoming likes nor defined saved favorites |
| Discovery/header and profile: matches | `GET /matches`, `GET /mismatches` | None | `Envelope<MatchWithPet[]>` / `Envelope<Match[]>` | Active directional rows can supply a count/target pet; require current pet and deduplication policy |
| Discovery/account blocking | `GET /blocks`, `POST /blocks` | None; `{to:userId,cause?,why?}` | `Envelope<Block[]>` / `Envelope<Block>` | User IDs, not card Pet IDs; exclusion enforcement incomplete |
| Pet Creation: select options | `GET /species`, `GET /races` | None | Bare arrays | Replace static species/breed values with ID-backed options only after an approved integration |
| Pet Creation: form save | `POST /pets` | Multipart pet fields; see below | `Envelope<Pet>` | Cannot faithfully submit current schema: missing DB-required fields, inconsistent image contract, unsupported traits/preferences |
| Pet Creation / Own Profile: later editing | `PUT/PATCH /pets/{petId}` | Validated partial pet fields | `Envelope<Pet>`; owner policy | No additional UI implied; unsupported profile fields still cannot persist |
| Own Profile: animal details/gallery | `GET /pets/{petId}` + `GET /races` (or race detail) | None | Raw pet and breed | `OwnPet` partly derivable; need pet ID first, normalize media; owner ID exists but owner object absent |
| Own Profile: location | `GET /locations` | None | Own coordinates only | No city label; do not fabricate San Diego from coordinates |
| Own Profile: statistics | `GET /matches` only for matches | None | Active directional match rows | No views or favorites statistic; cannot call outgoing likes “Favorites” without a product decision |
| Messaging: conversation summaries | none | — | No conversation-list endpoint | `Conversation[]`: pair identities, last message/time, unread count and current-owner context missing |
| Messaging: active thread | `GET /messages?receiver_id={userId}&page=1` | Receiver User ID, page | `Envelope<ChatRow[]>` | Only outgoing messages; resource omits message ID, content, raw time and read status; pagination metadata lost |
| Messaging: send | `POST /messages` | `{receiver_id:userId,content:string}` | `Envelope<Message>` | Raw sent message can map to `ChatMessage`, but authorization and reload/read path must be corrected first |
| Messaging: matched pet/details | `GET /matches` + pet/race reads | None / known IDs | Target pet in `to_pet`, match timestamp | Partial breed/age/sex/photo/matched date; owner identity, interests/playdate metadata absent |
| Messaging: read state/playdate action | none | — | No read-receipt or playdate endpoint | Keep deferred; `is_seen` column alone does not implement a secure read action |
| Own Profile: Plus | none | — | No subscription/payment implementation found | Presentational plans remain local/deferred |

### Additional route inventory and stubs

`Route::resources` registers full resource routes, including `/create` and `/{id}/edit`; registration is not proof of an implemented API action.

| Resource | Implemented actions beyond matrix | Unusable registered actions |
| --- | --- | --- |
| Pets | `DELETE /pets/{id}`, `PUT /pets/restore/{id}` → `Envelope<boolean>`, owner only | `GET /pets/create`, `GET /pets/{id}/edit`: absent methods |
| Locations | `POST /locations` latitude/longitude; `GET /locations/{id}`; delete/restore → boolean envelope; ownership enforced | PUT/PATCH update is empty; create/edit methods absent |
| Species / races | POST, PUT/PATCH, DELETE, PUT restore exist; **no admin policy checks** | create/edit methods absent; taxonomy writes are not required by these screens |
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

Taxonomy Store/Update only validate `name` (required, max 50; uniqueness on species Store and race Store/Update). Race `species_id` is fillable and DB-required but unvalidated; Species `description` is fillable and unvalidated. These global mutations have no role authorization in their API controllers. Use only read endpoints for frontend options.

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

`POST /locations/nears`: `{user_id:integer,latitude:number,longitude:number,perimetre?:integer}`. The supplied account ID is used only to exclude a user; it is not constrained to the authenticated user. Repository reads **`perimeter`**, not validated `perimetre`, and defaults to 5 km. It receives all request input, so an unvalidated `perimeter` can override the limit. Results are ordered by spherical distance and eager-load `user.pet.race`:

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

`PUT/PATCH /messages/{id}` validates content and enforces sender ownership, but passes **all** request fields to a model fillable with `sender_id,receiver_id,conversation_id,is_seen`. Thus an authorized sender can mutate participant/conversation fields outside the validated contract. This is distinct from create, which correctly prevents sender spoofing. Delete/restore require the original sender. No dedicated recipient read-marking endpoint exists.

MessageObserver also dispatches MessageEvent after creation; that event broadcasts the public message model on public `new-message`. MatchEvent uses public `new-match`. Actual exposure depends on broadcaster deployment (config default is `null`), but the events need participant authorization before enabling a network broadcaster. No sockets/polling are needed or proposed here.

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

After an approved identity endpoint and normalized nearby response containing stable pet/owner IDs and basic card data, the intended minimum path is: identity → `/locations` if coordinates are not already available → one `/locations/nears` request. Save coordinates with `/locations` only on an explicit user action when none exist; do not invent a location. Add taxonomy reads when labels/options are not embedded. Match count needs `/matches`; outgoing selection state optionally needs all relevant `/likes` and `/dislikes` pages. The message badge still needs an unread-summary contract. These are conditional call budgets, not currently working integration code.

Own Profile can eventually reuse identity/current-pet state, read `/pets/{id}`, resolve its race, and read own locations plus `/matches`. That covers basic identity, biography, potential photos and an active-match count. `users.is_verified` exists but is an account property, not proof of pet verification. Owner details are not eager-loaded by pet reads. No city/geocoding contract, rich pet attributes, view counter, favorites semantics, gallery write contract, or subscriptions are available. An all-pets scan filtered by known user ID is technically possible but should not replace a proper current-pet contract, especially while multiple pets per account are unresolved.

## Recommended integration order and gates

1. **Approve prerequisite backend/security work separately.** Resolve registration privilege assignment, uploads, message mutation/contact authorization and public-event exposure before exposing affected features. Decide cross-origin bearer transport versus a separately designed cookie migration. This audit changes neither.
2. **Password auth + authoritative identity together.** Existing bearer login/logout is the smallest established path; add/agree a safe current-user/current-pet read contract before wiring protected screens. Define unauthenticated, no-pet and invalid-token states. Social auth remains deferred.
3. **Read taxonomy, then pet/profile.** Species/races must precede pet creation because creation requires their IDs. Establish current-pet ownership/cardinality, no-pet handling, upload shape and required-field decisions first. Basic existing-profile reads can proceed before creation once identity is known.
4. **Locations.** Define which coordinate is current and how a city label is represented. Resolve radius spelling, ownership scope and nullable relationships before proximity queries.
5. **Read-only Discovery.** Normalize nearby IDs and required fields, then adapt stable card data and supported filters. Explicitly defer unsupported traits/preferences/companion metadata rather than manufacturing data.
6. **Likes/dislikes/matches/blocks.** Resolve idempotency, target validation, block enforcement and saved-favorite semantics before mutation; preserve Pet IDs for relationships and User IDs for blocks. Refetch authoritative relationship state after writes when this phase is implemented.
7. **Messaging last.** Requires identity, pet mapping, relationship authorization, conversation listing, bidirectional ordered message content with pagination, safe update semantics and explicit read/unread behavior. Then integrate send; do not treat a successful write as a substitute for a working read contract.
8. **Deferred product capabilities only by separate scope:** Plus/billing, view/favorite analytics, playdates, rich pet preferences, social handoff and real-time delivery.

### Endpoints usable without changing their successful basic contract

Subject to authenticated transport and valid existing data: password sign-in/sign-out; GET pets/list/detail; GET species/list/detail and races/list (race detail has null-not-found caveat); own-location list/show/create/delete/restore; owner-authorized non-image pet update/delete/restore; outgoing relationship lists and matches/mismatches when the user has a pet; block list. These are useful building blocks, **not** a claim that a whole screen is ready. Taxonomy reads use bare JSON, relationship lists need shape adaptation, and location creation needs an agreed UX source for coordinates.

Do not label registration, pet image creation/update, filtered Discovery, Messaging reads/writes, or relationship mutations production-ready merely because routes exist. Documented response, authorization and data-model gaps precede those integrations. No existing fixture was replaced during this audit.
