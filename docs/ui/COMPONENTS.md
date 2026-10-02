# PetMingle screen inventory and component architecture

Updated through Cycle 7. The screen inventory records the original mockups; the implemented architecture section describes the current five-route frontend. No Laravel/API/mobile integration has started. Reference IDs and visual tokens are defined in [DESIGN_SYSTEM.md](DESIGN_SYSTEM.md).

## Repository findings and safest location

- `composer.json` declares Laravel `^13.0`. `routes/api.php` groups the existing REST routes under `v.0`, including authenticated Sanctum routes. The supplied API base is `/api/v.0`; it remains untouched.
- Root `package.json` / `package-lock.json` belong to the existing Laravel Mix pipeline and include Vue 2, Bootstrap 5, Sass, and other legacy tooling. `webpack.mix.js` emits `resources/js/app.js` and `resources/sass/app.scss` to `public/js` and `public/css`.
- `resources/views` contains public, authentication, admin, and email Blade views. `routes/web.php` already owns `/`, `/search`, authentication, and content paths. Existing compiled assets and third-party assets live in `public/`.
- Recommended location: **repository-root `frontend/`**, an independent React/TypeScript Vite application with its own package manifest, lockfile, config, styles, assets, tests, and `dist/`. This isolates build output and CSS reset from the existing Laravel site.
- Do not replace root npm scripts, reuse legacy global CSS, introduce an npm workspace conversion, write React bundles to Laravel `public/`, edit Blade layouts, or alter any Laravel routes/controllers/configuration in this phase.
- A future Vite development origin can serve the proposed frontend routes without claiming Laravel's `/` path. No API proxy, client, authentication handshake, websocket connection, or environment secrets are needed for the visual implementation. Production SPA routing/hosting is a later decision, not a reason to add a Laravel fallback now.
- `/design/` is already ignored by the user's uncommitted `.gitignore` change. All five PNGs pass `git check-ignore`; no design files are tracked. Preserve that existing edit. Root `/node_modules` ignores only the root directory: when initialization is later authorized, add a frontend-local ignore file for `node_modules/`, `dist/`, coverage, and local environment files. Do not create it in this analysis phase.
- No applicable `AGENTS.md` was found in the project or its direct ancestor chain. Observed local tooling: Node `v22.23.1`, npm `9.2.0`; package compatibility must be checked before a later installation.

## Screen-by-screen inventory

### M1 — Public landing page

Purpose: introduce pet friendship/discovery and encourage signup or exploration.

Regions: branded public header; full-width photographic hero with large left-aligned headline and two actions; overlapping white container with three pastel benefit tiles; horizontal four-step explainer; six compact featured pet cards; pale-blue closing CTA with paw/high-five artwork.

Navigation: Home active, Explore, How It Works, Stories, Resources; search icon; Sign In; Get Started. Actions: Get Started (header, hero, closing banner), Explore Pets, See more pets, six heart affordances. No form is visible.

Pet content: Nala, Mochi, Toby, Luna, Simba, Buddy; cards have landscape crop, name, breed, outlined pink heart. Benefit tiles: Make Friends, Explore Together, Share the Joy. Process: Create a profile → Discover pets → Match & chat → Plan playdates, with numbered disks, icons, and light dashed connectors.

Visible state: public session and Home selection; no explicit saved, loading, or empty state. Do not add testimonials, footer, or other marketing sections beyond the image.

### M2 — Discover / Explore

Purpose: browse nearby pets, inspect a featured pet, and narrow results.

Regions: utility header; upper left navigation; central page title/count and featured pet; tall right filter panel; lower results grid spanning both left and center areas. The filter panel stays a separate right column.

Navigation: location selector (San Francisco), search input, notification with pink dot, Sarah account control; sidebar Discover active, Matches 12, Messages 3, Profile, PetMingle Plus.

