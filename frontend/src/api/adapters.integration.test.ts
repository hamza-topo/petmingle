import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';
import { authenticatedIdentityRequest, signInRequest, signOutRequest } from '../auth/auth.api';
import { accountLocationsRequest } from '../features/account-location/location.api';
import { discoveryRequest } from '../features/discovery/discovery.api';
import { petInteractionRequest } from '../features/discovery/interaction.api';
import { relationshipsRequest } from '../features/matches/matches.api';
import { conversationsRequest, threadRequest, messageSendRequest, markConversationSeenRequest } from '../features/messaging/messaging.api';
import { currentPetProfileRequest } from '../features/own-profile/profile.api';
import { petCreateRequest } from '../features/profile-creation/pet-create.api';
import { taxonomyRequest } from '../features/profile-creation/taxonomy.api';

vi.mock('./config', () => ({
  apiUrl: (path: string) => 'https://api.example.test/api/v.0' + path,
  mediaUrl: (path: string) => 'https://api.example.test/storage/' + path,
}));

const fetchMock = vi.fn();
const token = 'test-token';
const meta = { current_page: 1, last_page: 1, per_page: 24, total: 0 };
const pet = { id: 42, user_id: 10, species_id: 3, race_id: 9, name: 'Milo', age: 4, sexe: null, color: null, images: [], about: null };
const envelope = (data: unknown) => ({ success: true, message: 'OK', data });
function respond(data: unknown, status = 200) {
  return new Response(JSON.stringify(data), { status });
}
const calls: [string, () => Promise<unknown>][] = [
  ['identity', () => authenticatedIdentityRequest(token)],
  ['taxonomy', () => taxonomyRequest(token)],
  ['profile', () => currentPetProfileRequest({ token, petId: 42, userId: 10 })],
  ['creation', () => petCreateRequest({ speciesId: 3, raceId: 9, name: 'Milo', age: 4, photo: null }, token)],
  ['locations', () => accountLocationsRequest({ token, userId: 10 })],
  ['discovery', () => discoveryRequest({ token })],
  ['interaction', () => petInteractionRequest({ token, targetPetId: 55, interaction: 'liked' })],
  ['relationships', () => relationshipsRequest({ token, currentPetId: 42 })],
  ['conversations', () => conversationsRequest(token)],
  ['thread', () => threadRequest({ token, receiverUserId: 11 })],
  ['send', () => messageSendRequest({ token, conversationId: 7, currentUserId: 10, receiverUserId: 11, content: 'Hello' })],
  ['seen', () => markConversationSeenRequest({ token, conversationId: 7 })],
];

