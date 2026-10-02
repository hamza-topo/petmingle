# PetMingle desktop design system

Status: five desktop screens implemented and consolidated through Cycle 7. The five local images remain the visual source of truth. The reference measurements below originated in Cycle 1; current implementation notes and `frontend/src/styles/tokens.css` take precedence over initial estimates. See [COMPONENTS.md](COMPONENTS.md) for the implemented architecture and [GLOBAL_UI_REVIEW.md](GLOBAL_UI_REVIEW.md) for verification and remaining limitations.

## Reference inventory and measurement method

All five PNGs are directly inside the ignored `/design/` directory; all measure **1448 × 1086 pixels**. Their accompanying `:Zone.Identifier` files are metadata, not additional mockups. Paths below are reference identifiers only: do not copy, stage, commit, or bundle anything from `/design`.

| ID | Filename within `/design` | Interpretation |
| --- | --- | --- |
| M1 | `ChatGPT Image Sep 30, 2026, 10_42_38 AM-1.png` | Public landing page |
| M2 | `ChatGPT Image Sep 30, 2026, 10_42_40 AM-2.png` | Signed-in discovery / explore |
| M3 | `ChatGPT Image Sep 30, 2026, 10_42_42 AM-3.png` | Create pet profile, populated form |
| M4 | `ChatGPT Image Sep 30, 2026, 10_42_43 AM-4.png` | Match & Chat, selected conversation |
| M5 | `ChatGPT Image Sep 30, 2026, 10_42_45 AM-5.png` | Own pet profile with Plus subscription offer |

Each image was visually inspected. Dimensions are measured from the PNG files. Geometry is estimated in image pixels, not recovered CSS pixels; browser scale is unknown. Palette candidates combine visual inspection with RGB frequency checks (8-channel-value buckets for saturated colors). Raster shading and antialiasing prevent an exact original token specification. Values below are proposed implementation starting points and must be checked against each reference later.

The outer blue glow, rounded browser window, traffic-light controls, address bar, lock, and browser toolbar are presentation framing. Exclude them from the application. The application itself starts around y=49–54; its header ends around y=125–135. Do not implement a fake browser shell or assume the entire 1448-pixel image is the application viewport.

## Color tokens

| Proposed token | Candidate value | Observed use / confidence |
| --- | --- | --- |
| `color-ink` | `#080D50` | Very dark navy headings, labels, outline icons; high confidence in role, medium in exact value; references vary roughly from deep indigo to navy |
| `color-text-secondary` | `#5E75AD` | Blue-gray body copy, metadata, field labels; approximate |
| `color-brand-pink` | `#FC3D7C` | Primary CTAs, hearts, selected badges, required markers; strong sampled cluster around `#F83878`–`#F84080` |
| `color-brand-blue` | `#0088FF` | Links, secondary actions, active navigation text; sampled clusters around `#0080F8`–`#0890F8` |
| `color-surface` | `#FEFEFE` | Dominant near-white panels; measured common pixel value, with white variation |
| `color-canvas` | `#EDF8FF` | Pale blue application backdrop on M2–M5; approximate |
| `color-surface-blue` | `#E3F4FE` | Feature tiles, selected-soft treatments, chips; exact recurring M1 pixel, approximate across screens |
| `color-surface-pink` | `#FEF3F8` | Feature tiles and selected/spotlight regions; exact recurring M1 pixel, stronger pink tints elsewhere |
| `color-surface-pink-strong` | `#FCE3ED` | Icon disks, completion prompt, selected conversation tint; approximate |
| `color-border` | `#D7E7F7` | Fine input outlines, separators, lightly bounded cards; approximate |
| `color-border-action` | `#8ACBFF` | Blue outlined buttons and selected filter chips; approximate |
| `color-bubble-incoming` | `#EFF3F8` | M4 incoming messages and composer input; approximate |
| `color-bubble-outgoing` | `#D8EFFF` | M4 outgoing messages; approximate |
| `color-trait-teal` / `color-trait-teal-bg` | `#00AD9B` / `#DDFBF7` | Gentle, outdoorsy, compatible traits; approximate |
| `color-trait-purple` / `color-trait-purple-bg` | `#8648EF` / `#F0EAFF` | Calm/social/adventurous trait variants; approximate |
| `color-success` / `color-success-bg` | `#12C644` / `#BDF4C8` | Photo guidance checkmarks; approximate |

