import { useState } from 'react';
import { conversations, messageTime } from './messaging.fixtures';
import { MessagingHeader } from './components/MessagingHeader';
import { ConversationList } from './components/ConversationList';
import { ChatThread } from './components/ChatThread';
import { MatchDetailsPanel } from './components/MatchDetailsPanel';
export function MessagingPage() {
  const [items, setItems] = useState(() => conversations.map(item => ({ ...item, messages: [...item.messages] })));
  const [activeId, setActiveId] = useState(conversations[0].id);
  const active = items.find(item => item.id === activeId)!;
  function selectConversation(id: string) { setActiveId(id); setItems(previous => previous.map(item => item.id === id ? { ...item, unreadCount: 0 } : item)); }
  function send(content: string) {
    const timestamp = new Date().toISOString();
    setItems(previous => previous.map(item => item.id === activeId ? { ...item, preview: content, activityLabel: messageTime(timestamp), unreadCount: 0,
      messages: [...item.messages, { id: crypto.randomUUID(), senderId: item.currentOwnerId, content, timestamp }] } : item));
  }
  return <div className="messaging-page"><a className="skip-link" href="#messaging-main">Skip to messages</a><MessagingHeader /><main id="messaging-main" className="messaging-layout">
    <ConversationList conversations={items} activeId={activeId} onSelect={selectConversation} />
    <ChatThread conversation={active} onSend={send} /><MatchDetailsPanel conversation={active} />
  </main></div>;
}
