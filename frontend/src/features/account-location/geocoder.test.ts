import { afterEach, describe, expect, it, vi } from 'vitest';
import { reversePlace, searchPlaces } from './geocoder';
import { formatAccountLocation } from './location.api';
const feature = { geometry: { coordinates: [-7.5898, 33.5731] }, properties: { name: 'Private street', city: 'Casablanca', country: 'Morocco' } };
afterEach(() => vi.unstubAllGlobals());
describe('Place adapter', () => {
  it('normalizes GeoJSON coordinates and uses city names instead of street addresses', async () => {
    const fetcher = vi.fn().mockResolvedValue({ ok: true, json: async () => ({ features: [feature, { geometry: { coordinates: [500, 100] } }] }) });
    vi.stubGlobal('fetch', fetcher);
    const signal = new AbortController().signal;
    expect(await searchPlaces(' Casablanca ', signal)).toEqual([{ latitude: 33.5731, longitude: -7.5898, label: 'Casablanca, Morocco' }]);
    const [url, options] = fetcher.mock.calls[0];
    expect(url.searchParams.get('q')).toBe('Casablanca');
    expect(options).toEqual({ signal, credentials: 'omit', referrerPolicy: 'no-referrer' });
  });
  it('rounds reverse lookup coordinates and preserves cancellation', async () => {
    const fetcher = vi.fn().mockResolvedValue({ ok: true, json: async () => ({ features: [feature] }) });
    vi.stubGlobal('fetch', fetcher);
    const signal = new AbortController().signal;
    expect(await reversePlace({ latitude: 33.573123, longitude: -7.589823 }, signal)).toBe('Casablanca, Morocco');
    expect(fetcher.mock.calls[0][0].searchParams.get('lat')).toBe('33.57');
    expect(fetcher.mock.calls[0][0].searchParams.get('lon')).toBe('-7.59');
    expect(fetcher.mock.calls[0][1].signal).toBe(signal);
  });
  it('reports provider failure and handles an empty result', async () => {
    vi.stubGlobal('fetch', vi.fn().mockResolvedValueOnce({ ok: false }).mockResolvedValueOnce({ ok: true, json: async () => ({ features: [] }) }));
    const signal = new AbortController().signal;
    await expect(searchPlaces('City', signal)).rejects.toThrow('temporarily unavailable');
    expect(await reversePlace({ latitude: 0, longitude: 0 }, signal)).toBeNull();
  });
  it('never displays raw coordinates for legacy records', () => {
    const old = { id: 1, user_id: 10, latitude: 33.5731, longitude: -7.5898 };
    expect(formatAccountLocation(old)).toBe('Selected area');
    expect(formatAccountLocation({ ...old, label: 'Casablanca, Morocco' })).toBe('Casablanca, Morocco');
  });
});