Semantic colors are role-based; do not make each pet trait a permanently assigned universal color because trait colors differ between references. Gender symbols, crown/star promotion icons, checks, and unread markers retain their source treatments.

Some pink CTAs and blue selected controls have slight tonal variation; M5 has a very faint pink premium backdrop. Preserve these only where visible. Do not add broad gradients, glows, glossy cards, or extra decorative surfaces. Contrast has not been certified from these raster estimates; verify actual text/control colors at implementation without changing the visual hierarchy.

## Implemented shared tokens — Cycle 7

The original palette is retained, including Landing-calibrated pink `#FC3D7C`, pink disk `#FCE3ED`, and blue disk `#D3EBFD`. Repeated gold `#FFBF24` is now `--color-accent-gold` (Discovery's Plus star and Own Profile's energy icon). Isolated Messaging shield/smile colors remain local rather than expanding the shared palette.

- Existing spacing aliases 8/12/16/24px are reused for recurring gaps; exact screenshot-derived offsets and widths remain feature-specific.
- Radius tokens: control8px, card10px, media12px, panel20px, pill999px. Distinct bubble/gallery shapes stay local when their source differs.
- One small-card shadow: `0 2px 6px rgb(8 13 80 / 0.06)`.
- Fonts remain local fallback stacks; no remote request or exact-font claim. Hero68px/78px line height, section36px, feature21px, body16px, metadata14px; source-specific headings may differ.
- Neutral missing-asset surface aliases `--color-bubble-incoming`.

Shared badge/location rules live in `styles/shared.css`; creation choices use a distinct class to prevent style leakage. Per-screen widths, headers and dense-control sizes deliberately follow each reference. Home is visually underlined on Own Profile, but its link does not falsely claim to be the current URL.

## Typography

The mockups use a rounded sans-serif with heavy, compact headings and softer regular body text. The exact family is **unidentified**. Logo lettering is branded artwork, not ordinary page typography. Existing Laravel `Comfortaa` usage is not evidence that the new mockups use that face. Select a font only after comparing glyph shapes, line wraps, and widths; no font dependency or remote font request is proposed yet.

| Role | Approximate size / line height | Weight |
| --- | --- | --- |
| M1 hero title | 68–72 / 76–80 px | 800–900 |
| M5 pet name | 48–52 / 56 px | 800 |
| M3 introduction | 42–44 / 46 px | 800 |
| Page / large section heading | 30–36 / 36–42 px | 750–850 |
| Panel heading | 24–28 / 30–34 px | 700–800 |
| Feature / prominent item title | 18–22 / 24–28 px | 700 |
| Body / form value | 16–18 / 22–26 px | 400–500 |
| Navigation / metadata | 14–16 / 18–22 px | 400–600 |
| Compact card metadata / chip | 12–14 / 16–18 px | 400–600 |

M1 hero supporting copy is unusually large (about 28–30 px). M4 message text is around 19–20 px. Preserve these contextual sizes rather than forcing all paragraphs into one token. Headings are left aligned; pink emphasizes “people” and “Pets.” Avoid arbitrary tracking or all-caps labels except the observed onboarding eyebrow. Body lines are relatively open; headings are tighter.

## Desktop geometry and alignment

All coordinates below refer to the original image and are approximate. Use them as visual comparison anchors, not absolute-position instructions.