Featured pet: Nala, Golden Retriever, 3 years, verified mark, 1.2 miles away; large dog/cat image with Featured badge, arrows, 1/5 overlay; biography; six traits; “Lives with Mochi” linked companion row; Save and Say Hello actions.

Forms/actions: search; distance select (“Within 10 miles”); Species (All/Dogs/Cats/Other), dog size, Energy Level, Personality chip groups; Clear All; Show 127 Pets; distance sort; grid/list toggle (grid selected); gallery arrows; save hearts. Filter options visible include size All/Small/Medium/Large/Extra Large, energy All/Low/Medium/High, personality All/Playful/Calm/Friendly/Independent/Adventurous.

Cards: six nearby pets (Toby, Luna, Simba, Buddy, Mochi, Bella), each with photo, top-right heart, name, breed, distance, and two trait pills. Visible state: Discover selected, All selected in each chip group, matching count 127, featured/verified pet. No expanded selectors or alternative list layout are supplied.

### M3 — Create pet profile

Purpose: collect a pet photo, basic information, personality, and playdate preferences.

Regions: public header; left introduction and vertical numbered steps with bottom pet cutout; right stacked photo, basic information, personality, and preference panels. Pet Info is highlighted in the stepper; Personality, Preferences, Review are upcoming visually, even though the first two appear in the form.

Photo section: existing Nala preview with camera button; dashed upload target; JPG/PNG max 10MB hint; three green-check photo-quality tips. Basic fields: Pet name Nala, Species Dog, Breed Golden Retriever, Age 3 years, Size Large (50+ lbs). All five have required markers. Name is a text field; others have select chevrons.

Personality choices: Playful selected blue; Friendly, Outdoorsy, Gentle, Curious, Social in soft pink/blue treatments. Preferences: Energy level High energy; Ideal playdate type Active play. Actions: change/upload photo, choose traits, edit fields, Continue, Save and finish later. No submission result, validation error, progress completion, or review screen is provided.

### M4 — Match & Chat

Purpose: talk with a matched pet's owner and plan a meeting.

Regions: authenticated horizontal header; left conversation list; center selected conversation; right pair context, shared interests, playdate actions, and safety advice.

Navigation: Home, Explore, Match & Chat active, Stories, Resources; search, San Diego selector, unread notification dot, owner avatar dropdown. Left panel: search, All/Matches/Unread tabs, paired avatars, conversation names, previews, dates/times, selected pink Nala & Milo row with unread count 1. Additional pairs: Luna & Toby, Simba & Mochi, Buddy & Bella, Daisy & Coco, Charlie & Olive, Max & Willow, Rocky & Zoe.

Center: paired avatars, Nala & Milo, “Matched March 28, 2024,” video/phone/more actions, Today divider, four alternating bubbles with avatars/times and outgoing double checks. Composer: image attachment, emoji, empty message field, round pink send button.

Right: Nala and Milo photo cards with gender, breed, age; three illustrated shared interests; View all links; Suggest a Time, Pick a Park, Group Playdate; four safety tips. Visible state: selected thread, All tab active, unread indicators, sent/read-like receipts. Exact receipt semantics and actual call/scheduling capabilities are unspecified.

### M5 — Own profile and PetMingle Plus

Purpose: manage the current pet profile and compare premium durations.

Regions: header with Home selected, Messages button, Nala pet-account control; wider left profile column; narrower, tall premium panel on right.

Profile: large photo with camera action; Nala and pencil edit; age/breed/location; three stats (128 Matches, 342 Profile views, 56 Favorites); Complete your profile action; four photo thumbnails and dashed Add Photo tile. About Nala with pencil, biography, four traits. Details with pencil and two-column definition list: age, breed, weight, location, spayed/neutered, compatibility, favorite activities.

Plus: crown and branded title, introduction, four feature rows (Advanced Filters, Boost Profile Visibility, See Who Liked Your Pet, Unlimited Rewinds); three radio-like duration cards; annual selected with Most Popular/Save 33%; Try PetMingle Plus action and cancellation reassurance. Copy and prices are reference content, not verified subscriptions.

