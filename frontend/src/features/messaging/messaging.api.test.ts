import {
  beforeEach,
  describe,
  expect,
  it,
  vi,
} from 'vitest';

import { apiRequest } from '../../api/client';
import { mediaUrl } from '../../api/config';
import {
  conversationListRequest,
  messageThreadRequest,
} from './messaging.api';

vi.mock('../../api/client', () => ({
  apiRequest: vi.fn(),
}));

const mockedApiRequest = vi.mocked(apiRequest);

const pageMeta = {
  current_page: 1,
  last_page: 1,
  per_page: 50,
  total: 1,
};

const links = {
  first: '/first',
  last: '/last',
  prev: null,
  next: null,
};

describe('Messaging API adapters', () => {
  beforeEach(() => {
    mockedApiRequest.mockReset();
  });

  it('maps conversation participants using explicit User and Pet IDs', async () => {
    mockedApiRequest.mockResolvedValueOnce({
      success: true,
      message: 'List of conversations.',
      data: [
        {
          id: 70,
          current_user_id: 10,
          participants: [
            {
              user_id: 20,
              name: 'Milo owner',
              pet: {
                id: 51,
                user_id: 20,
                name: 'Milo',
                species_id: 1,
                race: {
                  id: 5,
                  species_id: 1,
                  name: 'Corgi',
                },
                age_years: 2,
                sex: 0,
                images: ['pets/milo.jpg'],
              },
            },
            {
              user_id: 10,
              name: 'Hamza',
              pet: {
                id: 42,
                user_id: 10,
                name: 'Nala',
                species_id: 1,
                race: {
                  id: 3,
                  species_id: 1,
                  name: 'Golden Retriever',
                },
                age_years: 3,
                sex: 1,
                images: ['pets/nala.png'],
              },
            },
          ],
          last_message: {
            id: 501,
            conversation_id: 70,
            sender_user_id: 20,
            receiver_user_id: 10,
            content: 'See you Saturday',
            is_seen: false,
            created_at: '2026-10-06T09:30:00.000000Z',
            updated_at: '2026-10-06T09:30:00.000000Z',
          },
          unread_count: 2,
        },
      ],
      meta: pageMeta,
      links,
    });

    await expect(
      conversationListRequest({
        token: 'test-token',
        currentUserId: 10,
      }),
    ).resolves.toEqual([
      {
        id: '70',
        pets: [
          {
            id: '42',
            name: 'Nala',
            photo: {
              src: mediaUrl('pets/nala.png'),
              alt: 'Nala pet photo',
              placeholder: 'Nala',
            },
            breed: 'Golden Retriever',
            ageYears: 3,
            sex: 'female',
          },
          {
            id: '51',
            name: 'Milo',
            photo: {
              src: mediaUrl('pets/milo.jpg'),
              alt: 'Milo pet photo',
              placeholder: 'Milo',
            },
            breed: 'Corgi',
            ageYears: 2,
            sex: 'male',
          },
        ],
        owners: [
          {
            id: '10',
            name: 'Hamza',
            representedPetId: '42',
          },
          {
            id: '20',
            name: 'Milo owner',
            representedPetId: '51',
          },
        ],
        currentOwnerId: '10',
        preview: 'See you Saturday',
        lastActivityAt:
          '2026-10-06T09:30:00.000000Z',
        unreadCount: 2,
        messages: [],
        interests: [],
      },
    ]);

    expect(mockedApiRequest).toHaveBeenCalledWith(
      '/conversations?per_page=50',
      { token: 'test-token' },
    );
  });

  it('omits an unusable conversation when a participant has no pet', async () => {
    mockedApiRequest.mockResolvedValueOnce({
      success: true,
      message: 'List of conversations.',
      data: [
        {
          id: 70,
          current_user_id: 10,
          participants: [
            {
              user_id: 10,
              name: 'Hamza',
              pet: null,
            },
            {
              user_id: 20,
              name: 'Other',
              pet: null,
            },
          ],
          last_message: null,
          unread_count: 0,
        },
      ],
      meta: pageMeta,
      links,
    });

    await expect(
      conversationListRequest({
        token: 'test-token',
        currentUserId: 10,
      }),
    ).resolves.toEqual([]);
  });

  it('rejects conversation data scoped to a different current User', async () => {
    mockedApiRequest.mockResolvedValueOnce({
      success: true,
      message: 'List of conversations.',
      data: [
        {
          id: 70,
          current_user_id: 999,
          participants: [],
          last_message: null,
          unread_count: 0,
        },
      ],
      meta: pageMeta,
      links,
    });

    await expect(
      conversationListRequest({
        token: 'test-token',
        currentUserId: 10,
      }),
    ).rejects.toThrow(
      'Messaging conversation response is invalid.',
    );
  });

  it('maps a bidirectional thread and preserves seen state', async () => {
    mockedApiRequest.mockResolvedValueOnce({
      success: true,
      message: 'Message thread.',
      data: [
        {
          id: 1,
          conversation_id: 70,
          sender_user_id: 20,
          receiver_user_id: 10,
          content: 'Incoming',
          is_seen: false,
          created_at: '2026-10-06T09:30:00.000000Z',
          updated_at: '2026-10-06T09:30:00.000000Z',
        },
        {
          id: 2,
          conversation_id: 70,
          sender_user_id: 10,
          receiver_user_id: 20,
          content: 'Outgoing',
          is_seen: true,
          created_at: '2026-10-06T09:31:00.000000Z',
          updated_at: '2026-10-06T09:31:00.000000Z',
        },
      ],
      meta: {
        ...pageMeta,
        total: 2,
      },
      links,
    });

    await expect(
      messageThreadRequest({
        token: 'test-token',
        conversationId: 70,
        currentUserId: 10,
        receiverUserId: 20,
      }),
    ).resolves.toEqual([
      {
        id: '1',
        senderId: '20',
        content: 'Incoming',
        timestamp: '2026-10-06T09:30:00.000000Z',
        receipt: undefined,
      },
      {
        id: '2',
        senderId: '10',
        content: 'Outgoing',
        timestamp: '2026-10-06T09:31:00.000000Z',
        receipt: 'read',
      },
    ]);

    expect(mockedApiRequest).toHaveBeenCalledWith(
      '/messages?receiver_id=20&per_page=50&page=1',
      { token: 'test-token' },
    );
  });

  it('rejects thread rows from an unrelated User', async () => {
    mockedApiRequest.mockResolvedValueOnce({
      success: true,
      message: 'Message thread.',
      data: [
        {
          id: 1,
          conversation_id: 70,
          sender_user_id: 30,
          receiver_user_id: 10,
          content: 'Wrong participant',
          is_seen: false,
          created_at: '2026-10-06T09:30:00.000000Z',
          updated_at: '2026-10-06T09:30:00.000000Z',
        },
      ],
      meta: pageMeta,
      links,
    });

    await expect(
      messageThreadRequest({
        token: 'test-token',
        conversationId: 70,
        currentUserId: 10,
        receiverUserId: 20,
      }),
    ).rejects.toThrow(
      'Messaging thread response does not preserve User ID semantics.',
    );
  });
});
