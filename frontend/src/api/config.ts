const rawApiBaseUrl = import.meta.env.VITE_API_BASE_URL?.trim();

export const API_BASE_URL = rawApiBaseUrl
  ? rawApiBaseUrl.replace(/\/+$/, '')
  : '';

export function apiUrl(path: string): string {
  if (!API_BASE_URL) {
    throw new Error('VITE_API_BASE_URL is not configured.');
  }

  const normalizedPath = path.startsWith('/') ? path : `/${path}`;

  return `${API_BASE_URL}${normalizedPath}`;
}

const rawMediaBaseUrl =
  import.meta.env.VITE_MEDIA_BASE_URL?.trim();

export const MEDIA_BASE_URL = rawMediaBaseUrl
  ? rawMediaBaseUrl.replace(/\/+$/, '')
  : '';

export function mediaUrl(path: string): string {
  const normalizedPath = path.replace(/^\/+/, '');

  if (MEDIA_BASE_URL) {
    return `${MEDIA_BASE_URL}/storage/${normalizedPath}`;
  }

  if (API_BASE_URL) {
    try {
      const origin = new URL(API_BASE_URL).origin;

      return `${origin}/storage/${normalizedPath}`;
    } catch {
      // Fall through to a same-origin relative path.
    }
  }

  return `/storage/${normalizedPath}`;
}
