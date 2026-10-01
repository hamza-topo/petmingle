# Pet profile creation — Cycle 4

Reference: `design/*10_42_42 AM-3.png`, 1448 × 1086. Route: `/pet/create`. This is a pet profile, not a human account. Laravel, APIs, Messaging and Own Profile remain untouched. No images were copied from the ignored design directory.

## Interpretation and measurements

The reference includes approximately 49px of browser chrome, excluded from application coordinates below. The first progress step is active even though all four form sections are visible. This remains one page; Continue validates locally without advancing to an invented wizard screen.

| Region | Reference / implemented at 1448px |
| --- | --- |
| Application | x≈68, width≈1312; header 76px |
| Left progress | x94, width338; four numbered steps, first pink; isolated illustration placeholder below |
| Form | x450, width905 (reference approximately906), 18px sidebar gap |
| Photo panel | y91, height289; preview and upload target 189px high; separate guidance panel |
| Basic information | y393, height271; two columns, five 54px fields; Size occupies left column only |
| Personality | y677, height171; six chips in three columns and two rows; 39px chip height |
| Preferences | y861, height172; two fields, 44px Continue action and inactive save action |
| Panel geometry | 13px vertical gaps, 20px radius, no added panel shadows |

The photo row reproduces one preview, one dashed upload target and three guidance items, rather than a multi-photo gallery. The initial values reproduce Nala, Dog, Golden Retriever, 3 years, Large (50+ lbs), Playful, High energy and Active play. There is no visible footer.

Existing color, typography and radius tokens are reused without changes. Desktop-only rules retain the sidebar, two field columns and three chip columns at 1280 and 1120px. No mobile composition was introduced.

## Components and form state

- Reused `SiteHeader`, `PetMingleLogo`, `ActionButton`, `ReferenceImage` and Lucide conventions.
- Added `FormField` for inset labels, optional visible icons, required markers and linked validation errors.
- Added `PhotoUploader` for local selection, drop, replacement, removal and object-URL lifecycle.
- Added `ProfileProgress`, scoped to the four steps in this reference.
- `PetCreatePage` composes these components and four semantic, labelled sections.
- `profile.schema.ts` contains the Zod schema, inferred TypeScript value type and typed reference defaults. React Hook Form owns all field, trait, preference and file values.

Only react-hook-form 7.89.0, @hookform/resolvers 5.9.1 and zod 3.25.76 were added as direct runtime dependencies. The resolver also installs its own transitive dependency. No development dependencies were added.

Validation requires a nonblank name and valid visible selections. Age uses nonnegative whole years; the local select offers under one year through 30 years. Photo is optional, JPG/PNG only, up to 10 × 1024 × 1024 bytes. Type/size validation is client-side preview validation, not a server-side security policy. Selection lists beyond the displayed defaults are small local examples, not a backend species/breed taxonomy. Traits may be empty because the source does not indicate a required selection.

Continue invokes a local handler after validation and displays an explicit local-only status. Nothing is uploaded or persisted. Save and finish later remains disabled, since persistence is outside this cycle. Existing signup CTAs remain disabled: pet creation does not implement account registration. Navigate directly to `/pet/create` to review it. Shared Home navigation now marks itself active only at `/`; How It Works returns to the Landing anchor from this screen.

## Verification

- TypeScript: passed (`npm run typecheck` inside the frontend Docker container).
- Vitest: 16/16 passed across three files (4 Landing, 7 Discovery, 5 profile creation).
- New tests cover route/sections/progress, required name, trait/preference submission, photo preview/replacement/removal/unmount URL cleanup, and invalid type/oversized file rejection.
- Production build: passed (`npm run build`).
- Chromium: all three routes checked at 1448, 1280 and 1120 × 1086, with screenshots and DOM bounds. Document widths equal viewport widths; no horizontal page overflow or JavaScript errors; zero external requests.
- At all three widths, exercised blank-name validation, editing, trait/preference selection, local image selection/removal and successful local validation. Form controls remained usable.
- The element overflow diagnostic also notices Discovery's deliberately clipped `.sr-only` explanatory text; this is an accessibility technique, not visible overflow.

## Remaining fidelity limitations

The exact Nala photo, dog/cat cutout, brand illustration and original typeface are unavailable. Neutral isolated placeholders preserve the slots without introducing other pets, stock images or generated artwork. Font metrics and Lucide glyphs approximate the source; particularly the dog icon is an outline head rather than the full-body silhouette. Existing flat brand colors approximate the source's raster shading; no new gradients were introduced. Native select menus vary by browser and their expanded states are not shown in the mockup. The left illustration cannot reproduce the original cutout overlap. Validation errors and the local-only submit status expand the form after interaction, while initial geometry follows the reference.

No existing design token was adjusted. No commit was created.
