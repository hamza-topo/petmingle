import { render, screen, within } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { MemoryRouter } from 'react-router';
import { describe, expect, it } from 'vitest';
import { App } from '../../app/App';
import { conversations, pairName } from './messaging.fixtures';
function renderMessages() { return render(<MemoryRouter initialEntries={['/messages']}><App /></MemoryRouter>); }

describe('Messaging', () => {
  it('renders its route, active navigation and conversation fixtures', () => {
    renderMessages();
    expect(screen.getByRole('heading', { name: 'Messages', level: 1 })).toBeVisible();
    expect(screen.getByRole('link', { name: 'Match & Chat' })).toHaveAttribute('aria-current', 'page');
    const list = within(screen.getByRole('list', { name: 'Conversations' }));
    expect(list.getAllByRole('listitem')).toHaveLength(conversations.length);
    for (const conversation of conversations) expect(list.getByRole('button', { name: pairName(conversation) })).toBeVisible();
    expect(list.getByRole('button', { name: 'Nala & Milo' })).toHaveAttribute('aria-current', 'true');
  });
  it('renders the active thread and both matched pets from fixtures', () => {
    renderMessages();
    const thread = within(screen.getByRole('region', { name: 'Nala & Milo' }));
    expect(thread.getByText('Matched March 28, 2024')).toBeVisible();
    for (const message of conversations[0].messages) expect(thread.getByRole('list', { name: 'Messages in active conversation' })).toHaveTextContent(message.content);
    const details = within(screen.getByRole('complementary', { name: 'Active match details' }));
    expect(details.getByRole('article', { name: 'Nala details' })).toHaveTextContent('Golden Retriever');
    expect(details.getByRole('article', { name: 'Milo details' })).toHaveTextContent('Pembroke Welsh Corgi');
  });
  it('switches the thread and details to the selected conversation', async () => {
    const user = userEvent.setup(); renderMessages();
    await user.click(screen.getByRole('button', { name: 'Luna & Toby' }));
    const thread = within(screen.getByRole('region', { name: 'Luna & Toby' }));
    expect(thread.getByText('Let’s meet at the dog park!')).toBeVisible();
    expect(screen.queryByText('Hi! Nala would love to meet Milo!')).not.toBeInTheDocument();
    expect(screen.getByRole('article', { name: 'Toby details' })).toBeVisible();
    expect(screen.queryByRole('article', { name: 'Milo details' })).not.toBeInTheDocument();
  });
  it('sends a local message, clears the composer and retains it only in its thread', async () => {
    const user = userEvent.setup(); renderMessages();
    expect(screen.getByRole('button', { name: 'Send message' })).toBeDisabled();
    await user.type(screen.getByRole('textbox', { name: 'Write a message' }), ' See you at ten! ');
    await user.click(screen.getByRole('button', { name: 'Send message' }));
    expect(within(screen.getByRole('list', { name: 'Messages in active conversation' })).getByText('See you at ten!')).toBeVisible();
    expect(screen.getByRole('textbox', { name: 'Write a message' })).toHaveValue('');
    await user.click(screen.getByRole('button', { name: 'Luna & Toby' }));
    expect(within(screen.getByRole('list', { name: 'Messages in active conversation' })).queryByText('See you at ten!')).not.toBeInTheDocument();
    await user.click(screen.getByRole('button', { name: 'Nala & Milo' }));
    expect(within(screen.getByRole('list', { name: 'Messages in active conversation' })).getByText('See you at ten!')).toBeVisible();
  });
  it('filters local conversations and clears unread state on opening', async () => {
    const user = userEvent.setup(); renderMessages();
    await user.click(screen.getByRole('button', { name: 'Unread' }));
    expect(within(screen.getByRole('list', { name: 'Conversations' })).getAllByRole('listitem')).toHaveLength(1);
    await user.click(screen.getByRole('button', { name: 'Nala & Milo' }));
    expect(screen.getByRole('status')).toHaveTextContent('No conversations found.');
    await user.click(screen.getByRole('button', { name: 'All' }));
    await user.type(screen.getByRole('searchbox', { name: 'Search pets or conversations' }), 'Rocky');
    expect(within(screen.getByRole('list', { name: 'Conversations' })).getAllByRole('listitem')).toHaveLength(1);
    expect(screen.getByRole('button', { name: 'Rocky & Zoe' })).toBeVisible();
  });
});
