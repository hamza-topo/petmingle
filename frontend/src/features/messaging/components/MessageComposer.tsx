import { useState } from 'react';
import { Image, SendHorizontal, Smile } from 'lucide-react';
export function MessageComposer({ onSend }: { onSend: (content: string) => void }) {
  const [draft, setDraft] = useState('');
  return <form className="message-composer" aria-label="Send a local message" onSubmit={event => { event.preventDefault(); const content = draft.trim(); if (!content) return; onSend(content); setDraft(''); }}>
    <button type="button" className="chat-round-control" disabled aria-label="Attach image — unavailable"><Image size={27} /></button>
    <button type="button" className="chat-round-control" disabled aria-label="Choose emoji — unavailable"><Smile size={27} /></button>
    <input aria-label="Write a message" placeholder="Write a message..." value={draft} onChange={event => setDraft(event.target.value)} />
    <button type="submit" className="chat-send" disabled={!draft.trim()} aria-label="Send message"><SendHorizontal size={27} aria-hidden="true" /></button>
  </form>;
}
