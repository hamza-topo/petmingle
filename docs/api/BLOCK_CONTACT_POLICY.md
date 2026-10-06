# Block and contact policy

Issue #123 defines the server-side contact rules used by PetMingle.

## Identity domains

- Blocks are between **User IDs**.
- Likes, dislikes and matches are between **Pet IDs**.
- Messages are between **User IDs**.
- A pet interaction resolves both pet owners before applying block rules.

## Allowed transitions

| State | Like | Dislike | Match creation | Message read/send | Edit old message | Restore old message |
| --- | --- | --- | --- | --- | --- | --- |
| Unblocked, not matched | Allowed | Allowed | Only after reciprocal likes | Denied | Existing authorization only | Existing authorization only |
| Unblocked, active reciprocal match | Allowed/idempotent | Allowed | Allowed/idempotent through relationship flow | Allowed | Sender only | Sender only |
| Block active in either direction | Denied | Allowed as a local dismissal action | Denied | Denied | Denied | Denied |

A block is reciprocal for contact enforcement: it does not matter which account created it.

## Block side effects

Creating a block severs the persisted contact state in both directions:

1. active conversation is soft-deleted;
2. active messages between the two accounts are soft-deleted;
3. active likes in both pet directions are soft-deleted;
4. active match rows in both pet directions are soft-deleted;
5. Discovery already excludes accounts with an active block in either direction.

Historical rows stay soft-deleted for audit/history semantics. They are not automatically restored.

## Re-establishing contact

There is currently no public unblock endpoint.

If a block is removed by a future authorized flow, previous likes, matches, conversations and messages are **not** automatically restored. Both accounts must re-establish relationship state through the normal like/match flow before new messaging is allowed.

## Enforcement boundaries

The browser is not trusted to hide or disable actions correctly. The API enforces the policy on:

- Like creation;
- Match creation;
- Message thread reads;
- Message creation;
- Message update by the sender;
- Message restore by the sender.

Client-supplied sender/source IDs cannot bypass these checks.