describe('Real API adapters with simulated HTTP responses', () => {
  beforeEach(() => { fetchMock.mockReset(); vi.stubGlobal('fetch', fetchMock); });
  afterEach(() => vi.unstubAllGlobals());

  it('authenticates, loads nullable pet identity and revokes the token', async () => {
    fetchMock
      .mockResolvedValueOnce(respond({ success: true, token, token_type: 'Bearer' }))
      .mockResolvedValueOnce(respond(envelope({ user: { id: 10, name: 'Owner', email: 'owner@example.test' }, pet: null })))
      .mockResolvedValueOnce(respond({ success: true, message: 'Logged out.' }));
    await expect(signInRequest({ email: 'owner@example.test', password: 'test-password' })).resolves.toMatchObject({ token });
    await expect(authenticatedIdentityRequest(token)).resolves.toMatchObject({ pet: null, user: { id: 10 } });
    await signOutRequest(token);
    expect(fetchMock.mock.calls.map(([url]) => url)).toEqual([
      'https://api.example.test/api/v.0/sign-in',
      'https://api.example.test/api/v.0/me',
      'https://api.example.test/api/v.0/sign-out',
    ]);
    expect(fetchMock.mock.calls[0][1].headers.has('Authorization')).toBe(false);
    expect(fetchMock.mock.calls[2][1].headers.get('Authorization')).toBe('Bearer test-token');
  });

  it('loads taxonomy and hydrates profile with server statistics', async () => {
    fetchMock.mockImplementation(async (url: string) => {
      const path = new URL(url).pathname;
      if (path.endsWith('/species')) return respond(envelope([{ id: 3, name: 'Dog' }]));
      if (path.endsWith('/races')) return respond(envelope([{ id: 9, species_id: 3, name: 'Labrador' }]));
      if (path.endsWith('/pets/42')) return respond(envelope(pet));
      if (path.endsWith('/races/9')) return respond(envelope({ id: 9, species_id: 3, name: 'Labrador' }));
      if (path.endsWith('/statistics')) return respond(envelope({ matches: 2, likes_sent: 5 }));
      throw new Error('Unexpected URL: ' + url);
    });
    await expect(taxonomyRequest(token)).resolves.toMatchObject({ species: [{ id: 3 }], races: [{ id: 9 }] });
    await expect(currentPetProfileRequest({ token, petId: 42, userId: 10 })).resolves.toMatchObject({ id: 42, userId: 10, breed: 'Labrador', statistics: { matches: 2, likesSent: 5 } });
  });

  it('creates a pet through multipart without sending client ownership', async () => {
    fetchMock.mockResolvedValue(respond(envelope(pet), 201));
    await expect(petCreateRequest({ speciesId: 3, raceId: 9, name: ' Milo ', age: 4, photo: null }, token)).resolves.toMatchObject({ id: 42 });
    const { body, headers } = fetchMock.mock.calls[0][1];
    expect(body.get('name')).toBe('Milo');
    expect(body.get('species_id')).toBe('3');
    expect(body.has('user_id')).toBe(false);
    expect(headers.has('Content-Type')).toBe(false);
  });

  it('maps empty Discovery and relationship responses without fixture fallback', async () => {
    fetchMock.mockImplementation(async () => respond({ ...envelope([]), meta }));
    await expect(discoveryRequest({ token })).resolves.toEqual({ pets: [], meta });
    await expect(relationshipsRequest({ token, currentPetId: 42 })).resolves.toEqual({ matches: [], mismatches: [] });
  });

  it('maps persisted interaction, send and seen results with distinct Pet/User IDs', async () => {
    fetchMock
      .mockResolvedValueOnce(respond(envelope({ id: 1, from_pet_id: 42, to_pet_id: 55, interaction: 'liked' })))
      .mockResolvedValueOnce(respond(envelope({ id: 90, conversation_id: 7, sender_id: 10, receiver_id: 11, content: 'Hello', created_at: '2026-10-06T12:00:00Z' })))
      .mockResolvedValueOnce(respond(envelope({ conversation_id: 7, marked_count: 1, unread_count: 0 })));
    await expect(petInteractionRequest({ token, targetPetId: 55, interaction: 'liked' })).resolves.toMatchObject({ to_pet_id: 55 });
    await expect(messageSendRequest({ token, conversationId: 7, currentUserId: 10, receiverUserId: 11, content: 'Hello' })).resolves.toMatchObject({ id: '90', senderId: '10', content: 'Hello' });
    await expect(markConversationSeenRequest({ token, conversationId: 7 })).resolves.toEqual({ conversationId: 7, markedCount: 1, unreadCount: 0 });
    expect(JSON.parse(fetchMock.mock.calls[0][1].body)).toEqual({ to_pet_id: 55 });
    expect(JSON.parse(fetchMock.mock.calls[1][1].body)).toEqual({ receiver_id: 11, content: 'Hello' });
  });

  it.each(calls)('%s propagates HTTP authorization and validation failures', async (_name, run) => {
    fetchMock.mockImplementation(async () => respond({ success: false, message: 'Forbidden.' }, 403));
    await expect(run()).rejects.toMatchObject({ name: 'ApiError', status: 403 });
    fetchMock.mockImplementation(async () => respond({ success: false, message: 'Validation failed.', errors: { field: ['Invalid'] } }, 422));
    await expect(run()).rejects.toMatchObject({ status: 422, payload: { errors: { field: ['Invalid'] } } });
  });

  it.each(calls)('%s propagates network failures without calling a live API', async (_name, run) => {
    fetchMock.mockImplementation(async () => { throw new TypeError('Offline'); });
    await expect(run()).rejects.toThrow('Offline');
  });

  it('rejects inconsistent identities after parsing the network response', async () => {
    fetchMock.mockResolvedValue(respond(envelope({ ...pet, user_id: 999 })));
    await expect(currentPetProfileRequest({ token, petId: 42, userId: 10 })).rejects.toThrow('Current pet identity');
    fetchMock.mockResolvedValue(respond(envelope({ id: 1, to_pet_id: 999, interaction: 'liked' })));
    await expect(petInteractionRequest({ token, targetPetId: 55, interaction: 'liked' })).rejects.toThrow('requested target');
  });

  it('rejects malformed collections instead of showing invented data', async () => {
    fetchMock.mockResolvedValue(respond(envelope(null)));
    await expect(discoveryRequest({ token })).rejects.toThrow();
    await expect(conversationsRequest(token)).rejects.toThrow();
    await expect(threadRequest({ token, receiverUserId: 11 })).rejects.toThrow();
  });
});
