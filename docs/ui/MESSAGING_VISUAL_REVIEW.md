# Messaging — Cycle 5

Reference: `design/*10_42_43 AM-4.png`, 1448 × 1086. Route: `/messages` (direct local preview: http://localhost:5174/messages). Scope is Messaging only; no dependencies installed, Laravel changes, APIs, persistence, sockets, mobile layout or Own Profile implementation. No source mockup assets were copied or committed.

## Reference interpretation

The screenshot has an approximately 49px browser frame above the application; coordinates below exclude it. The authenticated horizontal header differs from Discovery: Home, Explore, Match & Chat active, Stories, Resources, search, San Diego location, notification dot and an unnamed human account avatar. A Messaging-specific header reuses the brand and visual tokens instead of imposing Discovery's location/search header geometry.

Three rounded white columns sit on the existing pale blue canvas. The left column contains a title, local search, All/Matches/Unread controls and eight pet-pair conversations. Nala & Milo is pink and has unread count 1, even though its thread is open in the reference. The center shows the pair, match date, inactive video/phone/more actions, Today divider, four alternating messages and a bottom composer. The right column contains two pet cards, three shared interests, three playdate actions and four safety tips. No extra toolbar, dashboard navigation, footer or decorative background was added.

## Geometry and visual audit

| Region | Reference estimate | Implemented at 1448px |
| --- | --- | --- |
| App / header | x33, approximately 1382 wide, 82 high | x33, 1382 wide, 82 high |
| Conversation column | x44, approximately 360 ×924 | x43, 360 ×924, y94 |
| Chat column | x415, approximately 612 ×924 | x413, 612 ×924, y94 |
| Details column | x1036, approximately 370 ×924 | x1035, 370 ×924, y94 |
| Column spacing | approximately 10–12 | 10px |
| Thread divider | y213 | y213 |
| Photo cards | approximately 163 wide; image 180 high | approximately 163 wide; image 180 high |
| Composer | y910, approximately 590 ×84 | y910, approximately 588 ×84 |
| Incoming / outgoing | soft gray / soft blue; approximately 20px corners | existing bubble tokens; 22px corners |
| Panel corners | approximately 14 | 14px |

Conversation rows retain their reference rhythm (active 90px, others 93px). The first bubble is a single line; subsequent messages wrap to two/three/two lines. Message font metrics were adjusted locally to match this density without changing shared tokens. The original outgoing bubbles vary in width; the implementation uses natural text sizing with a 330px maximum, so exact horizontal edges differ. The composer remains at the bottom of its column and the timeline scrolls as local messages accumulate. New messages have real local timestamps and no invented delivery/read receipt.

| Desktop viewport | Conversation / chat / details widths | Details height |
| --- | --- | --- |
| 1448 ×1086 | 360 /612 /370 | 924px |
| 1280 ×1086 | 300 /566 /310 | 932px |
| 1120 ×1086 | 262 /510 /280 | 952px |

All three columns remain present; the details panel grows vertically when copy wraps. This is desktop resizing, not a mobile layout. Existing global tokens are unchanged. Small source-specific orange shield and yellow smile accents are local to this screen. Only the paw and smile already visible in the reference message content receive equivalent Lucide rendering, avoiding missing emoji glyphs on the Linux test host.

## Fixture and identity model

`messaging.fixtures.ts` defines explicit `ChatPet`, `ChatOwner`, `ChatMessage` and `Conversation` types. Messages carry an ID, sender account ID, text and ISO timestamp, with an optional reference receipt. Direction is derived from sender ID, not stored as a layout property. Conversations contain paired pets, owner participants, the current account ID, summaries, unread count, history and known shared interests. Asset references use the existing neutral placeholder strategy.

The initial four messages and all eight summary rows follow the reference. Nala and Milo have the displayed breeds, ages and sex metadata. Other threads reuse only their visible preview text as a minimal local history; unavailable breeds, ages, interests and match dates are omitted rather than invented. Their message times are synthetic fixture metadata, not recovered backend records. Relative date labels are anchored to the frozen reference day (April 13, 2024); actual newly submitted dates are separated in the timeline.

Unresolved identity/data ambiguities:

- The header owner is unnamed. Do not assume this is Discovery's Sarah or that San Diego equals Discovery's San Francisco.
- Incoming text speaks for Nala and outgoing text speaks for Milo. The preview therefore treats the current account as the participant represented by Milo in this thread; it does not equate the pet ID and account ID.
- Other rows show entirely different pairs. For selection demos, the second pet represents the current participant in each conversation. This is a per-thread preview assumption, **not evidence that one person owns all those pets**. A future API must establish actual ownership and membership before replacing these fixtures.
- The initial active row still has one unread message, matching the source inconsistency. Explicitly opening it clears that count locally.
- Double checks are represented as reference read receipts. Their actual server semantics are unspecified; new local messages are not marked delivered/read.
- All fixture conversations are matches, so All and Matches contain the same data.

## Components and interactions

Reused: `PetMingleLogo`, `ReferenceImage`, `ActionButton`, existing color/font/radius/shadow tokens and Lucide conventions. `PetLocation` and `PetTraitBadge` are not needed by this reference: no pet distance row or trait chips are shown.

Introduced only for this screen: `MessagingHeader`, `ConversationList`, `PetAvatar`/`PetPairAvatar`, `ChatThread` with a private `MessageBubble`, `MessageComposer`, and `MatchDetailsPanel`.

Local React state supports conversation selection, per-thread messages, case-insensitive search, All/Matches/Unread filtering and clearing unread on opening. Composer text is trimmed; blank submission is disabled. Sending appends only to the active thread, updates its summary, clears the composer and scrolls the timeline. Switching conversations clears an unsent draft; sent messages remain in page memory until unmount/reload. Nothing is sent or persisted.

Calls, more actions, attachment picker, emoji picker, account/location/notification controls, View all and playdate buttons are deliberately inactive; no corresponding interaction state is supplied. The header links to Home and Explore. Existing screen navigation is unchanged; `/messages` can be opened directly.

## Verification

- TypeScript check passed in Docker.
- All 21 frontend tests passed: Landing 4, Discovery 7, Profile Creation 5, Messaging 5.
- New tests cover route/navigation/list fixtures, initial thread/match details, switching threads/details, local send/composer clearing/thread isolation, search/filtering and unread clearing.
- Production build passed; no dependency manifest or lockfile changed.
- Chromium checked all four routes at 1448, 1280, 1120 ×1086. Document width equals viewport width, with no visible content overflow, JavaScript exceptions or external requests.
- Conversation switching, typing, local send and composer clearing were exercised at all three widths. Source and rendered screenshots were visually compared; screenshots stayed in `/tmp`.

## Remaining fidelity limitations

Original pet photos, owner portrait, logo illustration and interest artwork are unavailable; neutral placeholders keep their dimensions. They cannot reproduce photographic color balance or focal points. Font family and glyph weights are approximations inherited from the approved baseline. Lucide outlines differ from some source silhouettes and illustrated icons. No gradients or new imagery were introduced. At narrower desktop widths timestamps move above the row title to prevent overlap and long previews truncate. Noninitial conversation details remain sparse because the screenshot provides no additional records.

Git status and diff were checked without staging. At the final check, the existing frontend and documentation are tracked. The tracked diff reports `App.tsx` and `app.css` (4 insertions); the new Messaging feature directory and this report remain untracked and are therefore absent from `git diff --stat`. No commit was created by this cycle.
