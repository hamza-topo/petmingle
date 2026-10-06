import type { LocationCoordinates } from './location.api';

export type Place = LocationCoordinates & { label: string };
const base = import.meta.env.VITE_GEOCODER_BASE_URL || 'https://photon.komoot.io';

function places(data: unknown): Place[] {
  if (!data || typeof data !== 'object' || !('features' in data) || !Array.isArray(data.features)) return [];
  return data.features.flatMap((feature: unknown): Place[] => {
    if (!feature || typeof feature !== 'object') return [];
    const item = feature as { geometry?: { coordinates?: unknown[] }; properties?: Record<string, unknown> };
    const [longitude, latitude] = item.geometry?.coordinates ?? [];
    if (typeof latitude !== 'number' || typeof longitude !== 'number' || !Number.isFinite(latitude) || !Number.isFinite(longitude) || Math.abs(latitude) > 90 || Math.abs(longitude) > 180) return [];
    const properties = item.properties ?? {};
    const name = [properties.city, properties.town, properties.village, properties.name].find(value => typeof value === 'string' && value.trim());
    if (typeof name !== 'string') return [];
    const country = typeof properties.country === 'string' ? properties.country : '';
    const label = [...new Set([name, country].filter(Boolean))].join(', ').slice(0, 160);
    return [{ latitude, longitude, label }];
  });
}

async function request(path: string, params: URLSearchParams, signal: AbortSignal): Promise<Place[]> {
  const url = new URL(`${base.replace(/\/$/, '')}/${path}/`);
  url.search = params.toString();
  // Geocoding is public: never forward the account token or cookies.
  const response = await fetch(url, { signal, credentials: 'omit', referrerPolicy: 'no-referrer' });
  if (!response.ok) throw new Error('Place search is temporarily unavailable.');
  return places(await response.json());
}

export function searchPlaces(query: string, signal: AbortSignal): Promise<Place[]> {
  return request('api', new URLSearchParams({ q: query.trim(), limit: '5', lang: 'en' }), signal);
}

export async function reversePlace(point: LocationCoordinates, signal: AbortSignal): Promise<string | null> {
  // The provider only needs an approximate point to identify the city.
  const results = await request('reverse', new URLSearchParams({ lat: point.latitude.toFixed(2), lon: point.longitude.toFixed(2), lang: 'en' }), signal);
  return results[0]?.label ?? null;
}
