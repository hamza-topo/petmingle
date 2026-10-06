import {
  render,
  screen,
  within,
} from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { MemoryRouter } from 'react-router';
import {
  afterEach,
  beforeEach,
  describe,
  expect,
  it,
  vi,
} from 'vitest';

import { ApiError } from '../../api/errors';
import { App } from '../../app/App';
import { useAuth } from '../../auth/AuthProvider';
import { tokenStorage } from '../../auth/tokenStorage';
import {
  authenticatedAuthState,
  authenticatedWithoutPetAuthState,
} from '../../test/authFixtures';
import {
  conversationListRequest,
  messageThreadRequest,
} from './messaging.api';
import type {
  ChatMessage,
  Conversation,
} from './messaging.types';

vi.mock('../../auth/AuthProvider', () => ({
  useAuth: vi.fn(),
}));

vi.mock('./messaging.api', () => ({
  conversationListRequest: vi.fn(),
  messageThreadRequest: vi.fn(),
}));

const mockedUseAuth = vi.mocked(useAuth);
const mockedConversationListRequest =
  vi.mocked(conversationListRequest);
const mockedMessageThreadRequest =
  vi.mocked(messageThreadRequest);

function conversation({
  id,
  otherUserId,
  otherPetId,
  otherPetName,
  preview,
  unreadCount = 0,
}: {
  id: string;
  otherUserId: string;
  otherPetId: string;
  otherPetName: string;
  preview: string;
  unreadCount?: number;
}): Conversation {
  return {
    id,
    pets: [
      {
        id: '42',
        name: 'Nala',
        photo: {
          src: '/storage/pets/nala.jpg',
          alt: 'Nala pet photo',
          placeholder: 'Nala',
        },
        breed: 'Golden Retriever',
        ageYears: 3,
        sex: 'female',
      },
      {
        id: otherPetId,
        name: otherPetName,
        photo: {
          src: null,
          alt: `${otherPetName} pet profile`,
          placeholder: otherPetName,
        },
        breed: 'Mixed',
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
        id: otherUserId,
        name: null,
        representedPetId: otherPetId,
      },
    ],
    currentOwnerId: '10',
    preview,
    lastActivityAt:
      '2026-10-06T09:30:00.000000Z',
    unreadCount,
    messages: [],
    interests: [],
  };
}

const nalaMilo = conversation({
  id: '70',
  otherUserId: '20',
  otherPetId: '51',
  otherPetName: 'Milo',
  preview: 'See you Saturday',
  unreadCount: 1,
});

const nalaLuna = conversation({
  id: '71',
  otherUserId: '30',
  otherPetId: '61',
  otherPetName: 'Luna',
  preview: 'Dog park tomorrow?',
});

const miloMessages: ChatMessage[] = [
  {
    id: '500',
    senderId: '20',
    content: 'Hi from Milo',
    timestamp:
      '2026-10-06T09:30:00.000000Z',
  },
  {
    id: '501',
    senderId: '10',
    content: 'Hi Milo',
    timestamp:
      '2026-10-06T09:31:00.000000Z',
    receipt: 'read',
  },
];

const lunaMessages: ChatMessage[] = [
  {
    id: '600',
    senderId: '30',
    content: 'Hi from Luna',
    timestamp:
      '2026-10-06T09:40:00.000000Z',
  },
];

beforeEach(() => {
  tokenStorage.set('test-token');

  mockedUseAuth.mockReturnValue(
    authenticatedAuthState(),
  );

  mockedConversationListRequest.mockReset();
  mockedMessageThreadRequest.mockReset();

  mockedConversationListRequest.mockResolvedValue([
    nalaMilo,
    nalaLuna,
  ]);

  mockedMessageThreadRequest.mockImplementation(
    async ({ receiverUserId }) =>
      receiverUserId === 20
        ? miloMessages
        : lunaMessages,
  );
});

afterEach(() => {
  tokenStorage.clear();
});

function renderMessages() {
  return render(
    <MemoryRouter initialEntries={['/messages']}>
      <App />
    </MemoryRouter>,
  );
}

