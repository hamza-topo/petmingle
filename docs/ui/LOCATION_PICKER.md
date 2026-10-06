# Choose a discovery area

The account location chip opens a native modal dialog. Desktop uses a centred panel; below 600px the editor fills the screen. Users submit a city/neighbourhood search, explicitly request browser GPS, or move the map under a fixed pin (arrow keys also move it). Confirmation alone writes the account location. Cancel/Escape leaves it unchanged and returns focus to the chip. Saving disables dismissal and repeat submission; errors retain the draft.

The coordinate-based nearby query is unchanged. The nullable `locations.label` column adds a human-readable area name to the existing owner-only API. The frontend shows that name in the chip, discovery count and account profile. Older records show “Selected area” until edited. A legacy coordinate-only update clears a previous label to avoid displaying the wrong city. Labels are bounded to 160 characters and are display metadata, never used for distance calculations.

## Providers and deployment

Leaflet 1.9.4 is bundled from npm. Map code and tile requests load only when opening the picker. Default tiles are OpenStreetMap standard raster tiles, with visible attribution and ordinary browser caching; no offline download or prefetch is implemented. Photon provides explicit submitted search (not keystroke autocomplete) and an 800ms debounced reverse lookup. Rapid changes cancel older requests. GPS is requested only by the corresponding button. Reverse lookup rounds the point to two decimals to identify an area, while the API retains the selected point for distance calculations. Public geocoder requests omit cookies, referrer and application auth headers. Third-party tiles/geocoding receive requests and the chosen map area; the interface mentions external services.

Both default public services are best-effort and may throttle. For meaningful production traffic, configure a managed or self-hosted Photon-compatible geocoder and an appropriately licensed tile service rather than depending on public demos. Build-time variables in `.env.example` / `.env.production.example` and Docker build args:

- `VITE_GEOCODER_BASE_URL`: Photon-compatible `/api/` and `/reverse/` endpoints; HTTPS and browser CORS required in production.
- `VITE_MAP_TILE_URL`: Leaflet XYZ raster tile template, HTTPS in production.
- `VITE_MAP_TILE_ATTRIBUTION`: provider-required trusted attribution HTML; default OSM credit remains visible.

Missing city lookup does not prevent confirming a map/GPS selection: its label is null and the UI shows “Selected area”. Tile failures show an in-map message and keep city search/GPS available. Deploy the database migration alongside the API before the frontend. Rollback removes only the display label column; coordinates remain.

Official provider guidance: https://operations.osmfoundation.org/policies/tiles/ and https://github.com/komoot/photon . Public Nominatim is not used.

## Verification

Frontend tests cover explicit search/confirmation, cancellation/focus return, GPS denial and stale GPS, failed saves, map-only confirmation and the provider adapter's coordinate validation/privacy. Backend tests cover label create/update/reload, length validation and legacy label clearing alongside existing authorization/nearby tests. The browser review exercises the real Leaflet map, native dialog focus trapping, keyboard movement, search, persistence and refreshed discovery at desktop and mobile widths with deterministic provider fixtures. Fixtures isolate external provider availability from CI.
