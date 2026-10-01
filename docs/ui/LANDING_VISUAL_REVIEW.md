# Landing visual fidelity — Cycle 2.1

Scope: only `design/*10_42_38 AM-1.png`, reviewed at 1448 × 1086. No new packages, images, other screens, or Laravel changes. The reference's browser chrome occupies approximately 49px above the application. All reference y-coordinates below subtract that offset; no fake browser frame was added.

## Method and boundaries

Captured the existing and revised React page in Chromium through the Docker-published frontend at `http://127.0.0.1:5174`. Visually compared each section and measured DOM bounding rectangles. Also checked 1280px and 1120px desktop widths for overflow. Source dimensions are manual raster estimates, not recovered CSS. This is a layout review, not a claim of pixel equality with unavailable artwork.

## Section comparison

| Region | Discrepancy before review | Correction / resulting geometry at 1448px |
| --- | --- | --- |
| Page | App width already close; content started 1–2px too far left | App remains 1310px, x=69. Inner wrapper is 1240px at x=104; text/card region is 1204px at x=122. Source is approximately 1309 / 1240 / 1204px. |
| Header | Font-dependent space distribution shifted navigation; dashed logo box was not in reference | Explicit desktop tracks align Home near x=496 and utility actions near x=1004. Header remains 81px. Logo asset slot is neutral, without a dashed border. Public navigation and active underline retained. |
| Hero | Heading too wide, supporting copy condensed, actions slightly low/wide | Hero remains 319px; display size 72→68px with 78px line height. Body uses a separate regular sans fallback. Primary action is 242×55px at y≈286; secondary is 173×50px. Reference targets approximately 242×55 / 173×50px. |
| Benefits | Blue placeholders and insufficient icon-disk contrast skewed palette; left insets slightly off | Strip y=383, height=116px (source ≈383/116). Slightly unequal column proportions and 15px gutters follow the reference. Interior padding 32px, icon disks 78px. Blue/pink disks refined from source samples; heart and location icon receive the visible filled treatment. |
| How it works | Equal columns put steps 2–4 too far left; body copy was condensed | Unequal tracks place numbered markers at x≈122,454,749,1064; source ≈122,455,750,1066. Text inset 58px; metadata line height 20px. Adjusted heading/marker gaps, lighter subtitle scale, and filled chat icon. Kept four steps and dashed connectors. |
| Featured pets | Cards started ≈5px too low; heading slightly too large; 12px corners a little round | Cards begin y≈759 (previously ≈764; reference ≈759), with six equal columns, 14px gaps, 100px image height, 46px minimum metadata area, 10px corners, and existing subtle shadow. Each card is ≈189×148px; source cards vary slightly around 186–194px wide. |
| Signup CTA | Button at x≈849 rather than x≈813; heading too large; invented dashed image separator | CTA begins y≈923 (previously ≈928; source ≈924), height 101px. Button at x≈812, width 252px. Title 34→32px. Image slot widened to 256px and dashed separator removed. |
| Footer | No footer is visible in the source | None added. |

The source's photographs have different shapes: a 1310×319 scene, approximately 189×100 pet crops, and an approximately 256×101 closing cutout area. Those slots remain distinct. Card rows stay equal-height when long breed text wraps at nearby desktop widths.

## Token changes

- `--color-brand-pink`: `#FC387F` → `#FC3D7C`, following interior CTA samples around RGB 252–253 / 61 / 124–125. The source varies slightly across pixels; a flat representative color is retained rather than inventing gradients.
- `--color-surface-pink-strong`: `#FDE5F0` → `#FCE3ED`, matching sampled pink benefit disks.
- `--color-icon-blue`: `#D3EBFD`, extracted from the blue benefit disk, now separated from the paler blue panel surface.
- `--font-sans`: regular body fallback; `--font-display`: separate rounded/condensed heading fallback. No font file or dependency added. Exact font remains unknown.
- `--text-hero`: 68px; line height 78px; `--radius-card`: 10px.
- `--asset-placeholder-background`: aliases existing `--color-bubble-incoming` (`#EFF3F8`), making missing photos neutral instead of adding large brand-blue areas.
- Navy, link blue, page surfaces, standard spacing scale, panel radius, pill radius, and small-card shadow remain unchanged; the reference did not justify replacing those values.

These are Landing-calibrated candidates. Do not automatically impose its widths, grid proportions, or font sizes on the other screens.

## Removed unsupported behavior and decoration

The source visibly includes outlined heart affordances, but neither a selected/filled heart nor local save behavior. Removed `useState`, the click handler, pressed state, filled-heart state, preview-save tooltip, and corresponding toggle test. The outline remains as an inactive button, consistent with other unimplemented destinations. The existing fixture-card test now checks that it is disabled.

Removed dashed borders around the logo/hero placeholders and the closing artwork separator. Retained accessible image-placeholder labels and the keyboard-only skip link; the latter is a nonvisual accessibility affordance, not a new page section. Kept local section anchors for Explore and How It Works. No dialogs, animations, extra cards, or new interactions were introduced.

## Remaining fidelity limits

- Original logo, hero scene, six pet photos, and high-five artwork are still unavailable. Neutral placeholders preserve dimensions but cannot reproduce the photographic color balance, depth, subject alignment, or surrounding whitespace. They remain isolated in the asset map / pet fixtures.
- No exact font supplied: rounded glyph forms, weight interpolation, and text widths remain approximate and depend on installed fallback fonts. The review used Chromium's available Linux fonts; it is not proof of identical text metrics on every host.
- Lucide symbols cannot exactly reproduce the bespoke people/paw/document/calendar illustration style. Existing symbols are retained, with filled treatment only where the reference supports it.
- The screenshot has small inconsistencies in card widths and raster shading. The implementation uses reusable, equal-sized cards and a restrained flat palette rather than encoding every image artifact.

## Verification and Git reporting

TypeScript, the four remaining Landing tests, and the production build were run. Browser inspection at 1448, 1280, and 1120px reported no horizontal document overflow or text-container overflow, no runtime errors, and no external requests.

The frontend and Cycle 1 docs were already untracked at the start of this review. Therefore `git diff -- frontend` is empty, and `git diff --stat` lists only the user's pre-existing `.gitignore` edit. No staging or commit was performed to alter that reporting. A temporary before/after filesystem snapshot was used to inspect this cycle's changes without touching the Git index.
