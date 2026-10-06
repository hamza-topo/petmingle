# PetMingle — crossing paths, direction 02

The approved direction uses a crossing-path symbol, sky blue and soft pink ribbons, strong dark typography and an editorial encounter photograph. The later simplified two-arch mark was rejected.

## First delivery

- Replace the placeholder logo with a scalable crossing-path SVG across the frontend.
- Introduce charcoal, warm paper, sky blue and soft pink tokens; retain dark text/status colors for functional controls.
- Replace pastel benefit panels, unavailable controls and fixture profiles with an editorial hero, nearby-connections story and three quiet steps.
- Keep the existing English language and existing protected routes. Discovery still requires sign-in and a pet; onboarding remains unchanged.
- Adapt the landing page from 320 px to desktop. Connected-page mobile layouts remain a subsequent delivery; their desktop regression checks still run.
- Self-host Inter's Latin variable font; no font CDN or new package dependency.

## Assets

frontend/src/assets/brand/meeting.webp is an original ImageGen marketing photograph: two dogs meeting on a neighborhood park path. It is illustrative, never a real member profile or evidence of nearby users. The generated PNG was encoded as WebP at quality 86 without cropping. Decorative ribbons are separate SVG artwork.

inter-latin.woff2 comes from @fontsource-variable/inter 5.3.0 (Latin weight subset). The SIL Open Font License and copyright notice are included in INTER-LICENSE.txt. Non-Latin characters use system fallbacks.

## Verification

- TypeScript, complete React suite and production build.
- Existing desktop review at 1120, 1280 and 1448 px for connected pages.
- Landing additionally at 320, 390 and 768 px, with image decoding and overflow checks.
- Connected discovery/messaging loading, empty and error states remain under review.
- Landing routes, onboarding links, accessible names and keyboard skip destination covered by landing and navigation tests.

## Next delivery

Carry the brand into the connected application shell, discovery, matches, messages and mobile navigation, including intentional empty states and real API data.

