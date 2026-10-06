import {
  render,
  screen,
  within,
} from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { MemoryRouter } from 'react-router';
import {
  beforeEach,
  describe,
  expect,
  it,
  vi,
} from 'vitest';

import { App } from '../../app/App';
import { useAuth } from '../../auth/AuthProvider';
import { tokenStorage } from '../../auth/tokenStorage';
import { authenticatedAuthState } from '../../test/authFixtures';
import {
  conversationsRequest,
  threadRequest,
} from './messaging.api';
import type {
  ChatMessage,
  Conversation,
} from './messaging.types';

vi.mock('../../auth/AuthProvider', () => ({
  useAuth: vi.fn(),
}));

vi.mock('./messaging.api', async importOriginal => {
  const actual =
    await importOriginal<
      typeof import('./messaging.api')
    >();

  return {
    ...actual,
    conversationsRequest: vi.fn(),
    threadRequest: vi.fn(),
  };
});

const mockedUseAuth = vi.mocked(useAuth);
const mockedConversationsRequest =
  vi.mocked(conversationsRequest);
const mockedThreadRequest =
  vi.mocked(threadRequest);

function pet(
  id: string,
  name: string,
) {
  return {
    id,
    name,
    photo: {
      src: null,
      alt: `${name} pet profile`,
      placeholder: name,
    },
    breed: 'Mixed',
    ageYears: 3,
  };
}

const persistedConversations: Conversation[] = [
  {
    id: '101',
    pets: [pet('42', 'Nala'), pet('51', 'Milo')],
    owners: [
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
    ],
    currentOwnerId: '10',
    preview: 'Latest with Milo',
    activityLabel: '9:30 AM',
    unreadCount: 1,
    messages: [],
    interests: [],
  },
  {
    id: '102',
    pets: [pet('42', 'Nala'), pet('61', 'Luna')],
    owners: [
      {
        id: '10',
        name: 'Hamza',
        representedPetId: '42',
      },
      {
        id: '30',
        name: 'Sam',
        representedPetId: '61',
      },
    ],
    currentOwnerId: '10',
    preview: 'Latest with Luna',
    activityLabel: '8:00 AM',
    unreadCount: 0,
    messages: [],
    interests: [],
  },
];

const miloMessages: ChatMessage[] = [
  {
    id: '1',
    senderId: '20',
    content: 'Hello from Milo',
    timestamp: '2026-10-06T09:00:00.000Z',
  },
  {
    id: '2',
    senderId: '10',
    content: 'Hello back',
    timestamp: '2026-10-06T09:01:00.000Z',
    receipt: 'read',
  },
];

const lunaMessages: ChatMessage[] = [
  {
    id: '3',
    senderId: '30',
    content: 'Hello from Luna',
    timestamp: '2026-10-06T08:00:00.000Z',
  },
];

function renderMessages() {
  return render(
    <MemoryRouter initialEntries={['/messages']}>
      <App />
    </MemoryRouter>,
  );
}

beforeEach(() => {
  vi.clearAllMocks();
  window.localStorage.clear();
  tokenStorage.set('test-token');

  mockedUseAuth.mockReturnValue(
    authenticatedAuthState(),
  );

  mockedConversationsRequest.mockResolvedValue(
    persistedConversations,
  );

  mockedThreadRequest.mockImplementation(
    async ({ receiverUserId }) =>
      receiverUserId === 20
        ? miloMessages
        : lunaMessages,
  );
});

describe('Messaging persisted reads', () => {
  it('loads persisted conversations and the first active thread', async () => {
    renderMessages();

    expect(
      screen.getByText('Loading conversations...'),
    ).toBeVisible();

    const list = within(
      await screen.findByRole('list', {
        name: 'Conversations',
      }),
    );

    expect(
      await list.findByRole('button', {
        name: 'Nala & Milo',
      }),
    ).toHaveAttribute('aria-current', 'true');

    expect(
      list.getByRole('button', {
        name: 'Nala & Luna',
      }),
    ).toBeVisible();

    expect(
      await screen.findByText('Hello from Milo'),
    ).toBeVisible();
    expect(
      screen.getByText('Hello back'),
    ).toBeVisible();

    expect(
      mockedConversationsRequest,
    ).toHaveBeenCalledWith('test-token');

    expect(mockedThreadRequest).toHaveBeenCalledWith({
      token: 'test-token',
      receiverUserId: 20,
    });
  });

  it('switches conversations and loads the correct persisted thread', async () => {
    const user = userEvent.setup();

    renderMessages();

    await screen.findByText('Hello from Milo');

    await user.click(
      screen.getByRole('button', {
        name: 'Nala & Luna',
      }),
    );

    expect(
      await screen.findByText('Hello from Luna'),
    ).toBeVisible();

    expect(
      screen.queryByText('Hello from Milo'),
    ).not.toBeInTheDocument();

    expect(mockedThreadRequest).toHaveBeenLastCalledWith({
      token: 'test-token',
      receiverUserId: 30,
    });
  });

  it('renders an empty state when there are no persisted conversations', async () => {
    mockedConversationsRequest.mockResolvedValue([]);

    renderMessages();

    expect(
      await screen.findByText('No conversations yet'),
    ).toBeVisible();

    expect(mockedThreadRequest).not.toHaveBeenCalled();
  });

  it('renders a safe failure state when conversation loading fails', async () => {
    mockedConversationsRequest.mockRejectedValue(
      new Error('Network failed'),
    );

    renderMessages();

    expect(
      await screen.findByText('Messages unavailable'),
    ).toBeVisible();

    expect(
      screen.getByRole('alert'),
    ).toBeVisible();
  });

  it('renders a safe thread failure without replacing the selected conversation identity', async () => {
    mockedThreadRequest.mockRejectedValue(
      new Error('Thread failed'),
    );

    renderMessages();

    expect(
      await screen.findByText(
        'Conversation unavailable',
      ),
    ).toBeVisible();

    expect(
      screen.getByRole('article', {
        name: 'Milo details',
      }),
    ).toBeVisible();
  });

  it('does not fake local sends before issue 126', async () => {
    renderMessages();

    await screen.findByText('Hello from Milo');

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
});
