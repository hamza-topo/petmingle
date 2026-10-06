import {
  render,
  screen,
  waitFor,
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
import { ApiError } from '../../api/errors';
import { useAuth } from '../../auth/AuthProvider';
import { tokenStorage } from '../../auth/tokenStorage';
import { authenticatedAuthState } from '../../test/authFixtures';
import {
  conversationsRequest,
  messageSendRequest,
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
    messageSendRequest: vi.fn(),
    threadRequest: vi.fn(),
  };
});

const mockedUseAuth = vi.mocked(useAuth);
const mockedConversationsRequest =
  vi.mocked(conversationsRequest);
const mockedThreadRequest =
  vi.mocked(threadRequest);
const mockedMessageSendRequest =
  vi.mocked(messageSendRequest);

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

const sentMessage: ChatMessage = {
  id: '77',
  senderId: '10',
  content: 'Persisted hello',
  timestamp: '2026-10-06T11:00:00.000Z',
};

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

  mockedMessageSendRequest.mockResolvedValue(
    sentMessage,
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

  it('persists a message and updates the active thread after success', async () => {
    const user = userEvent.setup();

    renderMessages();

    await screen.findByText('Hello from Milo');

    const input = screen.getByRole('textbox', {
      name: 'Write a message',
    });

    await user.type(input, ' Persisted hello ');
    await user.click(
      screen.getByRole('button', {
        name: 'Send message',
      }),
    );

    expect(
      mockedMessageSendRequest,
    ).toHaveBeenCalledWith({
      token: 'test-token',
      conversationId: 101,
      currentUserId: 10,
      receiverUserId: 20,
      content: 'Persisted hello',
    });

    const timeline = within(
      screen.getByRole('list', {
        name: 'Messages in active conversation',
      }),
    );

    expect(
      await timeline.findByText('Persisted hello'),
    ).toBeVisible();

    await waitFor(() =>
      expect(input).toHaveValue(''),
    );
  });

  it('prevents duplicate submissions while a send is pending', async () => {
    const user = userEvent.setup();
    let resolveSend:
      | ((message: ChatMessage) => void)
      | undefined;

    mockedMessageSendRequest.mockReturnValue(
      new Promise(resolve => {
        resolveSend = resolve;
      }),
    );

    renderMessages();

    await screen.findByText('Hello from Milo');

    await user.type(
      screen.getByRole('textbox', {
        name: 'Write a message',
      }),
      'Only once',
    );

    await user.click(
      screen.getByRole('button', {
        name: 'Send message',
      }),
    );

    const pendingButton = screen.getByRole(
      'button',
      {
        name: 'Sending message',
      },
    );

    expect(pendingButton).toBeDisabled();
    expect(
      mockedMessageSendRequest,
    ).toHaveBeenCalledTimes(1);

    await user.click(pendingButton);

    expect(
      mockedMessageSendRequest,
    ).toHaveBeenCalledTimes(1);

    resolveSend?.({
      ...sentMessage,
      content: 'Only once',
    });

    const timeline = within(
      screen.getByRole('list', {
        name: 'Messages in active conversation',
      }),
    );

    expect(
      await timeline.findByText('Only once'),
    ).toBeVisible();
  });

  it('preserves the draft after a validation failure', async () => {
    const user = userEvent.setup();

    mockedMessageSendRequest.mockRejectedValue(
      new ApiError(
        'The Field Content is too long!',
        422,
      ),
    );

    renderMessages();

    await screen.findByText('Hello from Milo');

    const input = screen.getByRole('textbox', {
      name: 'Write a message',
    });

    await user.type(input, 'Keep this draft');
    await user.click(
      screen.getByRole('button', {
        name: 'Send message',
      }),
    );

    expect(
      await screen.findByRole('alert'),
    ).toHaveTextContent(
      'PetMingle could not process this request.',
    );

    expect(input).toHaveValue('Keep this draft');
  });

  it('preserves the draft and surfaces authorization failure', async () => {
    const user = userEvent.setup();

    mockedMessageSendRequest.mockRejectedValue(
      new ApiError(
        'Contact is not allowed.',
        403,
      ),
    );

    renderMessages();

    await screen.findByText('Hello from Milo');

    const input = screen.getByRole('textbox', {
      name: 'Write a message',
    });

    await user.type(input, 'Still here');
    await user.click(
      screen.getByRole('button', {
        name: 'Send message',
      }),
    );

    expect(
      await screen.findByRole('alert'),
    ).toHaveTextContent(
      'You do not have permission to access this information.',
    );

    expect(input).toHaveValue('Still here');
  });
});