| Screen | Application bounds and layout |
| --- | --- |
| M1 | App x≈70–1379 (1309 wide). Header ≈81 high. Hero y≈130–449. Main content x≈105–1345, ≈1240 wide. Three benefit tiles overlap hero bottom. Four horizontal process steps; six equal featured-pet columns; full-width bottom CTA. |
| M2 | App x≈38–1412 (1374 wide). Header ≈85 high. Upper left sidebar x≈49–273 (224 wide); central area x≈310–1120 (810); filter panel x≈1138–1399 (261). Featured pet panel has ≈436-wide image and ≈374-wide details. Crucially, lower six-pet results span x≈69–1114, beneath both navigation and featured region; filters continue on the right. |
| M3 | App x≈68–1381 (1313 wide). Content x≈94–1356. Left onboarding area ≈338 wide; right form ≈906 wide, gap≈18. Form surfaces stack with ≈12–14 gaps. Basic information is two columns with Size alone in the left third row. Photo row has preview, drop zone, and a narrower guidance panel. |
| M4 | App x≈34–1415 (1381 wide). Header ≈82 high. Main panels x≈44–404 (360), 414–1027 (613), 1036–1405 (369); ≈10–12 gaps. Message composer sits at the bottom of the center panel. |
| M5 | App x≈55–1394 (1339 wide). Main x≈78–1375. Left profile column ≈722 wide; right Plus panel ≈559; gap≈16. Left stacks identity/gallery, About, Details. Main photo ≈373×299; four thumbnails plus an add-photo slot below. Three pricing choices share the right panel width. |

Proposed spacing vocabulary: **4, 8, 12, 16, 20, 24, 32, 40, 48 px**. Most interior panel padding is 16–24 px; text stacks use 4–8 px; section spacing 16–32 px. Some references require values between these estimates. Do not snap everything to a coarse grid at the cost of fidelity.

Headers share a horizontal centerline for logo, navigation, and actions. Card titles, descriptions, and lower metadata align to a consistent left inset. Repeated card images have matching heights. Form columns align at their leading edges, and button labels center vertically with icons. Metadata rows use small bullet separators where shown. The desktop body widths vary: do not impose one universal narrow max-width. Use screen-specific grids with flexible center regions and bounded side regions; preserve column count and hierarchy during ordinary desktop resizing. No mobile breakpoint, stacking, hamburger, bottom navigation, or responsive rules document is specified.

## Shapes, elevation, controls

| Pattern | Visible treatment / proposed starting values |
| --- | --- |
| Major panel | Near-white, radius≈18–24 px; little or no shadow |
| Pet / pricing card | Radius≈10–14 px; fine blue-gray border and restrained shadow |
| Input / photo tile | Radius≈8–14 px; one-pixel pale border |
| Message bubble | Radius≈20–26 px, no sharp speech tail |
| CTA / chip / search | Pill radius (`999px`) |
| Avatar / icon disk | Circle; small avatars≈40–60 px, paired conversation portraits≈60–76 px overall |
| Shadow | Candidate `0 2px 6px rgb(8 13 80 / 0.06)` for small cards; keep larger panels largely flat |
| Primary action | Pink fill, white text, optional visible paw/arrow/calendar/crown; usually 44–56 px tall, premium CTA≈60 px |
| Secondary action | White or faint-blue fill, thin blue border, blue label |
| Soft action | Pale blue/pink background, colored label, no heavy border |
| Save action | White pill, pink heart and label, pale pink border |
| Icon action | Circle or small rounded tile, pale/white background; contextual size≈36–52 px |
| Filter chip | Pale blue unselected; selected “All” has a clear blue outline and blue text; ≈36 px high |
| Personality choice | Wide pill, optional trait icon; selected Playful filled blue with white label; unselected choices blue/pink tinted |
| Form field | ≈54 px high, inset small label above value, optional left icon, right chevron for selects; required asterisk in pink |
| Upload target | Dashed pale-blue outline; centered camera icon, action, and format hint |
| Pricing radio | Selected pink filled ring, pink card outline, “Most Popular” badge; other cards thin pale border and navy empty ring |

Inputs must retain real labels when built; the inset-label appearance is not placeholder-only text. Search fields use a leading search icon and muted hint. Most iconography is outlined, but paws, hearts, crowns, illustrated interests, and the logo include filled or multicolor artwork. Lucide can cover ordinary interface symbols; it is not a replacement for the brand logo or illustrated pet assets.

## Navigation and observed states

