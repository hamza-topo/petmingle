# Desktop accessibility review

Scope: Landing, pet creation, Discovery, own profile/Plus, Messaging and matches; includes sign-in and the account-location editor. Review records code inspection and automated behavior checks. It does not certify WCAG compliance.

## Changes and existing behavior

- Every page main landmark can receive programmatic focus. Route navigation focuses main; hash navigation focuses its target. Sign-in gains the same skip-link pattern already present on the other screens.
- Location editor is an inline disclosure, not a modal: native Tab order remains available, opening focuses latitude, Escape/Cancel/save returns focus to its trigger, and busy saves cannot be dismissed with Escape.
- Profile editing focuses the first field after taxonomy loads. Validation focuses the first invalid field. Species, breed, age and biography errors now have explicit descriptions, alongside the existing name association.
- Location errors have unique per-instance IDs and explicit field descriptions. Sign-in field errors announce via alert.
- Messaging retains labeled native inputs/buttons, conversation-filter pressed states and polite timeline additions. Composer errors describe the input; completed sends restore input focus. Forms expose busy state.
- Unsupported controls remain natively disabled and labeled unavailable. No modal focus trap or ARIA menu pattern is introduced for inline forms.
- Focus rings use dark ink; reduced-motion preferences suppress transitions/animation and smooth scrolling.
- Decorative sky-blue/pink colors remain; text variants and filled action backgrounds use darker shades.

## Contrast calculations

Relative luminance calculations for foregrounds against white, canvas, pale blue, pale pink and strong pale pink:

| Foreground | Minimum ratio across those surfaces |
| --- | --- |
| Secondary #4d6294 | 4.97:1 |
| Blue #0068c4 | 4.59:1 |
| Pink #bd2056 | 4.95:1 |
| Purple #7132d5 | 5.56:1 |

Teal #00786c is used on the teal trait background; inspect new surface pairings separately. White text on the darker blue/pink action fills exceeds 4.5:1. These are token calculations, not a rendered per-pixel audit of every element.

## Verification and remaining limits

Dedicated tests cover route/hash focus, location Enter/Escape/focus return and validation descriptions, message keyboard submission/focus recovery and send-error association. Existing suites cover sign-in errors, profile editing, Discovery controls, unavailable actions and messaging state.

Run the complete frontend typecheck, tests and build in the React CI job. Manual screen-reader/browser review and high zoom/reflow remain necessary. The application still targets desktop widths of at least 1120px; this pass does not establish narrow-screen or 400% zoom conformance. Photos and decorative colors require contextual review; native disabled controls are exempt from text-contrast requirements. Visual review is tracked separately in #134.
