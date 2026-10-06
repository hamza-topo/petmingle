/** Media received from the API may be unavailable; retain an explicit fallback. */
export interface ReferenceAsset {
  src: string | null;
  alt: string;
  placeholder: string;
  position?: string;
}

