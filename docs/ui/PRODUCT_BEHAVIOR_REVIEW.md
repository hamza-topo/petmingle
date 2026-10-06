# Product behavior and wording review — #135

Implemented controls remain active: authentication/sign-out, profile creation/editing/media, account location in Discovery, species/breed/distance filters, likes/passes, matches/mismatches, conversation reads/sends and seen state. These use current API adapters and their pending/error states.

Deferred controls remain disabled: search, notifications, account/pet menus, Stories/Resources, list view, calls, attachments, emoji picker, scheduling/park selection, draft saving and Plus purchases. Labels describe availability without referring to a prototype. Notifications no longer claim unread activity or display a fabricated unread dot.

Creation starts with an empty name and age. Unsupported size/personality/playdate controls are disabled, unselected and labelled as unavailable; they are not registered as editable fields or sent to the API. Photo previews remain intentional: they are pending selections until saved and can be cancelled.

Profile details no longer present demonstration weight, neutering, compatibility, activities or traits as facts about the current pet. Missing unsupported values say Not provided. Plus has no billing integration: its existing comparison panel explicitly labels proposed features/prices, disables plan selection and purchases, and makes no active-subscription/cancellation promise. Promotional copy no longer hardcodes Nala.

Discovery, matches and messaging use product wording rather than persisted/backend-contract terminology. Location editing is correctly described as available in Discovery, while match/like statistics update automatically.

Landing explicitly labels its reference pet cards as examples and still contains and missing-artwork placeholders. This task does not invent real public listings, purchase capability or original assets. Historical visual notes are not current product copy.

Validation: existing integration and persistence tests, focused assertions for deferred controls and removal of demo profile facts, TypeScript/production build and the 33-case Chromium desktop review in CI.