describe('Messaging', () => {
  it('loads persisted conversations and the first active thread', async () => {
    renderMessages();

    expect(
      screen.getByRole('status'),
    ).toHaveTextContent(
      'Loading conversations...',
    );

    expect(
      await screen.findByRole('button', {
        name: 'Nala & Milo',
      }),
    ).toHaveAttribute(
      'aria-current',
      'true',
    );

    expect(
      mockedConversationListRequest,
    ).toHaveBeenCalledWith({
      token: 'test-token',
      currentUserId: 10,
    });

    expect(
      mockedMessageThreadRequest,
    ).toHaveBeenCalledWith({
      token: 'test-token',
      conversationId: 70,
      currentUserId: 10,
      receiverUserId: 20,
    });

    const thread = within(
      await screen.findByRole('region', {
        name: 'Nala & Milo',
      }),
    );

    expect(
      thread.getByRole('list', {
        name: 'Messages in active conversation',
      }),
    ).toHaveTextContent('Hi from Milo');

    expect(
      thread.getByText('Hi Milo'),
    ).toBeVisible();

    expect(
      screen.getByRole('link', {
        name: 'Match & Chat',
      }),
    ).toHaveAttribute(
      'aria-current',
      'page',
    );
  });

  it('loads the selected conversation thread using its other User ID', async () => {
    const user = userEvent.setup();

    renderMessages();

    await screen.findByText('Hi from Milo');

    await user.click(
      screen.getByRole('button', {
        name: 'Nala & Luna',
      }),
    );

    expect(
      await screen.findByText('Hi from Luna'),
    ).toBeVisible();

    expect(
      mockedMessageThreadRequest,
    ).toHaveBeenLastCalledWith({
      token: 'test-token',
      conversationId: 71,
      currentUserId: 10,
      receiverUserId: 30,
    });

    expect(
      screen.queryByText('Hi from Milo'),
    ).not.toBeInTheDocument();
  });

  it('keeps backend unread state until a read mutation exists', async () => {
    const user = userEvent.setup();

    renderMessages();

    await screen.findByText('Hi from Milo');

    expect(
      screen.getByLabelText(
        '1 unread message',
      ),
    ).toBeVisible();

    await user.click(
      screen.getByRole('button', {
        name: 'Nala & Milo',
      }),
    );

    expect(
      screen.getByLabelText(
        '1 unread message',
      ),
    ).toBeVisible();
  });

  it('renders an explicit empty state when no persisted conversation exists', async () => {
    mockedConversationListRequest
      .mockResolvedValueOnce([]);

    renderMessages();

    expect(
      await screen.findByText(
        'No conversations yet',
      ),
    ).toBeVisible();

    expect(
      mockedMessageThreadRequest,
    ).not.toHaveBeenCalled();
  });

  it('surfaces a safe conversation-list failure', async () => {
    mockedConversationListRequest
      .mockRejectedValueOnce(
        new ApiError(
          'Sensitive backend detail',
          500,
        ),
      );

    renderMessages();

    expect(
      await screen.findByRole('alert'),
    ).toHaveTextContent(
      'PetMingle is temporarily unavailable. Please try again.',
    );

    expect(
      screen.queryByText(
        'Sensitive backend detail',
      ),
    ).not.toBeInTheDocument();
  });

  it('surfaces a safe active-thread failure', async () => {
    mockedMessageThreadRequest
      .mockRejectedValueOnce(
        new ApiError(
          'Sensitive authorization detail',
          403,
        ),
      );

    renderMessages();

    expect(
      await screen.findByRole('alert'),
    ).toHaveTextContent(
      'You do not have permission to access this information.',
    );

    expect(
      screen.getByRole('button', {
        name: 'Nala & Milo',
      }),
    ).toBeVisible();
  });

  it('uses authenticated identity and disables fake local sends until #126', async () => {
    renderMessages();

    await screen.findByText('Hi from Milo');

    const account = screen.getByRole(
      'button',
      {
        name: 'Hamza account — unavailable',
      },
    );

    expect(
      within(account).getByRole('img'),
    ).toHaveAccessibleName(
      /Hamza avatar/,
    );

    expect(
      screen.getByRole('textbox', {
        name: 'Write a message',
      }),
    ).toBeDisabled();

    expect(
      screen.getByRole('button', {
        name: 'Send message — unavailable',
      }),
    ).toBeDisabled();
  });

  it('keeps the no-pet state ahead of Messaging API loading', () => {
    mockedUseAuth.mockReturnValue(
      authenticatedWithoutPetAuthState(),
    );

    renderMessages();

    expect(
      screen.getByRole('heading', {
        name: 'Create your pet profile to continue',
      }),
    ).toBeVisible();

    expect(
      mockedConversationListRequest,
    ).not.toHaveBeenCalled();

    expect(
      mockedMessageThreadRequest,
    ).not.toHaveBeenCalled();
  });
});
