# Global PetMingle desktop UI review — Cycle 7

> Historical prototype review. For the current API-integrated desktop matrix and corrections, see [DESKTOP_REGRESSION_REVIEW.md](DESKTOP_REGRESSION_REVIEW.md). The runtime, fixture and verification statements below describe Cycle 7 only.

Scope: consolidation of the five implemented desktop references only. No new screen, dependency, Laravel change, API integration, mobile layout or commit. This report supersedes earlier review statements that navigation was unavailable or certain fixtures/styles were screen-owned.

## Audit and visual decisions

All five PNGs were re-inspected and the rendered pages compared at 1448 × 1086. The browser framing in each source remains excluded. Layout differences that exist in the sources were retained rather than normalized into a generic dashboard.

| Screen | Header height | Maximum application width | Retained geometry |
| --- | --- | --- | --- |
| Landing | 81px | 1310px | Wide hero, three benefits, four process steps, six featured pets, closing CTA |
| Discovery | 86px | 1374px | Upper navigation only; lower six-pet grid extends underneath it; right filters |
| Pet creation | 76px | 1312px | Left progress and four stacked panels; two field columns, six choices |
| Messaging | 82px | 1382px | 360/612/370px columns at reference width; composer below scrollable timeline |
| Own Profile | 76px | 1338px | Unequal profile/Plus columns, four thumbnails, three plans |

Typography, control heights, image crops and source-specific spacing are intentionally not identical between these layouts. Rounded navy headings, pink primary actions, blue navigation, subtle card shadows and neutral missing-asset surfaces remain consistent. No new gradients or ornamental elements were introduced.

Concrete drift fixed: the creation choice grid used `.pet-traits`, also used for Discovery's display badges. Because both stylesheets are loaded globally, it changed the Discovery badge arrangement. Creation now uses `.pet-trait-choices`; Discovery again has two natural flex rows, with no form-grid styling. The shared location row preserves Own Profile's larger icon through a scoped size override.

## Final architecture and consolidation

- `app/App.tsx`: five routes; `RouteScroll` handles normal route resets and the existing How It Works / Plus anchors. No network provider or service layer.
- `components/PrimaryNavigation`: the duplicated public/Messaging navigation markup is consolidated with two explicit link sets. Header composition is still screen-specific.
- `SiteHeader`: reused on Landing, creation and Own Profile. Discovery and Messaging keep their genuinely different utility/header arrangements.
- `Avatar`: common circular image/placeholder behavior for human and pet identities, also used by the Messaging pair wrapper and Discovery companion. Context-specific dimensions and overlap remain feature-owned.
- `NotificationButton`: duplicated bell/disabled-control semantics consolidated; badge baseline in shared CSS, source-specific geometry in each feature.
- `PetLocation`: common icon and metadata row, supporting optional distance or location. It no longer requires a fictional distance on Own Profile.
- `ActionLink`: previously unused anchor primitive now uses React Router and the same visual variants as `ActionButton`. Navigation is a link, mutation/submission remains a button.
- Existing `PetTraitBadge`, `PetCard`, `ReferenceImage`, `PetMingleLogo`, `SectionHeading` and form-specific controls remain in use. No universal card/header/form framework was introduced.

No screen component was deleted. Duplicate navigation, notification and avatar implementations were replaced at their call sites. Identity summaries, editing headings, pairs and timeline bubbles remain distinct because their content and geometry differ.

## Tokens and CSS ownership

- Existing palette remains unchanged. Repeated `#ffbf24` is promoted to `--color-accent-gold` for the Discovery Plus star and Own Profile energy icon.
- Recurring 8px/12px corners become `--radius-control` / `--radius-media`. Existing card 10px, panel 20px and pill tokens remain.
- Repeated 8/12/16/24px gaps use the existing spacing aliases. Screenshot-specific offsets and exceptional shapes are not forced onto a new scale.
- Repeated white foreground declarations use `--color-surface`; no local copies of brand colors were added.
- Badge/location styles moved from Discovery into `styles/shared.css`; shared avatars and notification badge styles also live there.
- Landing-only composition rules moved from `styles/app.css` into `features/landing/landing.css`, including its desktop adjustments. The app stylesheet retains imports, base styles and established common primitives.
- Removed the duplicate creation photo-panel padding rule. Renamed the conflicting creation class instead of increasing selector specificity.
- Styles are imported in a stable Tailwind layer order. No blanket resets, new CSS framework or mobile breakpoint were added.

