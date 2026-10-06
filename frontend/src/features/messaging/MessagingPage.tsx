import {
  useCallback,
  useEffect,
  useRef,
  useState,
} from 'react';

import { ApiError } from '../../api/errors';
import { describeApiFailure } from '../../api/presentation';
import { useAuth } from '../../auth/AuthProvider';
import { tokenStorage } from '../../auth/tokenStorage';
import { ApiState } from '../../components/ApiState';
import {
  createPrivateRealtimeClient,
  type PrivateRealtimeEvent,
} from '../../realtime/pusherClient';
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
  typingRequest,
} from './messaging.api';
import {
  isRealtimeMatchEvent,
  realtimeMessageFrom,
  realtimeTypingFrom,
} from './messaging.realtime';
import type { Conversation } from './messaging.types';

type LoadStatus =
  | 'loading'
  | 'ready'
  | 'error';

export function MessagingPage() {
  const { user } = useAuth();

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
  const [typingByUser, setTypingByUser] =
    useState<Record<string, boolean>>({});

  const threadRequestId = useRef(0);
  const activeIdRef = useRef<string | null>(null);
  const itemsRef = useRef<Conversation[]>([]);
  const typingExpiryTimers = useRef(
    new Map<string, ReturnType<typeof setTimeout>>(),
  );
  const outboundTypingTimer = useRef<
    ReturnType<typeof setTimeout> | null
  >(null);
  const outboundTypingState = useRef(false);
  const realtimeMessageIds = useRef(
    new Set<string>(),
  );

  const active =
    items.find(item => item.id === activeId)
    ?? null;

  useEffect(() => {
    itemsRef.current = items;
  }, [items]);

  useEffect(() => {
    activeIdRef.current = activeId;
  }, [activeId]);

  const loadConversations = useCallback(
    async (
      showLoading = true,
    ): Promise<Conversation[]> => {
      const token = tokenStorage.get();

      if (!token) {
        if (showLoading) {
          setItems([]);
          setActiveId(null);
          setLoadError(
            new ApiError(
              'Authentication token is missing.',
              401,
            ),
          );
          setStatus('error');
        }

        return [];
      }

      if (showLoading) {
        setStatus('loading');
        setLoadError(null);
      }

      try {
        const conversations =
          await conversationsRequest(token);

        setItems(current =>
          conversations.map(serverItem => {
            if (showLoading) {
              return serverItem;
            }

            const existing = current.find(
              item => item.id === serverItem.id,
            );

            return existing
              ? {
                  ...serverItem,
                  messages:
                    existing.messages.length > 0
                      ? existing.messages
                      : serverItem.messages,
                }
              : serverItem;
          }),
        );

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

        if (showLoading) {
          setStatus('ready');
        }

        return conversations;
      } catch (caught) {
        if (showLoading) {
          setItems([]);
          setActiveId(null);
          setLoadError(caught);
          setStatus('error');
        }

        return [];
      }
    },
    [],
  );

  const persistSeen = useCallback(
    async (
      conversationId: string,
      exposeFailure: boolean,
    ) => {
      const token = tokenStorage.get();

      if (!token) {
        return;
      }

      try {
        const seen =
          await markConversationSeenRequest({
            token,
            conversationId:
              Number(conversationId),
          });

        setItems(current =>
          current.map(item =>
            item.id === conversationId
              ? {
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
                }
              : item,
          ),
        );
      } catch (caught) {
        if (exposeFailure) {
          setSeenError(
            describeApiFailure(
              caught,
            ).message,
          );
        }
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
          await persistSeen(
            conversation.id,
            true,
          );
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
    [persistSeen],
  );

  const handleRealtimeMessage = useCallback(
    async (data: unknown) => {
      const incoming = realtimeMessageFrom(data);

      if (!incoming || !user) {
        return;
      }

      if (
        realtimeMessageIds.current.has(
          incoming.message.id,
        )
      ) {
        return;
      }

      realtimeMessageIds.current.add(
        incoming.message.id,
      );

      if (
        realtimeMessageIds.current.size > 500
      ) {
        const oldest =
          realtimeMessageIds.current
            .values()
            .next()
            .value;

        if (oldest) {
          realtimeMessageIds.current.delete(
            oldest,
          );
        }
      }

      const conversation =
        itemsRef.current.find(
          item =>
            item.id
            === incoming.conversationId,
        );

      if (!conversation) {
        await loadConversations(false);
        return;
      }

      const alreadyPresent =
        conversation.messages.some(
          message =>
            message.id
            === incoming.message.id,
        );

      if (alreadyPresent) {
        return;
      }

      const isIncoming =
        incoming.receiverUserId
        === String(user.id);
      const isActive =
        activeIdRef.current
        === incoming.conversationId;

      setItems(current =>
        current.map(item => {
          if (
            item.id
            !== incoming.conversationId
          ) {
            return item;
          }

          if (
            item.messages.some(
              message =>
                message.id
                === incoming.message.id,
            )
          ) {
            return item;
          }

          return {
            ...item,
            preview: incoming.message.content,
            activityLabel: messageTime(
              incoming.message.timestamp,
            ),
            unreadCount:
              isIncoming && !isActive
                ? item.unreadCount + 1
                : item.unreadCount,
            messages: [
              ...item.messages,
              incoming.message,
            ],
          };
        }),
      );

      if (isIncoming && isActive) {
        await persistSeen(
          incoming.conversationId,
          false,
        );
      }
    },
    [
      loadConversations,
      persistSeen,
      user,
    ],
  );

  const handleRealtimeTyping = useCallback(
    (data: unknown) => {
      const typing = realtimeTypingFrom(data);

      if (
        !typing
        || !user
        || typing.receiverUserId
          !== String(user.id)
      ) {
        return;
      }

      const previousTimer =
        typingExpiryTimers.current.get(
          typing.senderUserId,
        );

      if (previousTimer) {
        clearTimeout(previousTimer);
        typingExpiryTimers.current.delete(
          typing.senderUserId,
        );
      }

      setTypingByUser(current => ({
        ...current,
        [typing.senderUserId]:
          typing.isWriting,
      }));

      if (typing.isWriting) {
        const timer = setTimeout(() => {
          setTypingByUser(current => ({
            ...current,
            [typing.senderUserId]:
              false,
          }));
          typingExpiryTimers.current.delete(
            typing.senderUserId,
          );
        }, 3000);

        typingExpiryTimers.current.set(
          typing.senderUserId,
          timer,
        );
      }
    },
    [user],
  );

  const handleRealtimeEvent = useCallback(
    (event: PrivateRealtimeEvent) => {
      if (event.name === 'new.message') {
        void handleRealtimeMessage(
          event.data,
        );
        return;
      }

      if (
        event.name === 'new.match'
        && isRealtimeMatchEvent(event.data)
      ) {
        void loadConversations(false);
        return;
      }

      if (
        event.name === 'is-writing-to'
      ) {
        handleRealtimeTyping(
          event.data,
        );
      }
    },
    [
      handleRealtimeMessage,
      handleRealtimeTyping,
      loadConversations,
    ],
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

  useEffect(() => {
    const token = tokenStorage.get();

    if (!token || !user) {
      return;
    }

    const client =
      createPrivateRealtimeClient({
        token,
        userId: user.id,
        callbacks: {
          onEvent: handleRealtimeEvent,
          onReconnect: () => {
            void (async () => {
              const refreshed =
                await loadConversations(false);

              const currentId =
                activeIdRef.current;

              if (!currentId) {
                return;
              }

              const current =
                refreshed.find(
                  item =>
                    item.id === currentId,
                )
                ?? itemsRef.current.find(
                  item =>
                    item.id === currentId,
                );

              if (current) {
                await loadThread(current);
              }
            })();
          },
        },
      });

    client.connect();

    return () => {
      client.disconnect();
    };
  }, [
    handleRealtimeEvent,
    loadConversations,
    loadThread,
    user,
  ]);

  useEffect(
    () => () => {
      for (
        const timer
        of typingExpiryTimers.current.values()
      ) {
        clearTimeout(timer);
      }

      typingExpiryTimers.current.clear();

      if (outboundTypingTimer.current) {
        clearTimeout(
          outboundTypingTimer.current,
        );
      }
    },
    [],
  );

  async function publishTyping(
    isWriting: boolean,
  ) {
    if (!active) {
      return;
    }

    const token = tokenStorage.get();

    if (!token) {
      return;
    }

    const receiverUserId =
      otherParticipantUserId(active);

    if (outboundTypingTimer.current) {
      clearTimeout(
        outboundTypingTimer.current,
      );
      outboundTypingTimer.current = null;
    }

    if (isWriting) {
      if (!outboundTypingState.current) {
        outboundTypingState.current = true;

        void typingRequest({
          token,
          receiverUserId,
          isWriting: true,
        }).catch(() => {
          // Typing is best-effort; HTTP messaging remains functional.
        });
      }

      outboundTypingTimer.current =
        setTimeout(() => {
          outboundTypingTimer.current = null;

          if (!outboundTypingState.current) {
            return;
          }

          outboundTypingState.current = false;

          void typingRequest({
            token,
            receiverUserId,
            isWriting: false,
          }).catch(() => {});
        }, 1500);

      return;
    }

    if (!outboundTypingState.current) {
      return;
    }

    outboundTypingState.current = false;

    void typingRequest({
      token,
      receiverUserId,
      isWriting: false,
    }).catch(() => {});
  }

  function selectConversation(id: string) {
    void publishTyping(false);
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
        conversationId: Number(
          conversationId,
        ),
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
              message =>
                message.id === sent.id,
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

  const otherUserId = active
    ? String(
        otherParticipantUserId(active),
      )
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
                  ? () =>
                      void loadConversations()
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
                onTypingChange={
                  publishTyping
                }
                isOtherTyping={
                  otherUserId !== null
                  && typingByUser[
                    otherUserId
                  ] === true
                }
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
                      ? () =>
                          void loadThread(
                            active,
                          )
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
