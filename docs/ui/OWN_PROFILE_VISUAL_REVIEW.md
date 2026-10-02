# Own pet profile — Cycle 6

Reference: `design/*10_42_45 AM-5.png`, 1448 × 1086. Route: `/profile`, available at http://localhost:5174/profile in the existing Docker frontend. This cycle implements only the fifth supplied screen. No dependencies, Laravel changes, API integration, billing, mobile implementation or commits.

## Reference interpretation

The source has approximately 54px of browser chrome, excluded from application coordinates. The app has a 76px header and two unequal content columns. The left contains a main pet photo beside Nala's identity, three statistics and a completion action; four photo thumbnails and a dashed Add Photo tile follow below. About Nala and Details are separate white panels. The right is a pale pink Plus panel with four feature rows, three duration choices, annual selected, Most Popular and Save 33% badges, a primary purchase CTA and cancellation text. Prices and premium copy are reference fixtures, not actual offers connected to billing.

No owner name or human biography is shown. The top-right avatar/name is Nala, so the control is explicitly labelled a pet menu. The visible Home underline is retained despite the `/profile` route. This is implemented as reference styling, not an inaccurate `aria-current=page` declaration for the Home link. Whether Home means a signed-in pet overview remains a product/navigation ambiguity.

## Geometry at 1448 × 1086

| Region | Reference estimate, excluding browser frame | Rendered |
| --- | --- | --- |
| App | x55, width 1338; header 76 high | x55, width 1338; header 76 high |
| Left column | x77, width 724 | x77, width ≈723 |
| Right column | x816, width 558 | x≈816, width ≈557 |
| Column gap | 16 | 16 |
| Profile / gallery panel | y95, height 467 | y95, height 467 |
| Main photo | x93, y109, approximately373 ×300 | x92, y109, approximately372 ×300 |
| Thumbnail strip | y426, height 122 | y426, height 122 |
| About panel | y576, height 202 | y577, height 201 |
| Details panel | y795, height 216 | y793, height 216 |
| Plus panel | y95, height≈917 | y95, height 918 |
| Plan row | x828, y647, height≈240 | x≈828, y648, height 240 |
| Purchase CTA | y899, height 61 | y901, height 61 |

The header navigation, name at 50px, three compact 78px statistic tiles, completion action 64px, 20px panel corners, and main photo/gallery proportions follow the source. Plus plan widths are slightly unequal, with the annual card a little wider. Shared tokens remain unchanged; styles are scoped to Own Profile. The premium backdrop uses the existing flat pink surface, without adding ornamental gradients or shapes.

Desktop resizing keeps both main columns and all three plans visible. The gallery retains four thumbnails plus Add Photo. Detail labels/values wrap into stacked rows within their two columns at narrower widths, and Plus cards grow with their text. At 1120px the page scrolls vertically; no mobile layout is introduced.

## Identity and fixture structure

`profile.fixtures.ts` separates:

- `ProfileOwner`: anonymous account ID and owned pet IDs. Owner name is null because the reference does not supply it. No assumption that this account is Discovery's Sarah or Messaging's owner.
- `OwnPet`: Nala's ID, name, age, breed, location, biography, gallery and traits.
- `GalleryPhoto`: stable ID plus an existing `ReferenceAsset` shape; four null-source assets describe the four reference shots. No replacement photography or screenshot crops are bundled.
- `ProfileStat`: ID, label and numeric value (128 Matches, 342 Profile views, 56 Favorites), presented as a definition list with no chart or fetching.
- Detail fixtures: age, breed, 62 lbs, San Diego, spayed/neutered Yes, Dogs/People/Kids compatibility, Fetch/Hiking/Beach activities, with only the displayed icons.
- `PlusPlan`: ID, duration label, monthly display price, billing copy, benefits and optional recommendation/saving metadata. Annual `$7.99`, quarterly `$11.99`, monthly `$14.99`; all values copied from the mockup.

The term Own Profile means the pet's profile, not the human account. Fixtures are independent of earlier screens; conflicting locations or account context across mockups are not silently merged. The screenshot does not establish whether account switching and pet switching are the same menu. The menu therefore remains inactive.

## Reuse and component boundaries

`SiteHeader` now accepts an optional typed pet identity. This reuses the exact public-style navigation of the profile mockup and replaces signup/sign-in controls with Messages and the pet menu. Its existing Landing and creation behavior is preserved. Discovery's search/location header and Messaging's Match & Chat navigation genuinely differ and were not forced into this composition.

Reused: `PetMingleLogo`, `ReferenceImage`, `PetTraitBadge`, `ActionButton`, shared tokens and icon conventions. `PetLocation` requires a distance not visible here, so the profile uses its own simple location row rather than inventing a distance.

New visible compositions:

- `OwnProfilePage`: local active-photo state and the two-column layout.
- `PetProfileGallery` / `ProfilePhoto`: main photo and thumbnail controls, with inactive camera/Add Photo controls.
- `PetProfileSummary`: pet name/metadata, three statistic tiles and completion action; small `ProfileEdit` control also used by About and Details.
- `ProfileAbout` and `ProfileDetails`: biography/traits and the two-column definition list.
- `PlusPlans` with a private `PlusPlanCard`: feature list, local radio choice, recommendation badge and inactive purchase CTA.

## Local interactions and boundaries

Selecting a thumbnail updates the main image slot and its accessible description. Selecting a radio changes the highlighted plan; Most Popular and Save 33% stay attached to the annual offer. Neither selection persists after unmount/reload. Messages navigates to the existing `/messages` screen; Home/Explore and How It Works keep their real existing destinations.

Camera, Add Photo, editing, profile completion and pet menu remain inactive because corresponding editing flows are not supplied for this screen. The approved local uploader remains in `/pet/create`; it was not duplicated here. Purchase is disabled regardless of selected plan. No checkout, billing request, fake purchase success or subscription persistence is implemented.

## Verification

- TypeScript: passed in Docker (`npm run typecheck`).
- Tests: 26/26 passed across five files (Landing 4, Discovery 7, creation 5, Messaging 5, Own Profile 5).
- New tests cover identity/biography/details/traits, fixture statistics, four thumbnails and local main-image selection, plan prices/selection/disabled purchase, and navigation to Messaging with the reference Home styling.
- Production build: passed (`npm run build`).
- Chromium: `/`, `/discover`, `/pet/create`, `/messages`, `/profile` checked at 1448, 1280 and 1120 ×1086. No horizontal document/visible text overflow, JavaScript exceptions, external requests or HTTP errors.
- Gallery selection, plan selection and navigation to Messaging exercised at all three widths. Screenshots remain in `/tmp`, outside the source tree.
- Dependency manifest, lockfile and shared design tokens are unchanged. `/design` remains gitignored and untouched.

## Remaining fidelity limitations

The exact pet photographs, logo artwork and font remain unavailable. Neutral placeholders preserve image geometry but do not reproduce the source's photographic color balance or cropping. Glyph shapes, compact plan text and icon silhouettes approximate the source. The flat Plus surface omits very faint source shading. Some line breaks vary with the local font stack, and details/plan height increases at nearby desktop widths. Edit/upload/menu behavior cannot be inferred from this one reference and is intentionally not implemented. Home's selected appearance remains a documented navigation inconsistency.

Git status/diff were checked without staging. The worktree also includes uncommitted Messaging changes from Cycle 5; the final diff stat is therefore cumulative. New feature directories and review documents remain untracked and do not appear in the tracked diff stat. No automatic commit was made.