## Navigation decisions

All five screens can now be reached using existing visible controls:

- Get Started → `/pet/create`. This is a pet-onboarding preview, not human account signup.
- Explore / Explore Pets → `/discover`.
- Discovery Messages → `/messages`; Profile → `/profile`; PetMingle Plus → `/profile#petmingle-plus`.
- Own Profile Messages → `/messages`; Complete your profile → `/pet/create`, reusing the existing local pet form rather than inventing an edit screen. This does not imply persistence or a loaded server record.
- Home/logo → `/`; How It Works → `/#how-it-works` with working cross-route scrolling.

Unknown destinations and unimplemented actions remain disabled, including Matches, Stories, Resources, account menus, calls, scheduling, photo editing and purchase. Say Hello does not route to an unrelated existing conversation: matching is not implemented. Discovery's nonfunctional search now uses a disabled input instead of accepting text into an unused state.

Own Profile's Home underline is inconsistent with literal URL-based navigation and may be inherited template styling; the supplied images cannot establish intent. **Decision: preserve the screenshot**, using reference-only styling and no false `aria-current=page` on Home at `/profile`. It still navigates Home. Other current-route indicators remain accurate.

## Accessibility and accidental behavior

Existing icon labels, associated fields, semantic lists, forms, radio groups, headings, image labels and focus outlines were reviewed. Shared decorative notification icons/dots are hidden from accessible naming. Matches count is labelled as matches, not unread messages. Real destinations were changed from disabled buttons to keyboard-reachable links. Unavailable actions use native disabled semantics.

Browser checks found no unnamed button/link/input/select. Keyboard tests exercised radio ArrowRight, thumbnail Enter, route-link Enter and trait Space. The form, conversation selection and composer still use native controls. This is a focused accessibility pass, not a claim of complete WCAG certification.

The local form's debug-style explanation was replaced by the concise status “Pet details are valid.” It reports validation only and never claims persistence. There are no console logs/debug output in application code. The unused Discovery search state and unused Messaging account metadata were removed. Intentional asset placeholders remain visible and explicitly labelled.

## Fixture consistency and unresolved domain questions

`fixtures/petIdentity.ts` centralizes only Nala's concordant ID/name/breed/age: Nala, Golden Retriever, 3. Landing, Discovery, creation, Messaging and Own Profile reuse these facts. Own Profile's owner fixture is explicitly referenced by the pet's owner ID; the account name remains unknown.

Messaging account IDs remain distinct from pet IDs. Its avatar association is now named `representedPetId`, not an asserted ownership field. This avoids implying that one person owns every second pet in the conversation list. Unused account metadata was removed; no human identity was invented.

Reference conflicts deliberately retained:

- Discovery gives Nala/Sarah a San Francisco context; Own Profile and Messaging show San Diego. A canonical location/ownership relationship cannot be derived from these mockups.
- Sarah is named only in Discovery. Other owner identities remain anonymous; Nala in the profile header is a pet identity.
- The same or similar corgi artwork is associated with Toby and Milo; pair thumbnails also differ from some breed cards. No deduplication by image or inferred renaming.
- Display traits differ across contexts without necessarily contradicting one another. Trait selection defaults remain those of creation.
- Messaging's active row initially has unread 1, as shown. Opening it clears that state locally; receipt meanings, initial date labels and other-thread histories remain fixture assumptions described in the Messaging review.
- Plus amounts and Save 33% are screenshot copy, not verified billing rules.

## Missing assets for true fidelity

