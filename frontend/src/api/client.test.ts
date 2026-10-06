import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';

const fetchMock = vi.fn();

describe('API transport at the fetch boundary', () => {
  beforeEach(() => {
    vi.stubEnv('VITE_API_BASE_URL', 'https://api.example.test/api/v.0');
    vi.resetModules();
    fetchMock.mockReset();
    vi.stubGlobal('fetch', fetchMock);
  });
  afterEach(() => { vi.unstubAllGlobals(); vi.unstubAllEnvs(); });

  async function request(path: string, options = {}) {
    const { apiRequest: configuredRequest } = await import('./client');
    return configuredRequest(path, options);
  }

  it('sends JSON with Bearer authorization and preserves request cancellation', async () => {
    fetchMock.mockResolvedValue(new Response('{"success":true,"data":[]}'));
    const signal = new AbortController().signal;
    await request('/likes', { method: 'POST', token: 'active-token', body: '{"to_pet_id":42}', signal });
    const [url, options] = fetchMock.mock.calls[0];
    expect(url).toBe('https://api.example.test/api/v.0/likes');
    expect(options.headers.get('Authorization')).toBe('Bearer active-token');
    expect(options.headers.get('Accept')).toBe('application/json');
    expect(options.headers.get('Content-Type')).toBe('application/json');
    expect(options.body).toBe('{"to_pet_id":42}');
    expect(options.signal).toBe(signal);
  });

  it('lets the browser assign the multipart boundary', async () => {
    fetchMock.mockResolvedValue(new Response('{"success":true}'));
    const body = new FormData();
    body.set('image', new File(['photo'], 'pet.png', { type: 'image/png' }));
    await request('/pets', { method: 'POST', token: 'active-token', body });
    const options = fetchMock.mock.calls[0][1];
    expect(options.body).toBe(body);
    expect(options.headers.has('Content-Type')).toBe(false);
  });

  it('does not send a Bearer header for public authentication', async () => {
    fetchMock.mockResolvedValue(new Response('{"success":true,"token":"issued"}'));
    await request('/sign-in', { method: 'POST', body: '{}' });
    expect(fetchMock.mock.calls[0][1].headers.has('Authorization')).toBe(false);
  });

  it.each([401, 403, 422, 429, 500])('preserves status %i and structured error details', async status => {
    const payload = { success: false, message: 'Rejected.', errors: { name: ['Invalid.'] } };
    fetchMock.mockResolvedValue(new Response(JSON.stringify(payload), { status }));
    await expect(request('/pets')).rejects.toMatchObject({ name: 'ApiError', status, message: 'Rejected.', payload });
  });

  it('rejects a legacy failure envelope even when HTTP status is 200', async () => {
    fetchMock.mockResolvedValue(new Response('{"success":false,"message":"Validation errors","data":{"name":["Required"]}}'));
    await expect(request('/pets')).rejects.toMatchObject({ status: 200, message: 'Validation errors' });
  });

  it.each(['<html>Gateway error</html>', ''])('handles non-JSON or empty HTTP failures', async body => {
    fetchMock.mockResolvedValue(new Response(body, { status: 502 }));
    await expect(request('/me')).rejects.toMatchObject({ status: 502, message: 'The request could not be completed.' });
  });

  it('preserves a network failure for retry handling', async () => {
    const failure = new TypeError('Failed to fetch');
    fetchMock.mockRejectedValue(failure);
    await expect(request('/me')).rejects.toBe(failure);
  });

  it('does not call fetch when the API URL is missing', async () => {
    vi.stubEnv('VITE_API_BASE_URL', '');
    vi.resetModules();
    await expect(request('/me')).rejects.toThrow('VITE_API_BASE_URL is not configured.');
    expect(fetchMock).not.toHaveBeenCalled();
  });
});