Actions/forms: profile/photo edits, add photo, complete profile, Messages, account dropdown, three-choice plan selection, upgrade CTA. No edit modal, checkout, photo management dialog, or actual purchase flow is provided.

## Reference component families (analysis inventory)

Prefer named PetMingle components with a small number of explicit variants. Reuse repeated anatomy without forcing these differing screens into one universal card or shell.

| Component / family | References | Responsibility and visible variants |
| --- | --- | --- |
| `PetMingleLogo` | M1–M5 | Supplied brand artwork; no logo recreation with ordinary text/icons |
| `PetMingleHeader`, `PrimaryNav` | M1, M3, M4, M5 | Public, conversation, pet-account header compositions; explicit nav items/active item |
| `DiscoveryHeader`, `DiscoveryNav` | M2 | Location/search/owner header and upper-left sidebar; reuse header controls |
| `LocationSelector`, `SearchField`, `NotificationButton`, `AccountMenuTrigger` | M2/M4; search M1–M4 | Repeated control geometry; owner/pet identity variant; dropdown contents unspecified |
| `PetMingleButton`, `IconButton` | M1–M5 | Primary pink, outlined blue, soft, save-pink, text variants; only reference icons |
| `PetAvatar`, `PetPairAvatar` | M2–M5; pairs M4 | Circular single pet / overlapping pair; account identity distinct from pet data |
| `PetPhoto`, `PhotoAction` | M1–M5; edit M3/M5 | Crop/focal point, radius, alt text; camera overlay where shown |
| `PetSummaryCard` | M1/M2/M4 | Explicit featured-strip, nearby-result, match-context variants; differing metadata and save placement |
| `SavePetButton` | M1/M2 | Compact heart on card or labeled Save pill; do not infer persistence |
| `PetIdentity`, `PetLocation` | M2/M4/M5 | Name, breed, age, optional verified/gender/distance display; size variants |
| `PetTraitBadge`, `PetTraitList` | M2/M5 | Display-only trait pills with explicit tone/icon; M3 uses separate selectable control |
| `PetPhotoUpload` | M3/M5 | Full upload target vs compact Add Photo slot; camera trigger reused separately |
| `SectionHeading`, `EditSectionAction` | M1–M5; edits M5 | Consistent title alignment, optional supporting text/action; no automatic wrapper card |
| `ChoiceChipGroup` | M2/M3 | Shared selectable semantics; filter outline vs large personality-fill variants; cardinality explicit |
| `FieldFrame`, `PetTextField`, `PetSelectField` | M3; selects M2 | Accessible labels and reference input appearance; form-only icons as shown |
| `IconTextRow`, `Checklist` | M1/M3/M4/M5 | Small repeated icon-title-body / check-copy anatomy; screen-specific spacing |
| `CountBadge`, `StatusDot` | M2/M4 | Unread/count indicators; purpose-specific accessible label |

Screen-owned compositions:

- Landing: `LandingHero`, `BenefitStrip`, `HowItWorksSteps`, `FeaturedPetsStrip`, `JoinCommunityBanner`.
- Discovery: `FeaturedPetPanel`, `PetPhotoGallery`, `CompanionPetRow`, `PetFilters`, `NearbyPetsToolbar`, `NearbyPetsGrid`.
- Profile creation: `ProfileCreationSteps`, `PhotoGuidance`, `PetBasicsFields`, `PersonalityPicker`, `PlaydatePreferenceFields`, `ProfileFormActions`.
- Messages: `ConversationList`, `ConversationListItem`, `ConversationTabs`, `ConversationHeader`, `MessageTimeline`, `MessageBubble`, `MessageComposer`, `MatchContextPanel`, `SharedInterests`, `PlanPlaydateActions`, `SafetyTips`.
- Profile/Plus: `OwnPetSummary`, `PetPhotoStrip`, `PetStats`, `ProfileCompletionPrompt`, `PetAbout`, `PetDetails`, `PlusOffer`, `PlusFeatureList`, `PlusPlanOption`, `PlusPlanSelector`.

