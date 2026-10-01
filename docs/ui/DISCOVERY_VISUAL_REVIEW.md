# Discovery implementation and visual review — Cycle 3

Reference: `design/*10_42_40 AM-2.png`, 1448 × 1086. Existing Landing is the component/token baseline. Implemented only `/discover`; Landing stays at `/`. No Laravel, API, package, lockfile, or existing token changes. No mobile layout or remaining screen implementation.

## Layout comparison

The source has approximately 49px of browser chrome above the application. Reference y-values below exclude that chrome, as in the Landing review. Source measurements are raster estimates. The live measurements were taken from Chromium at 1448 × 1086, served through the existing Docker frontend on port 5174.

| Region | Reference, approximately | Implemented |
| --- | --- | --- |
| Application | x=38, width=1374px | x=37, width=1374px |
| Header | 85–86px high | 86px high |
| Upper navigation | x=49, width=224px; five items confined to upper area | x=49, width=224px; visible navigation group 384px high |
| Featured card | x=310, y=198, width=810, height=512px | x=310, y=198, width=809, height=512px |
| Featured photograph | width≈436, height≈504px | width≈437, height=504px |
| Filter panel | x≈1138, y≈108, width≈262, height≈821px | x=1137, y=108, width=262, height=820px |
| Nearby grid | x≈69, y≈783, width≈1044px; six columns | x=69, y=782, width=1044px; six columns |
| Nearby cards | Slightly inconsistent widths/heights in raster; photos roughly 1.5:1 | Equal columns ≈162×217px, photos 1.52:1 |

The upper navigation and featured region share a grid. The nearby section sits below that entire grid, so it extends beneath the navigation; the filters occupy a separate right column. No full-height left sidebar, sticky behavior, footer, or additional dashboard panels were introduced.

Reviewed header spacing, sidebar item alignment, featured photo/detail split, metadata, biography wrapping, trait rows, companion row, outlined Save and pink Say Hello actions, filter chip dimensions, selected outlines, card metadata, sorting/view controls, whitespace, radii, palette, and icon scale. The filter panel retains the source's two-row size/energy groups and three-row personality group. Shadows are absent from Discovery's large panels and nearby cards; the Landing shadow token remains unchanged.

An initial import-order issue registered the Discovery components layer before Tailwind's reset. Discovery CSS now enters through `styles/app.css`, after the Tailwind import, so the reset cannot override component spacing. Discovery-specific card selectors preserve the approved Landing variant.

## Components and fixtures

Reused `PetMingleLogo`, `ReferenceImage`, `ActionButton`, `SectionHeading`, and `PetCard` plus approved typography/color tokens and Lucide conventions. `PetCard` has a typed nearby variant for the top-right outlined heart, distance, and trait pair; the original Landing variant retains its markup and styling except for the optional variant class.

New screen compositions: `DiscoveryHeader`, `DiscoverySidebar`, `FeaturedPetCard`, `DiscoveryFilters`, and `PetGrid`. New small shared components: `PetLocation` and `PetTraitBadge`, each required by both featured and nearby pet presentations. No components for unimplemented screens were created.

`discovery.fixtures.ts` defines typed location/owner/count metadata, the featured Nala profile, Mochi companion data, six nearby pets, photo placeholders, trait tones/icons, filter option groups, and default selections. Source counts (127 nearby, 12 matches, 3 messages) remain literal reference data. They do not claim an API result or actual signed-in session.

## Interaction boundary

- Landing's Explore, Explore Pets, and See more pets now link to `/discover`. The shared logo returns to `/`. Other sidebar destinations remain inactive.
- Filter chips use native radio inputs with visible selected styles. Each group is single-select for this preview; Clear All restores All. The source does not define selection cardinality beyond its default state, so this minimal assumption is not a final product rule.
- Search text is editable locally. It does not search a backend or alter fixtures. The Show button scrolls to the fixed nearby grid; filter selections do not modify the 127 label or the six fixture cards. Accessible descriptions explain these preview boundaries without adding visible interface labels.
- Distance and sort retain only their reference option. No unshown options or alternate list layout were invented.
- Heart/Save controls remain outlined and inactive because the source does not establish a selected favorite state. Say Hello, gallery arrows, companion profile, notifications, location/account menus, and list-view control remain inactive. No persistence, authentication, matching, likes/dislikes, or global state was added.

## Tokens and remaining limitations

`src/styles/tokens.css` is byte-for-byte unchanged from Cycle 2.1. Discovery uses its own dimensions and desktop media rules. Its very pale canvas mixes the existing canvas/surface colors. The screenshot's Plus star uses a local gold accent (`#FFBF24`); no existing palette token was replaced. No gradients, additional icon packs, or new dependencies were introduced.

Exact logo, pet photographs, companion image, and Sarah portrait are still unavailable. Existing neutral image fallbacks are used, with sources isolated in fixtures and the existing asset map. No `/design` data, screenshot crops, stock photos, or generated imagery were copied into the frontend. The actual photo color balance and subject placement cannot be validated until approved assets are supplied.

Original fonts and bespoke icons remain unavailable. The approved fallback font stacks and Lucide approximations mean some glyph shapes, line wrapping, and apparent weights differ. Biography and chip sizes are calibrated to fit the reference rather than changing global tokens. Equal nearby card widths are a deliberate reusable approximation of the slight width inconsistencies in the source raster.

At narrower desktop widths, all columns remain visible; the photo/detail ratio adjusts, text may wrap, and pet trait rows gain height. This is desktop resizing, not a new mobile design.

## Verification

- Docker TypeScript check: passed (`docker exec petmingle-frontend npm run typecheck`).
- Docker tests: 11 passed, including all four Landing tests and seven Discovery tests. Coverage: render, active navigation, featured fixture/companion, six nearby cards, filter defaults, local selection/reset, and Landing-to-Discovery navigation and return.
- Docker production build: passed after final CSS changes.
- Browser checks: 1448×1086, 1280×1086, and 1120×1086; no horizontal document or visible-content overflow, no runtime errors, no external requests. Accessibility-only visually hidden text was excluded from overflow measurements.
- Filter selection/reset and route navigation were exercised in Chromium. Landing was visually rechecked after returning through the logo.

| Viewport width | Sidebar width | Featured card | Filter panel | Nearby grid columns |
| --- | --- | --- | --- | --- |
| 1448px | 224px | 809×512px | 262×820px | 6 |
| 1280px | 184px | 696×512px | 262×820px | 6 |
| 1120px | 156px | 598×512px | 236×820px | 6 |

`git status --short` and `git diff --stat` were run. The frontend and docs were already untracked, so the tracked diff reports only the user's pre-existing `.gitignore` edit. No files were staged or committed to change that reporting; source changes were also compared with a temporary pre-cycle filesystem snapshot.
