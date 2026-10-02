import { useState } from 'react';
import { Search } from 'lucide-react';
import { pairName, type Conversation } from '../messaging.fixtures';
import { PetPairAvatar } from './PetAvatar';
export function ConversationList({ conversations, activeId, onSelect }: { conversations: Conversation[]; activeId: string; onSelect: (id: string) => void }) {
  const [search, setSearch] = useState('');
  const [filter, setFilter] = useState('All');
  const visible = conversations.filter(item => (filter !== 'Unread' || item.unreadCount > 0) && `${pairName(item)} ${item.preview}`.toLowerCase().includes(search.toLowerCase()));
  return <aside className="conversation-list" aria-labelledby="messages-title">
    <h1 id="messages-title">Messages</h1>
    <label className="conversation-search"><Search size={22} aria-hidden="true" /><input aria-label="Search pets or conversations" type="search" placeholder="Search pets or conversations..." value={search} onChange={event => setSearch(event.target.value)} /></label>
    <div className="conversation-filters" role="group" aria-label="Conversation filters">{['All', 'Matches', 'Unread'].map(label => <button type="button" key={label} aria-pressed={filter === label} onClick={() => setFilter(label)}>{label}</button>)}</div>
    <ul aria-label="Conversations">{visible.map(item => <li key={item.id}>
      <button type="button" className="conversation-row" aria-current={activeId === item.id ? 'true' : undefined} aria-label={pairName(item)} onClick={() => onSelect(item.id)}>
        <PetPairAvatar pets={item.pets} /><span className="conversation-summary"><strong>{pairName(item)}</strong><span>{item.preview}</span></span>
        <span className="conversation-activity"><span>{item.activityLabel}</span>{item.unreadCount > 0 && <span className="chat-unread" aria-label={`${item.unreadCount} unread message`}>{item.unreadCount}</span>}</span>
      </button>
    </li>)}</ul>
    {visible.length === 0 && <p className="chat-empty" role="status">No conversations found.</p>}
  </aside>;
}
