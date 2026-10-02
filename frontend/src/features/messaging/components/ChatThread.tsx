import { Fragment, useEffect, useRef } from 'react';
import { CheckCheck, MoreVertical, PawPrint, Phone, Smile, Video } from 'lucide-react';
import { messageDay, messageTime, pairName, type ChatMessage, type Conversation } from '../messaging.fixtures';
import { PetAvatar, PetPairAvatar } from './PetAvatar';
import { MessageComposer } from './MessageComposer';

function MessageBubble({ message, conversation }: { message: ChatMessage; conversation: Conversation }) {
  const outgoing = message.senderId === conversation.currentOwnerId;
  const owner = conversation.owners.find(item => item.id === message.senderId)!;
  const pet = conversation.pets.find(item => item.id === owner.representedPetId)!;
  return <li className={`chat-message chat-message--${outgoing ? 'outgoing' : 'incoming'}`} aria-label={`Message from ${pet.name}’s owner`}>
    <PetAvatar pet={pet} /><div className="chat-message-content"><p>{message.content.split(/(🐾|🙂)/u).map((part, index) => part === '🐾' || part === '🙂' ? <Fragment key={index}><span className="sr-only">{part}</span>{part === '🐾' ? <PawPrint size={19} className="message-emoji" fill="currentColor" aria-hidden="true" /> : <Smile size={19} className="message-emoji message-emoji--smile" aria-hidden="true" />}</Fragment> : part)}</p><div className="chat-message-meta"><time dateTime={message.timestamp}>{messageTime(message.timestamp)}</time>{message.receipt === 'read' && <CheckCheck size={20} aria-label="Read receipt shown in reference" />}</div></div>
  </li>;
}
export function ChatThread({ conversation, onSend }: { conversation: Conversation; onSend: (content: string) => void }) {
  const timeline = useRef<HTMLOListElement>(null);
  useEffect(() => { if (timeline.current) timeline.current.scrollTop = timeline.current.scrollHeight; }, [conversation.id, conversation.messages.length]);
  return <section className="chat-thread" aria-labelledby="chat-title">
    <header className="chat-thread-header"><PetPairAvatar pets={conversation.pets} /><div><h2 id="chat-title">{pairName(conversation)}</h2>{conversation.matchedOn && <p>Matched {new Date(`${conversation.matchedOn}T12:00:00`).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' })}</p>}</div><div className="chat-call-actions">
      <button type="button" disabled aria-label="Video call — unavailable"><Video size={27} /></button><button type="button" disabled aria-label="Phone call — unavailable"><Phone size={26} /></button><button type="button" disabled aria-label="More conversation actions — unavailable"><MoreVertical size={26} /></button>
    </div></header>
    <div className="chat-day">{messageDay(conversation.messages[0].timestamp)}</div>
    <ol ref={timeline} className="chat-timeline" aria-label="Messages in active conversation" aria-live="polite" aria-relevant="additions">{conversation.messages.map((message, index) => <Fragment key={message.id}>{index > 0 && message.timestamp.slice(0, 10) !== conversation.messages[index - 1].timestamp.slice(0, 10) && <li className="chat-day">{messageDay(message.timestamp)}</li>}<MessageBubble message={message} conversation={conversation} /></Fragment>)}</ol>
    <MessageComposer key={conversation.id} onSend={onSend} />
  </section>;
}
