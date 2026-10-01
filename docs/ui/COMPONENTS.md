# PetMingle screen inventory and frontend proposal

Analysis only; this document proposes future work. No application code, dependency installation, API integration, route change, or commit is part of this cycle. Reference IDs and visual tokens are defined in [DESIGN_SYSTEM.md](DESIGN_SYSTEM.md).

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

## Reusable component boundaries

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

## Proposed source architecture (not created)

```text
frontend/
  package.json
  package-lock.json
  .gitignore
  index.html
  vite.config.ts
  tsconfig.json
  tsconfig.app.json
  tsconfig.node.json
  public/                       # separately supplied static frontend assets
  src/
    main.tsx
    app/
      App.tsx
      router.tsx
      providers.tsx             # QueryClientProvider; no network requests
      layouts/
        PublicLayout.tsx
        DiscoveryLayout.tsx
        ConversationLayout.tsx
        PetAccountLayout.tsx
    styles/
      tokens.css                # semantic PetMingle tokens
      app.css                   # Tailwind entry + scoped base styles
    assets/
      brand/
      pets/                     # approved standalone assets, never /design copies
    components/
      brand/
      navigation/
      controls/
      pets/
    features/
      landing/
        LandingPage.tsx
        components/
      discovery/
        DiscoveryPage.tsx
        components/
        types.ts
      profile-creation/
        CreatePetProfilePage.tsx
        components/
        schema.ts
      messages/
        MessagesPage.tsx
        components/
        types.ts
      pet-profile/
        PetProfilePage.tsx
        components/
      plus/
        components/             # embedded on profile, no new Plus page
        types.ts
    fixtures/
      landing.ts
      discovery.ts
      profile-creation.ts
      messages.ts
      pet-profile.ts
    types/
      pet.ts                    # UI model only, no assumed API response shape
    test/
      setup.ts
      render.tsx
```

Tests should live beside the features/components they verify. Fixtures remain screen-specific where source data conflicts; common pet display types can still be reused. Do not encode screenshot text directly throughout component markup. Feature components may import shared components/types; shared components should not import page features.

Proposed frontend-only routes: `/` → M1; `/explore` → M2; `/pets/new` → M3; `/messages` → M4; `/profile` → M5. Only `/explore` is evidenced by a reference URL. These paths run on a separate frontend origin initially; they do not change Laravel routing. No fabricated pages for Stories, Resources, authentication, Matches, or purchase flows are part of the five-screen scope.

Use local React state for visible selections and fixture-driven interactions in a later implementation. React Hook Form plus Zod owns the creation form; only visible requirements should become validation constraints. TanStack Query is included in the requested stack, but no endpoint hooks, services, fetch calls, Axios client, or socket connection should be added yet. React Router owns navigation between the five agreed screens. Motion is available for restrained transitions when behavior is specified; static mockups do not justify a new animation system.

Later verification: desktop comparison against each local reference, meaningful Testing Library tests for implemented selection/form/navigation behavior, TypeScript checks, and production build. Exact fonts and source imagery must be resolved before claiming visual parity. No application tests or builds are necessary for these documentation-only changes.

## Exact proposed npm package list

This is the complete proposed direct package-name list for a future standalone `frontend/package.json`, not an installation instruction. Nothing has been installed. Version pins remain to be resolved together against engine/peer requirements at initialization and then locked in the frontend lockfile; no unverified exact version numbers are asserted here.

### Runtime dependencies — 10

| Package | Purpose |
| --- | --- |
| `react` | UI runtime |
| `react-dom` | Browser rendering |
| `react-router` | Declarative routing for the five screens |
| `@tanstack/react-query` | Requested query/state infrastructure; API use deferred |
| `react-hook-form` | Profile creation form state |
| `@hookform/resolvers` | Zod / React Hook Form bridge |
| `zod` | Form validation and inferred types |
| `motion` | Requested Motion for React package |
| `lucide-react` | Ordinary interface icons |
| `clsx` | Small conditional class utility for explicit variants |

### Development dependencies — 12

| Package | Purpose |
| --- | --- |
| `typescript` | Type checking |
| `vite` | Isolated dev server and build |
| `@vitejs/plugin-react` | React Vite integration |
| `@types/react` | React types |
| `@types/react-dom` | DOM-renderer types |
| `@types/node` | Build/config Node types |
| `tailwindcss` | Requested styling tooling |
| `@tailwindcss/vite` | Tailwind Vite integration |
| `vitest` | Test runner |
| `@testing-library/react` | Component behavior tests |
| `@testing-library/jest-dom` | DOM assertions for Vitest |
| `@testing-library/user-event` | User interaction simulation |

### Test environment dependency — 1 (also a devDependency)

| Package | Purpose |
| --- | --- |
| `jsdom` | Browser-like DOM test environment |

Total: **10 runtime + 13 development = 23 direct packages**. The test environment is separated above only for clarity, not a separate manifest section.

Use the Vite Tailwind plugin; no extra PostCSS/Autoprefixer package is proposed. Use `react-router` directly for the documented declarative installation; no duplicate `react-router-dom` dependency is proposed. Use `motion` (imports from `motion/react`), not a second `framer-motion` package. No Bootstrap, Vue, large UI framework, Radix/shadcn component collection, state store, API client, payment SDK, or icon pack beyond Lucide is proposed for this frontend. Existing root dependencies remain untouched. No additional lint/format tooling is silently included.

Package-choice references checked during this analysis: [Vite guide](https://vite.dev/guide/), [Tailwind Vite installation](https://tailwindcss.com/docs/installation/using-vite), [React Router declarative installation](https://reactrouter.com/start/declarative/installation), [Motion React installation](https://motion.dev/docs/react-installation). These support the integration/package choices, not a claim that every future version combination has already been tested.

## Cycle 1 completion boundary

Deliverables are only `docs/ui/DESIGN_SYSTEM.md` and `docs/ui/COMPONENTS.md`. No `RESPONSIVE_RULES.md`, React initialization, page components, frontend folder, package changes, backend changes, API calls, design assets, or commits are included. Outstanding visual/product ambiguities are listed in the design system document; they remain explicit rather than being resolved through invented screens or behavior.
