import {
  Fragment,
  useEffect,
  useRef,
} from 'react';
import {
  CheckCheck,
  MoreVertical,
  PawPrint,
  Phone,
  Smile,
  Video,
} from 'lucide-react';

import {
  messageDay,
  messageTime,
  pairName,
} from '../messaging.presentation';
import type {
  ChatMessage,
  Conversation,
} from '../messaging.types';
import { MessageComposer } from './MessageComposer';
import {
  PetAvatar,
  PetPairAvatar,
} from './PetAvatar';

function MessageBubble({
  message,
  conversation,
}: {
  message: ChatMessage;
  conversation: Conversation;
}) {
  const outgoing =
    message.senderId
    === conversation.currentOwnerId;

  const owner = conversation.owners.find(
    item => item.id === message.senderId,
  );

  const pet = owner
    ? conversation.pets.find(
        item =>
          item.id === owner.representedPetId,
      )
    : undefined;

  if (!owner || !pet) {
    return null;
  }

  return (
    <li
      className={`chat-message chat-message--${outgoing ? 'outgoing' : 'incoming'}`}
      aria-label={`Message from ${pet.name}’s owner`}
    >
      <PetAvatar pet={pet} />

      <div className="chat-message-content">
        <p>
          {message.content
            .split(/(🐾|🙂)/u)
            .map((part, index) =>
              part === '🐾'
              || part === '🙂'
                ? (
                    <Fragment key={index}>
                      <span className="sr-only">
                        {part}
                      </span>
                      {part === '🐾'
                        ? (
                            <PawPrint
                              size={19}
                              className="message-emoji"
                              fill="currentColor"
                              aria-hidden="true"
                            />
                          )
                        : (
                            <Smile
                              size={19}
                              className="message-emoji message-emoji--smile"
                              aria-hidden="true"
                            />
                          )}
                    </Fragment>
                  )
                : part,
            )}
        </p>

        <div className="chat-message-meta">
          <time dateTime={message.timestamp}>
            {messageTime(message.timestamp)}
          </time>

          {message.receipt === 'read' && (
            <CheckCheck
              size={20}
              aria-label="Message seen"
            />
          )}
        </div>
      </div>
    </li>
  );
}

export function ChatThread({
  conversation,
}: {
  conversation: Conversation;
}) {
  const timeline =
    useRef<HTMLOListElement>(null);

  useEffect(() => {
    if (timeline.current) {
      timeline.current.scrollTop =
        timeline.current.scrollHeight;
    }
  }, [
    conversation.id,
    conversation.messages.length,
  ]);

  const firstMessage =
    conversation.messages[0];

  return (
    <section
      className="chat-thread"
      aria-labelledby="chat-title"
    >
      <header className="chat-thread-header">
        <PetPairAvatar
          pets={conversation.pets}
        />

        <div>
          <h2 id="chat-title">
            {pairName(conversation)}
          </h2>
        </div>

        <div className="chat-call-actions">
          <button
            type="button"
            disabled
            aria-label="Video call — unavailable"
          >
            <Video size={27} />
          </button>

          <button
            type="button"
            disabled
            aria-label="Phone call — unavailable"
          >
            <Phone size={26} />
          </button>

          <button
            type="button"
            disabled
            aria-label="More conversation actions — unavailable"
          >
            <MoreVertical size={26} />
          </button>
        </div>
      </header>

      {firstMessage ? (
        <>
          <div className="chat-day">
            {messageDay(
              firstMessage.timestamp,
            )}
          </div>

          <ol
            ref={timeline}
            className="chat-timeline"
            aria-label="Messages in active conversation"
            aria-live="polite"
            aria-relevant="additions"
          >
            {conversation.messages.map(
              (message, index) => (
                <Fragment key={message.id}>
                  {index > 0
                    && message.timestamp.slice(0, 10)
                      !== conversation.messages[
                        index - 1
                      ].timestamp.slice(0, 10)
                    && (
                      <li className="chat-day">
                        {messageDay(
                          message.timestamp,
                        )}
                      </li>
                    )}

                  <MessageBubble
                    message={message}
                    conversation={conversation}
                  />
                </Fragment>
              ),
            )}
          </ol>
        </>
      ) : (
        <div
          className="chat-empty"
          role="status"
        >
          No messages in this conversation yet.
        </div>
      )}

      <MessageComposer />
    </section>
  );
}
