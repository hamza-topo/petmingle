# PetMingle frontend — five-screen desktop prototype

Standalone React/TypeScript/Vite app. Landing, Discovery, Pet Profile Creation, Messaging and Own Profile are implemented from their desktop references. Laravel, its root npm package, Mix pipeline, Blade views, public assets, routes, and API remain separate.

## Run

Use Node 22.12+ (tested on 22.23.1). From `frontend/`:

```sh
npm ci
npm run dev
npm run build
npm test
npm run typecheck
```

The Vite server binds to `127.0.0.1` and uses port 5173 by default. Build output stays in `frontend/dist`. No API proxy, network data client, or Laravel integration is configured. React Router exposes `/`, `/discover`, `/pet/create`, `/messages` and `/profile`; existing controls connect the five screens and unknown paths return Home.

## Implementation

- `src/app/App.tsx`: frontend-only router.
- `src/features/landing/LandingPage.tsx`: header + five small section compositions.
- `src/features/landing/components/`: hero, benefits, process, pet strip, closing CTA, repeated signup button.
- `src/features/landing/landing.fixtures.ts`: typed benefits, steps, and six pets from the reference.
- `src/components/`: action styles, header, logo treatment, reference-image fallback, pet card, section heading.
- `src/styles/tokens.css`: documented colors, typography estimates, spacing, radii, shadow, and width exposed as CSS variables / Tailwind theme tokens.
- `src/styles/app.css`: desktop composition and nearby-desktop adjustments. No mobile layout or footer. Discovery has its own scoped desktop stylesheet.

## Reference fidelity and asset handoff

Target viewport: **1448 × 1086**. The centered application is 1310px wide, with an 81px header, 319px hero, overlapping benefit strip, four horizontal process steps, six pet cards, and final CTA. The mockup's browser toolbar, window frame, and outer glow are not website UI; content starts at y=0 instead of the screenshot's approximate y=49. Compare section geometry with that offset in mind.

All repository raster assets were visually reviewed. Existing `public/logo.png` is a different pink/navy silhouette mark; available real dog photos and template placeholders do not match the reference. None were copied into this frontend. No external imagery, generated imagery, screenshot crops, or `/design` content is bundled.

Missing assets are explicit labeled placeholders: original logo, Nala/Mochi hero, six pet portraits, and the high-five cutout. `src/assets/landingAssets.ts` isolates hero/logo/CTA sources; each typed pet fixture owns its photo source. Replace `src: null` with an approved imported asset to render an actual image in the same geometry. The hero expects a complete wide scene with room on the left for text; the logo source should contain the complete mark and wordmark. Actual photos need focal-point review on replacement.

The blue/pink text wordmark is a temporary brand placeholder, not a claimed recreation of the logo. No exact font file is available; separate local body/display fallback stacks preserve hierarchy approximately, but glyph shape and wrapping differ. Body text no longer uses the condensed display fallback. Ordinary interface symbols use Lucide; its outlined symbols cannot exactly reproduce all illustrated/filled reference icons. No new gradients or animation were added.

## Interaction boundary

On Landing, Home is active; Explore, Explore Pets, and See more pets now open `/discover`. How It Works links to its section. Search, Stories, Resources, Sign In, and signup CTAs are disabled with explanatory titles because their destinations are outside this cycle. They retain the reference's resting appearance. Signup buttons additionally expose their unavailable explanation to assistive technology. No placeholder routes or extra dialogs were invented. Pet hearts retain the outlined reference appearance and are inactive. Cycle 2.1 removed the speculative local favorite toggle; the mockup does not establish a selected state.

## Checks

Vitest + Testing Library cover screen sections, primary navigation, CTA presence/unavailability, fixture-backed pet cards with honest image placeholders and inactive save affordances. Browser viewport inspection is separate from these DOM tests; jsdom does not verify layout.

Tool integration follows [Tailwind's Vite installation](https://tailwindcss.com/docs/installation/using-vite), [React Router's declarative setup](https://reactrouter.com/start/declarative/installation), and [Vitest's guide](https://vitest.dev/guide/). Installed versions are pinned in `package.json` and the independent lockfile.


## Discovery — Cycle 3

- `src/features/discovery/DiscoveryPage.tsx` composes the authenticated-looking header, upper navigation/featured-pet region, lower six-pet grid, and right filter panel. The results extend beneath the navigation; there is no full-height sidebar.
- `discovery.fixtures.ts` contains typed owner/location/count data, Nala and her companion, six nearby pets with distance/traits/photo slots, and filter groups/defaults. The displayed 127-pet count is reference copy, not a computed result count.
- `components/DiscoveryHeader.tsx`, `DiscoverySidebar.tsx`, `FeaturedPetCard.tsx`, `DiscoveryFilters.tsx`, and `PetGrid.tsx` implement only this screen's compositions.
- Shared logo, action button, image fallback, section heading, and `PetCard` are reused. The nearby card variant adds the screenshot's top-right heart, distance, and two traits. `PetLocation` and `PetTraitBadge` are shared because both the featured pet and nearby cards require them.
- Existing tokens remain unchanged. The Plus star has the reference's local gold accent; backgrounds and trait colors otherwise use the approved tokens. `discovery.css` is imported through the Tailwind entry to preserve layer order, and its selectors do not restyle Landing.

Local interactions: radio-chip selection/reset, editable search text, and the Show button scrolling to the fixed fixture grid. Search and filters do not fetch or change results. Single selection per group is a minimal preview assumption because cardinality is not specified in the image. Distance and sort show only the reference option; list view, gallery arrows, Save, Say Hello, notification/account/location menus, companion profiles, and other sidebar destinations remain inactive. There is no local favorite state, matching, authentication, persistence, or API integration.

All Discovery photographs and the owner portrait remain isolated neutral placeholders. No generated/stock images or screenshot crops were introduced. The original font and some bespoke icons are still unavailable. See `docs/ui/DISCOVERY_VISUAL_REVIEW.md` at the repository root for measurements and verification results.

## Consolidation review

See [GLOBAL_UI_REVIEW.md](../docs/ui/GLOBAL_UI_REVIEW.md) for the current component architecture, navigation, verification, missing assets and API prerequisites. Earlier cycle notes below/above are historical; all data and interactions still remain local. No API, authentication, billing or persistence is implemented.
