import {
  beforeEach,
  describe,
  expect,
  it,
  vi,
} from 'vitest';

import { apiRequest } from '../../api/client';
import {
  conversationsRequest,
  mapConversation,
  markConversationSeenRequest,
  messageSendRequest,
  otherParticipantUserId,
  threadRequest,
} from './messaging.api';

vi.mock('../../api/client', () => ({
  apiRequest: vi.fn(),
}));

const mockedApiRequest = vi.mocked(apiRequest);

beforeEach(() => {
  vi.clearAllMocks();
});

const conversationApi = {
  id: 7,
  current_user_id: 10,
  participants: [
    {
      user_id: 10,
      name: 'Hamza',
      pet: {
        id: 42,
        user_id: 10,
        name: 'Nala',
        species_id: 1,
        race: {
          id: 5,
          species_id: 1,
          name: 'Golden Retriever',
        },
        age_years: 3,
        sex: 1,
        images: ['pets/nala.jpg'],
      },
    },
    {
      user_id: 20,
      name: 'Alex',
      pet: {
        id: 51,
        user_id: 20,
        name: 'Milo',
        species_id: 1,
        race: {
          id: 6,
          species_id: 1,
          name: 'Corgi',
        },
        age_years: 2,
        sex: 0,
        images: [],
      },
    },
  ] as [
    {
      user_id: number;
      name: string;
      pet: {
        id: number;
        user_id: number;
        name: string;
        species_id: number;
        race: {
          id: number;
          species_id: number;
          name: string;
        };
        age_years: number;
        sex: number;
        images: string[];
      };
    },
    {
      user_id: number;
      name: string;
      pet: {
        id: number;
        user_id: number;
        name: string;
        species_id: number;
        race: {
          id: number;
          species_id: number;
          name: string;
        };
        age_years: number;
        sex: number;
        images: string[];
      };
    },
  ],
  last_message: {
    id: 99,
    conversation_id: 7,
    sender_user_id: 20,
    receiver_user_id: 10,
    content: 'Hello',
    is_seen: false,
    created_at: '2026-10-06T10:00:00.000Z',
    updated_at: '2026-10-06T10:00:00.000Z',
  },
  unread_count: 1,
};