Avoid speculative shared components for future screens. Keep plain content sections flat where the reference is flat. A display trait is not an interactive choice; a selected conversation is not a generic pet card. Header variants should be explicit compositions, not dozens of boolean props.

## Implemented architecture — Cycle 7

The five screens are implemented in the isolated `frontend/` application. Earlier screen inventories remain the reference interpretation; this section supersedes the Cycle 1 architecture/dependency proposal.

```text
frontend/src/
  app/              App routes, RouteScroll, navigation regression test
  components/       Shared visual/semantic primitives
  fixtures/         petIdentity.ts (only concordant Nala facts)
  assets/           Typed references for missing original assets
  features/
    landing/        Page, compositions, fixtures, landing.css, tests
    discovery/      Page, compositions, fixtures, discovery.css, tests
    profile-creation/ Page, form components, schema, profile.css, tests
    messaging/      Page, conversations/thread/details, fixtures, messaging.css, tests
    own-profile/    Page, gallery/details/Plus, fixtures, profile.css, tests
  styles/           tokens.css, shared.css, app.css
  test/             jsdom setup
```

Shared components now include:

- `PrimaryNavigation`: public and Messaging link sets; reference-only Home treatment on Own Profile.
- `SiteHeader`: public and pet-identity utilities. `DiscoveryHeader` and `MessagingHeader` retain their source-specific compositions rather than becoming one large configurable shell.
- `Avatar`: common circular image/placeholder behavior for owner, pet and companion contexts. Messaging's pair overlap remains feature-owned.
- `NotificationButton`: common inactive notification control with source-specific sizing.
- `ActionButton` and router-backed `ActionLink`: real actions versus navigation with shared appearance.
- `PetLocation`: optional distance and/or location; no invented distance on Own Profile.
- `PetTraitBadge`, `PetCard`, `ReferenceImage`, `PetMingleLogo`, `SectionHeading`: retained shared primitives.

Header heights remain 81/86/76/82/76px for Landing/Discovery/Creation/Messaging/Profile respectively; this reflects the screenshots, not inconsistent use of one header variant. Shared badge/location CSS is in `styles/shared.css`; the creation choice grid has its own `pet-trait-choices` class. Landing-only layout moved out of the global stylesheet.

Routes: `/`, `/discover`, `/pet/create`, `/messages`, `/profile`. Existing controls connect these routes. Get Started and Complete your profile open the local pet form; they do not register an account or save edits. Discovery links to Messages, Profile and `/profile#petmingle-plus`. `RouteScroll` handles section anchors and route scroll resets. Unknown routes return Home.

Fixtures remain local where references conflict. Nala's name, breed and age agree and share one identity constant; location, owner association and contextual traits are not globally merged. Chat owner accounts use `representedPetId` for avatar association, not an asserted ownership relationship.

## Installed dependencies

The manifest and lockfile are authoritative. No package was added in Cycle 7.

Runtime: `react`, `react-dom`, `react-router`, `clsx`, `lucide-react`, `react-hook-form`, `@hookform/resolvers`, `zod`.

Development: `typescript`, `vite`, `@vitejs/plugin-react`, `@types/react`, `@types/react-dom`, `@types/node`, `tailwindcss`, `@tailwindcss/vite`, `vitest`, `@testing-library/react`, `@testing-library/jest-dom`, `@testing-library/user-event`, `jsdom`.

TanStack Query, Motion, state stores, API clients, chat/payment SDKs and UI frameworks are not installed. Laravel's root dependencies and pipeline remain separate.

See [GLOBAL_UI_REVIEW.md](GLOBAL_UI_REVIEW.md) for consolidation decisions, verification, missing assets and integration prerequisites.
