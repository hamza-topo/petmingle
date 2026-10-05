# Pet media storage policy

This document defines the storage contract for pet images used by the authenticated API.

## Supported media

The current contract supports one primary pet image.

Accepted uploads must satisfy all of the following server-side checks:

- valid image content
- MIME type `image/jpeg` or `image/png`
- filename extension `.jpg`, `.jpeg`, or `.png`
- maximum size 10 MB

Validation is performed by Laravel before storage. Client-side checks are advisory only.

## Storage

Pet images use the Laravel `public` disk and are written under:

```text
pets/<server-generated-uuid>.<derived-extension>
```

The original client filename is never used as the stored filename.

The persisted extension is derived from the validated MIME type:

- `image/jpeg` → `.jpg`
- `image/png` → `.png`

The public disk is intentionally used because pet profile media is product-visible content. Deployment must expose `public/storage` through Laravel's documented storage link.

## Creation

`POST /api/v.0/pets` accepts an optional multipart field:

```text
image
```

When omitted, the persisted `images` array is empty.

## Replacement

`PUT /api/v.0/pets/{pet}` accepts an optional multipart `image`.

A valid replacement is stored first. The pet record is then updated. Only after the database update succeeds is the previous managed image deleted.

If validation fails, the previous image and database state remain unchanged.

If persistence fails after a new file was written, the new file is deleted so an orphan is not intentionally left behind.

## Removal

An owner can explicitly remove the current image with:

```json
{
  "remove_image": true
}
```

The pet record is updated to `images: []`, then the previous managed file is deleted.

A request cannot both upload `image` and set `remove_image=true`.

## Managed legacy paths

Deletion is restricted to application-managed pet media paths:

- `pets/`
- legacy `uploads/`

Arbitrary filesystem paths stored or supplied elsewhere are not deleted by the pet media service.

## Pet deletion

Pet records use soft deletes and can be restored. Soft-deleting a pet therefore does **not** delete its media. Media lifecycle changes happen only through explicit replacement/removal in the current API contract.