describe('messaging api', () => {
  it('maps explicit User and Pet identity domains', () => {
    const mapped = mapConversation(conversationApi);

    expect(mapped.id).toBe('7');
    expect(mapped.currentOwnerId).toBe('10');

    expect(mapped.owners).toEqual([
      {
        id: '10',
        name: 'Hamza',
        representedPetId: '42',
      },
      {
        id: '20',
        name: 'Alex',
        representedPetId: '51',
      },
    ]);

    expect(mapped.pets[0]).toMatchObject({
      id: '42',
      name: 'Nala',
      breed: 'Golden Retriever',
      ageYears: 3,
      sex: 'female',
    });

    expect(mapped.pets[1]).toMatchObject({
      id: '51',
      name: 'Milo',
      sex: 'male',
    });

    expect(mapped.preview).toBe('Hello');
    expect(mapped.unreadCount).toBe(1);
    expect(mapped.messages[0]).toMatchObject({
      id: '99',
      senderId: '20',
      content: 'Hello',
    });

    expect(otherParticipantUserId(mapped)).toBe(20);
  });

  it('requests persisted conversation summaries', async () => {
    mockedApiRequest.mockResolvedValue({
      success: true,
      message: 'List of conversations.',
      data: [conversationApi],
      meta: {
        current_page: 1,
        last_page: 1,
        per_page: 50,
        total: 1,
      },
      links: {
        first: null,
        last: null,
        prev: null,
        next: null,
      },
    });

    const result =
      await conversationsRequest('token');

    expect(mockedApiRequest).toHaveBeenCalledWith(
      '/conversations?per_page=50',
      {
        method: 'GET',
        token: 'token',
      },
    );

    expect(result).toHaveLength(1);
    expect(result[0].id).toBe('7');
  });

  it('requests and maps a bidirectional persisted thread', async () => {
    mockedApiRequest.mockResolvedValue({
      success: true,
      message: 'Message thread.',
      data: [
        {
          id: 1,
          conversation_id: 7,
          sender_user_id: 20,
          receiver_user_id: 10,
          content: 'Incoming',
          is_seen: false,
          created_at: '2026-10-06T10:00:00.000Z',
          updated_at: '2026-10-06T10:00:00.000Z',
        },
        {
          id: 2,
          conversation_id: 7,
          sender_user_id: 10,
          receiver_user_id: 20,
          content: 'Outgoing',
          is_seen: true,
          created_at: '2026-10-06T10:01:00.000Z',
          updated_at: '2026-10-06T10:01:00.000Z',
        },
      ],
      meta: {
        current_page: 1,
        last_page: 1,
        per_page: 50,
        total: 2,
      },
      links: {
        first: null,
        last: null,
        prev: null,
        next: null,
      },
    });

    const result = await threadRequest({
      token: 'token',
      receiverUserId: 20,
    });

    expect(mockedApiRequest).toHaveBeenCalledWith(
      '/messages?receiver_id=20&per_page=50&page=1',
      {
        method: 'GET',
        token: 'token',
      },
    );

    expect(result).toEqual([
      {
        id: '1',
        senderId: '20',
        content: 'Incoming',
        timestamp: '2026-10-06T10:00:00.000Z',
        receipt: undefined,
      },
      {
        id: '2',
        senderId: '10',
        content: 'Outgoing',
        timestamp: '2026-10-06T10:01:00.000Z',
        receipt: 'read',
      },
    ]);
  });

  it('marks a conversation seen without client-supplied receiver identity', async () => {
    mockedApiRequest.mockResolvedValue({
      success: true,
      message: 'Conversation marked as seen.',
      data: {
        conversation_id: 7,
        marked_count: 3,
        unread_count: 0,
      },
    });

    const result =
      await markConversationSeenRequest({
        token: 'token',
        conversationId: 7,
      });

    expect(mockedApiRequest).toHaveBeenCalledWith(
      '/conversations/7/seen',
      {
        method: 'PUT',
        token: 'token',
      },
    );

    expect(result).toEqual({
      conversationId: 7,
      markedCount: 3,
      unreadCount: 0,
    });
  });

  it('sends only receiver identity and content, then maps the persisted message', async () => {
    mockedApiRequest.mockResolvedValue({
      success: true,
      message: 'Message created.',
      data: {
        id: 77,
        conversation_id: 7,
        sender_id: 10,
        receiver_id: 20,
        content: 'Persisted hello',
        is_seen: false,
        created_at: '2026-10-06T11:00:00.000Z',
        updated_at: '2026-10-06T11:00:00.000Z',
      },
    });

    const result = await messageSendRequest({
      token: 'token',
      conversationId: 7,
      currentUserId: 10,
      receiverUserId: 20,
      content: 'Persisted hello',
    });

    expect(mockedApiRequest).toHaveBeenCalledWith(
      '/messages',
      {
        method: 'POST',
        token: 'token',
        body: JSON.stringify({
          receiver_id: 20,
          content: 'Persisted hello',
        }),
      },
    );

    expect(
      JSON.parse(
        mockedApiRequest.mock.calls[0][1]
          ?.body as string,
      ),
    ).not.toHaveProperty('sender_id');

    expect(
      JSON.parse(
        mockedApiRequest.mock.calls[0][1]
          ?.body as string,
      ),
    ).not.toHaveProperty('conversation_id');

    expect(result).toEqual({
      id: '77',
      senderId: '10',
      content: 'Persisted hello',
      timestamp: '2026-10-06T11:00:00.000Z',
      receipt: undefined,
    });
  });

  it('rejects a created-message response with the wrong sender identity', async () => {
    mockedApiRequest.mockResolvedValue({
      success: true,
      message: 'Message created.',
      data: {
        id: 77,
        conversation_id: 7,
        sender_id: 999,
        receiver_id: 20,
        content: 'Forged sender',
        is_seen: false,
        created_at: '2026-10-06T11:00:00.000Z',
        updated_at: '2026-10-06T11:00:00.000Z',
      },
    });

    await expect(
      messageSendRequest({
        token: 'token',
        conversationId: 7,
        currentUserId: 10,
        receiverUserId: 20,
        content: 'Forged sender',
      }),
    ).rejects.toThrow(
      'Created message response does not preserve authenticated conversation identity.',
    );
  });

  it('rejects mixed identity domains from the backend', () => {
    expect(() =>
      mapConversation({
        ...conversationApi,
        participants: [
          {
            ...conversationApi.participants[0],
            pet: {
              ...conversationApi.participants[0].pet,
              user_id: 999,
            },
          },
          conversationApi.participants[1],
        ],
      }),
    ).toThrow(
      'Messaging participant is missing a valid persisted Pet identity.',
    );
  });
});