- M1/M3: public header with Home, Explore, How It Works, Stories, Resources; search, Sign In, Get Started. M1 Home is blue with a short pink underline; M3 has no obvious active underline.
- M2: utility header with location, large search, notification dot, owner portrait/name/dropdown. Separate sidebar: Discover, Matches (12), Messages (3), Profile, PetMingle Plus. Discover is selected using pink tint and a left pink rail.
- M4: horizontal public-like navigation, but Match & Chat replaces How It Works and is active. Location, notification, and owner avatar replace sign-in actions.
- M5: Home remains active despite profile content; Messages button and pet avatar/name appear on the right. Preserve this discrepancy in the reference; final route-to-active-nav logic is unresolved.
- M2 shows a verified pet, Featured badge, gallery position 1/5, default filter selections, grid-view selection, save hearts, and trait badges. Filled persistence states for Save are not supplied.
- M3 shows step 1 active, a supplied photo, populated required fields, and Playful selected. Error, focus, pending upload, completed-step, and review states are absent.
- M4 shows All selected, one pink selected conversation with unread count 1, notification dot, incoming/outgoing bubbles, times, and blue double-check receipts. Composer is empty. No typing/loading/error/call state is shown.
- M5 shows a selected 12-month plan and incomplete-profile prompt. The purchase/checkout outcome is absent.

Only infer semantic interactions needed for visible controls later. Static images do not specify animation timing, hover styles, validation messages, empty results, loading skeletons, errors, dropdown contents, or disabled states. Motion should be restrained and should not introduce a swipe deck that is absent from these desktop views.

## Photography and assets

Pet photography dominates. Photos are bright, warm, outdoors, with softly blurred backgrounds; image crops prioritize faces and ears. Use cover cropping with per-image focal points, not arbitrary stretching. M1 uses a wide scene with open space for left text and pets on the right. M2 uses a tall dog-and-cat portrait. M3/M5 use landscape pet previews; M4 uses paired pet portraits. Avatars are circular, with overlapping pairs in conversations. Card photos clip to top corners; galleries and previews clip all corners. Overlays include hearts, camera actions, gallery arrows/count, and Featured badges only where supplied.

No standalone source logo, reference pet photos, cutouts, or exact font have been supplied alongside the five mockups. Existing `public/logo.png` and legacy assets were found but have not been established as matching assets. Do not claim they match. Asset sourcing/export is a future prerequisite for fidelity; do not substitute random stock pets or commit screenshot crops from `/design`. M1's paw/high-five and M3's bottom pet cutout are intentional reference decorations, not a pattern to repeat elsewhere.

## Ambiguities and inconsistencies to retain for review

1. Three different authenticated header patterns exist; owner identity (Sarah) and pet identity (Nala) both act as account affordances. Do not unify them prematurely.
2. M2 is San Francisco; M4/M5 are San Diego. Keep screen-local fixtures until a canonical location model is agreed.
3. M3 highlights Pet Info while Personality and Preferences are already visible. It is unclear whether this is one long form, a wizard overview, or a composite. Preserve the visible composition; do not invent later steps.
4. Only M2 shows a specific URL (`/explore`). Other browser bars show the root domain; proposed routes are architecture suggestions, not extracted requirements.
5. M2's lower results run underneath the left navigation area. A full-height sidebar would change the supplied composition.
6. M5 profile content marks Home active; the intended active route needs clarification before functional navigation.
7. Pet names/breeds/portraits are not fully consistent between cards and conversation pair thumbnails. Nala appears consistently, while corgi identity includes Toby and Milo. Do not deduplicate pets by photograph alone.
8. M2 distance and species filters mix apparent defaults with potentially multi-select personality chips. Selection cardinality, clear/reset behavior, and when filters apply are not specified.
9. M3 upload copy says JPG, PNG (max 10MB); file count, size-unit interpretation, processing, and validation wording are unspecified. Required fields visible: pet name, species, breed, age, size.
10. Plus prices are visible presentation copy: $7.99/month billed $95.88 annually; $11.99/month billed $35.97 quarterly; $14.99/month billed monthly; annual “Save 33%.” The savings comparator is unstated (approximately quarterly pricing, not monthly). No payment behavior or premium entitlement should be inferred.
11. Calling, scheduling, park picking, group playdates, verification, profile metrics, rewinds, and premium capabilities appear visually but are not verified backend capabilities.
12. Exact fonts, logo/vector source, photo source, original CSS measurements, scroll/sticky behavior, hover/focus/error states, and all mobile designs are unavailable. No footer is visible; do not invent one.