| Asset group | Still needed |
| --- | --- |
| Brand | Exact PetMingle logo artwork/wordmark source |
| Font | Exact family, licensed local files and weights; current local fallbacks alter text metrics |
| Landing | Dog/cat hero scene, six exact featured-pet crops, high-five cutout |
| Discovery | Featured Nala/Mochi image, any actual gallery alternatives, six nearby-pet images, Sarah portrait and companion crop |
| Creation | Nala preview image and lower dog/cat cutout |
| Messaging | Exact paired pet avatars, Nala/Milo photo cards, owner portrait, three shared-interest illustrations |
| Own Profile | Main Nala portrait plus the four exact thumbnail shots/crops |
| Icons | Original filled paw, pet silhouettes, crown and bespoke illustrated treatments where Lucide differs |

No external assets were fetched or generated. Neutral placeholders preserve proportions; they cannot reproduce photographic color balance or subject placement. The original typeface and illustration assets remain the principal fidelity limitation. Some clipped conversation preview text is deliberate ellipsis, as in the reference; accessibility-only text is deliberately visually hidden. No unintended visible text clipping was detected.

## Verification

- TypeScript passed; production build passed.
- 27/27 tests passed across six files. Original 26 tests retained; one cross-screen navigation regression added. Updated navigation expectations and removed the test's direct assertion of a styling data attribute. Critical photo/validation/message/gallery/plan behavior remains covered. The longer cross-route test has a scoped 15s timeout for the Docker test environment.
- Chromium: all five routes at 1448, 1280, 1120 ×1086 (15 combinations). No runtime or console errors, external requests, HTTP errors, horizontal overflow, visible text overflow or overlapping navigation items detected.
- Full route journey, form validation, local Discovery choice, gallery/plan selection and local message send exercised at every width. Anchor links verified. Additional keyboard checks passed at 1120px.
- Discovery grid still spans beneath the upper navigation; Messaging retains all three columns; creation keeps its form columns; Plus retains all three plans. Narrow desktop pages may scroll vertically.
- Five 1448px captures visually compared with the references; captures and browser scripts remain in `/tmp`. Shared CSS changes were followed by a fresh 15-combination browser check.
- `git diff --check` passed. No package manifest/lockfile, Laravel file or design asset changed. No files were staged or committed.

## Readiness for a future Laravel API cycle

The desktop frontend is a coherent, navigable fixture-backed prototype, with shared primitives and tested local interactions. Feature data boundaries and the existing local form schema provide clear places for future adapters. It is **not connected to Laravel** and does not claim authenticated, persisted, billing or realtime behavior.

Before integration, agree canonical owner/pet IDs and relationships, location scope, actual API response/validation shapes, authorization/session handling, media URLs, message chronology/receipts, error/loading states and which backend actions exist. These are integration prerequisites, not work started in this cycle. Do not treat UI fixtures or reference prices as API contracts. No TanStack Query, Motion, state store, chat SDK, payment SDK or new dependency was installed.

## Final Git snapshot

New untracked files are excluded from the tracked diff stat. The large stylesheet deletion is mostly relocation into the new Landing stylesheet. Nothing was staged.

`git status --short`:

```text
 M docs/ui/COMPONENTS.md
 M docs/ui/DESIGN_SYSTEM.md
 M frontend/README.md
 M frontend/src/app/App.tsx
 M frontend/src/components/Action.tsx
 M frontend/src/components/PetLocation.tsx
 M frontend/src/components/SiteHeader.tsx
 M frontend/src/features/discovery/DiscoveryPage.test.tsx
 M frontend/src/features/discovery/components/DiscoveryHeader.tsx
 M frontend/src/features/discovery/components/DiscoverySidebar.tsx
 M frontend/src/features/discovery/components/FeaturedPetCard.tsx
 M frontend/src/features/discovery/discovery.css
 M frontend/src/features/discovery/discovery.fixtures.ts
 M frontend/src/features/landing/LandingPage.test.tsx
 M frontend/src/features/landing/components/GetStartedButton.tsx
 M frontend/src/features/landing/landing.fixtures.ts
 M frontend/src/features/messaging/components/ChatThread.tsx
 M frontend/src/features/messaging/components/MessagingHeader.tsx
 M frontend/src/features/messaging/components/PetAvatar.tsx
 M frontend/src/features/messaging/messaging.css
 M frontend/src/features/messaging/messaging.fixtures.ts
 M frontend/src/features/own-profile/OwnProfilePage.test.tsx
 M frontend/src/features/own-profile/components/PetProfileSummary.tsx
 M frontend/src/features/own-profile/components/PlusPlans.tsx
 M frontend/src/features/own-profile/profile.css
 M frontend/src/features/own-profile/profile.fixtures.ts
 M frontend/src/features/profile-creation/PetCreatePage.test.tsx
 M frontend/src/features/profile-creation/PetCreatePage.tsx
 M frontend/src/features/profile-creation/profile.css
 M frontend/src/features/profile-creation/profile.schema.ts
 M frontend/src/styles/app.css
 M frontend/src/styles/tokens.css
 M frontend/src/test/setup.ts
?? docs/ui/GLOBAL_UI_REVIEW.md
?? frontend/src/app/Navigation.test.tsx
?? frontend/src/app/RouteScroll.tsx
?? frontend/src/components/Avatar.tsx
?? frontend/src/components/NotificationButton.tsx
?? frontend/src/components/PrimaryNavigation.tsx
?? frontend/src/features/landing/landing.css
?? frontend/src/fixtures/
?? frontend/src/styles/shared.css
```

`git diff --stat`:

```text
 docs/ui/COMPONENTS.md                              | 154 ++-------
 docs/ui/DESIGN_SYSTEM.md                           |  20 +-
 frontend/README.md                                 |  10 +-
 frontend/src/app/App.tsx                           |   4 +
 frontend/src/components/Action.tsx                 |   5 +-
 frontend/src/components/PetLocation.tsx            |   7 +-
 frontend/src/components/SiteHeader.tsx             |  22 +-
 .../src/features/discovery/DiscoveryPage.test.tsx  |   7 +-
 .../discovery/components/DiscoveryHeader.tsx       |  17 +-
 .../discovery/components/DiscoverySidebar.tsx      |  34 +-
 .../discovery/components/FeaturedPetCard.tsx       |   3 +-
 frontend/src/features/discovery/discovery.css      |  42 +--
 .../src/features/discovery/discovery.fixtures.ts   |   3 +-
 frontend/src/features/landing/LandingPage.test.tsx |  10 +-
 .../landing/components/GetStartedButton.tsx        |   6 +-
 frontend/src/features/landing/landing.fixtures.ts  |   3 +-
 .../features/messaging/components/ChatThread.tsx   |   2 +-
 .../messaging/components/MessagingHeader.tsx       |  16 +-
 .../features/messaging/components/PetAvatar.tsx    |   4 +-
 frontend/src/features/messaging/messaging.css      |  34 +-
 .../src/features/messaging/messaging.fixtures.ts   |  11 +-
 .../features/own-profile/OwnProfilePage.test.tsx   |   3 +-
 .../own-profile/components/PetProfileSummary.tsx   |   8 +-
 .../features/own-profile/components/PlusPlans.tsx  |   2 +-
 frontend/src/features/own-profile/profile.css      |  37 +-
 .../src/features/own-profile/profile.fixtures.ts   |   7 +-
 .../profile-creation/PetCreatePage.test.tsx        |   2 +-
 .../features/profile-creation/PetCreatePage.tsx    |   4 +-
 frontend/src/features/profile-creation/profile.css |  31 +-
 .../features/profile-creation/profile.schema.ts    |   3 +-
 frontend/src/styles/app.css                        | 373 +--------------------
 frontend/src/styles/tokens.css                     |   3 +
 frontend/src/test/setup.ts                         |   6 +-
 33 files changed, 239 insertions(+), 654 deletions(-)
```
