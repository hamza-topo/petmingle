# Desktop regression review after API integration — #134

This review supersedes the fixture-only runtime and verification claims in GLOBAL_UI_REVIEW.md. The five existing screens now use their current API adapters; Landing retains its reference content.

## Reproducible matrix

The production Vite build runs in headless Chromium at a 1000px viewport height.

| Screen | 1448px | 1280px | 1120px | Data exercised |
| --- | --- | --- | --- | --- |
| Landing | Capture + overflow check | Capture + overflow check | Capture + overflow check | Existing reference content |
| Pet creation | Capture + overflow check | Capture + overflow check | Capture + overflow check | Authenticated owner, loaded species/races |
| Discovery | Capture + overflow check | Capture + overflow check | Capture + overflow check | Account coordinates, long pet/breed/biography, empty media |
| Own Profile | Capture + overflow check | Capture + overflow check | Capture + overflow check | Long current pet identity, biography, breed, persisted statistics, empty gallery |
| Messaging | Capture + overflow check | Capture + overflow check | Capture + overflow check | Distinct owner/pet IDs, long pair names and continuous message URL |

Discovery and Messaging additionally capture empty, HTTP 500 error and delayed loading states at **each** width: 33 combinations overall. Loading waits for the feature request, rather than the authentication bootstrap. JSON records include document width and, for populated screens, primary heading/control bounds and internal overflow. Unexpected API paths and uncaught page errors fail populated-screen checks.

## Corrections driven by browser evidence

- Creation and Own Profile headers exceeded the 1448px viewport after authenticated identities and sign-out replaced the old public controls. Their existing header compositions now distribute available space with flex; names truncate within a bounded identity slot while their complete accessible names remain available.
- Discovery had five grid slots for six utilities. Sign-out now has an explicit sixth slot; the account name truncates within its assigned width. Location and search keep their existing functions and visual treatment.
- Conversation pair names escaped the list column. The list now uses deliberate ellipsis, with the full pair retained in the row's accessible name. The active-thread heading wraps; call actions retain their width.
- Match-card names were anonymous unbreakable flex items. They now wrap as text with an inline sex marker; breed labels can wrap.
- Own Profile headings, breed summaries and detail values wrap inside their columns, including unusually long words.

The three Messaging columns, Discovery filters/sidebar, creation form columns and profile/Plus columns remain. Supported desktop minimum remains 1120px. No body overflow masking, mobile layout or generic redesign was added.

## Running and reviewing evidence

CI installs pinned Playwright 1.56.1 without changing package.json or the lockfile, installs Chromium, then runs `node scripts/desktop-review.mjs` from frontend after the production build. API/media build URLs point at the local preview origin. For an equivalent local run:

```sh
npm ci
VITE_API_BASE_URL=http://127.0.0.1:4173/api/v.0 VITE_MEDIA_BASE_URL=http://127.0.0.1:4173 npm run build
npm install --no-save --package-lock=false playwright@1.56.1
npx playwright install --with-deps chromium
node scripts/desktop-review.mjs
```

The `desktop-review` Actions artifact contains full-page screenshots, a contact sheet and results.json, retained for 14 days. Review the workflow on the PR for the latest evidence. Captures are generated from the real application build and manually inspected; this is a layout guard and review harness, not pixel-baseline comparison.

## Limits

API responses are deterministic, contract-shaped records routed through the current adapters, including consistent owner/pet relationships. This review does **not** query a live production database or validate every possible backend record. Laravel API tests remain a separate CI gate. Actual media crops, missing original brand/font assets, browser differences, mobile, screen readers and 200% zoom are outside this pass. Empty media is intentional and labelled; original assets are still required for reference-image fidelity.
