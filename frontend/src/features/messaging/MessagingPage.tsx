import {
  useCallback,
  useEffect,
  useRef,
  useState,
} from 'react';

import { ApiError } from '../../api/errors';
import { describeApiFailure } from '../../api/presentation';
import { tokenStorage } from '../../auth/tokenStorage';
import { ApiState } from '../../components/ApiState';
import { ChatThread } from './components/ChatThread';
import { ConversationList } from './components/ConversationList';
import { MatchDetailsPanel } from './components/MatchDetailsPanel';
import { MessagingHeader } from './components/MessagingHeader';
import {
  conversationsRequest,
  markConversationSeenRequest,
  messageSendRequest,
  messageTime,
  otherParticipantUserId,
  threadRequest,
} from './messaging.api';
import type { Conversation } from './messaging.types';

type LoadStatus =
  | 'loading'
  | 'ready'
  | 'error';

export function MessagingPage() {
  const [status, setStatus] =
    useState<LoadStatus>('loading');
  const [items, setItems] =
    useState<Conversation[]>([]);
  const [activeId, setActiveId] =
    useState<string | null>(null);
  const [loadError, setLoadError] =
    useState<unknown | null>(null);

  const [threadStatus, setThreadStatus] =
    useState<LoadStatus>('ready');
  const [threadError, setThreadError] =
    useState<unknown | null>(null);
  const [sendError, setSendError] =
    useState<string | null>(null);
  const [seenError, setSeenError] =
    useState<string | null>(null);

  const threadRequestId = useRef(0);

  const active =
    items.find(item => item.id === activeId)
    ?? null;

  const loadConversations = useCallback(
    async () => {
      const token = tokenStorage.get();

      if (!token) {
        setItems([]);
        setActiveId(null);
        setLoadError(
          new ApiError(
            'Authentication token is missing.',
            401,
          ),
        );
        setStatus('error');
        return;
      }

      setStatus('loading');
      setLoadError(null);

      try {
        const conversations =
          await conversationsRequest(token);

        setItems(conversations);
        setActiveId(current => {
          if (
            current
            && conversations.some(
              item => item.id === current,
            )
          ) {
            return current;
          }

          return conversations[0]?.id ?? null;
        });
        setStatus('ready');
      } catch (caught) {
        setItems([]);
        setActiveId(null);
        setLoadError(caught);
        setStatus('error');
      }
    },
    [],
  );

  const loadThread = useCallback(
    async (conversation: Conversation) => {
      const token = tokenStorage.get();

      if (!token) {
        setThreadError(
          new ApiError(
            'Authentication token is missing.',
            401,
          ),
        );
        setThreadStatus('error');
        return;
      }

      const requestId = ++threadRequestId.current;

      setThreadStatus('loading');
      setThreadError(null);
      setSeenError(null);

      try {
        const messages = await threadRequest({
          token,
          receiverUserId:
            otherParticipantUserId(conversation),
        });

        if (
          requestId !== threadRequestId.current
        ) {
          return;
        }

        setItems(current =>
          current.map(item =>
            item.id === conversation.id
              ? {
                  ...item,
                  messages,
                }
              : item,
          ),
        );
        setThreadStatus('ready');

        if (conversation.unreadCount > 0) {
          try {
            const seen =
              await markConversationSeenRequest({
                token,
                conversationId:
                  Number(conversation.id),
              });

            if (
              requestId !== threadRequestId.current
            ) {
              return;
            }

            setItems(current =>
              current.map(item => {
                if (
                  item.id
                  !== conversation.id
                ) {
                  return item;
                }

                return {
                  ...item,
                  unreadCount:
                    seen.unreadCount,
                  messages:
                    item.messages.map(
                      message =>
                        message.senderId
                          !== item.currentOwnerId
                          ? {
                              ...message,
                              receipt: 'read',
                            }
                          : message,
                    ),
                };
              }),
            );
          } catch (caught) {
            if (
              requestId
              === threadRequestId.current
            ) {
              setSeenError(
                describeApiFailure(
                  caught,
                ).message,
              );
            }
          }
        }
      } catch (caught) {
        if (
          requestId !== threadRequestId.current
        ) {
          return;
        }

        setThreadError(caught);
        setThreadStatus('error');
      }
    },
    [],
  );

  useEffect(() => {
    void loadConversations();
  }, [loadConversations]);

  useEffect(() => {
    if (!active) {
      threadRequestId.current += 1;
      setThreadStatus('ready');
      setThreadError(null);
      return;
    }

    void loadThread(active);
  }, [activeId, loadThread]);

  function selectConversation(id: string) {
    setSendError(null);
    setSeenError(null);
    setActiveId(id);
  }

  async function sendMessage(content: string) {
    if (!active) {
      const error = new Error(
        'No active conversation is available.',
      );

      setSendError(
        describeApiFailure(error).message,
      );
      throw error;
    }

    const token = tokenStorage.get();

    if (!token) {
      const error = new ApiError(
        'Authentication token is missing.',
        401,
      );

      setSendError(
        describeApiFailure(error).message,
      );
      throw error;
    }

    const conversationId = active.id;
    const currentUserId =
      Number(active.currentOwnerId);
    const receiverUserId =
      otherParticipantUserId(active);

    setSendError(null);

    try {
      const sent = await messageSendRequest({
        token,
        conversationId: Number(conversationId),
        currentUserId,
        receiverUserId,
        content,
      });

      setItems(current =>
        current.map(item => {
          if (item.id !== conversationId) {
            return item;
          }

          const alreadyPresent =
            item.messages.some(
              message => message.id === sent.id,
            );

          return {
            ...item,
            preview: sent.content,
            activityLabel: messageTime(
              sent.timestamp,
            ),
            messages: alreadyPresent
              ? item.messages
              : [...item.messages, sent],
          };
        }),
      );
    } catch (caught) {
      setSendError(
        describeApiFailure(caught).message,
      );
      throw caught;
    }
  }

  const failure =
    loadError !== null
      ? describeApiFailure(loadError)
      : null;

  const threadFailure =
    threadError !== null
      ? describeApiFailure(threadError)
      : null;

  return (
    <div className="messaging-page">
      <a
        className="skip-link"
        href="#messaging-main"
      >
        Skip to messages
      </a>

      <MessagingHeader />

      <main
        id="messaging-main"
        className="messaging-layout"
      >
        <ConversationList
          conversations={items}
          activeId={activeId}
          onSelect={selectConversation}
        />

        {status === 'loading' && (
          <section className="chat-thread">
            <ApiState
              kind="loading"
              message="Loading conversations..."
            />
          </section>
        )}

        {status === 'error' && failure && (
          <section className="chat-thread">
            <ApiState
              kind="error"
              title="Messages unavailable"
              message={failure.message}
              onRetry={
                failure.retryable
                  ? () => void loadConversations()
                  : undefined
              }
            />
          </section>
        )}

        {status === 'ready' && !active && (
          <section className="chat-thread">
            <ApiState
              kind="empty"
              title="No conversations yet"
              message="Your persisted matches have not started a conversation yet."
            />
          </section>
        )}

        {status === 'ready'
          && active
          && threadStatus !== 'error' && (
            <>
              <ChatThread
                conversation={active}
                loading={
                  threadStatus === 'loading'
                }
                onSend={sendMessage}
                sendError={sendError}
                seenError={seenError}
              />
              <MatchDetailsPanel
                conversation={active}
              />
            </>
          )}

        {status === 'ready'
          && active
          && threadStatus === 'error'
          && threadFailure && (
            <>
              <section className="chat-thread">
                <ApiState
                  kind="error"
                  title="Conversation unavailable"
                  message={threadFailure.message}
                  onRetry={
                    threadFailure.retryable
                      ? () => void loadThread(active)
                      : undefined
                  }
                />
              </section>
              <MatchDetailsPanel
                conversation={active}
              />
            </>
          )}
      </main>
    </div>
  );
}
