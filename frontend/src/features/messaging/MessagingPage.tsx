import {
  useCallback,
  useEffect,
  useState,
} from 'react';

import { ApiError } from '../../api/errors';
import { describeApiFailure } from '../../api/presentation';
import { useAuth } from '../../auth/AuthProvider';
import { tokenStorage } from '../../auth/tokenStorage';
import { ApiState } from '../../components/ApiState';
import {
  conversationListRequest,
  messageThreadRequest,
} from './messaging.api';
import type { Conversation } from './messaging.types';
import { ChatThread } from './components/ChatThread';
import { ConversationList } from './components/ConversationList';
import { MatchDetailsPanel } from './components/MatchDetailsPanel';
import { MessagingHeader } from './components/MessagingHeader';

type LoadStatus =
  | 'loading'
  | 'ready'
  | 'error';

export function MessagingPage() {
  const { user } = useAuth();

  const [status, setStatus] =
    useState<LoadStatus>('loading');
  const [threadStatus, setThreadStatus] =
    useState<LoadStatus>('loading');
  const [items, setItems] =
    useState<Conversation[]>([]);
  const [activeId, setActiveId] =
    useState<string | null>(null);
  const [error, setError] =
    useState<unknown | null>(null);
  const [threadError, setThreadError] =
    useState<unknown | null>(null);
  const [reloadKey, setReloadKey] =
    useState(0);
  const [
    threadReloadKey,
    setThreadReloadKey,
  ] = useState(0);

  const loadConversations = useCallback(
    async () => {
      if (!user) {
        return;
      }

      const token = tokenStorage.get();

      if (!token) {
        setError(
          new ApiError(
            'Authentication token is missing.',
            401,
          ),
        );
        setStatus('error');
        return;
      }

      setStatus('loading');
      setError(null);

      try {
        const result =
          await conversationListRequest({
            token,
            currentUserId: user.id,
          });

        setItems(result);
        setActiveId(current => {
          if (
            current
            && result.some(
              item => item.id === current,
            )
          ) {
            return current;
          }

          return result[0]?.id ?? null;
        });
        setStatus('ready');
      } catch (caught) {
        setItems([]);
        setActiveId(null);
        setError(caught);
        setStatus('error');
      }
    },
    [user],
  );

  useEffect(() => {
    void loadConversations();
  }, [loadConversations, reloadKey]);

  const active =
    activeId === null
      ? null
      : items.find(
          item => item.id === activeId,
        ) ?? null;

  useEffect(() => {
    let cancelled = false;

    async function loadThread() {
      if (
        status !== 'ready'
        || !active
        || !user
      ) {
        return;
      }

      const token = tokenStorage.get();
      const receiver = active.owners.find(
        owner =>
          owner.id
          !== active.currentOwnerId,
      );

      if (!token) {
        if (!cancelled) {
          setThreadError(
            new ApiError(
              'Authentication token is missing.',
              401,
            ),
          );
          setThreadStatus('error');
        }
        return;
      }

      if (!receiver) {
        if (!cancelled) {
          setThreadError(
            new Error(
              'Conversation receiver is unavailable.',
            ),
          );
          setThreadStatus('error');
        }
        return;
      }

      setThreadStatus('loading');
      setThreadError(null);

      try {
        const messages =
          await messageThreadRequest({
            token,
            conversationId:
              Number(active.id),
            currentUserId: user.id,
            receiverUserId:
              Number(receiver.id),
          });

        if (cancelled) {
          return;
        }

        setItems(previous =>
          previous.map(item =>
            item.id === active.id
              ? {
                  ...item,
                  messages,
                }
              : item,
          ),
        );
        setThreadStatus('ready');
      } catch (caught) {
        if (cancelled) {
          return;
        }

        setThreadError(caught);
        setThreadStatus('error');
      }
    }

    void loadThread();

    return () => {
      cancelled = true;
    };
  }, [
    activeId,
    status,
    user,
    threadReloadKey,
  ]);

  const failure =
    error !== null
      ? describeApiFailure(error)
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
        {status === 'loading' && (
          <section className="messaging-state-panel">
            <ApiState
              kind="loading"
              message="Loading conversations..."
            />
          </section>
        )}

        {status === 'error' && failure && (
          <section className="messaging-state-panel">
            <ApiState
              kind="error"
              title="Messages unavailable"
              message={failure.message}
              onRetry={
                failure.retryable
                  ? () =>
                    setReloadKey(
                      current =>
                        current + 1,
                    )
                  : undefined
              }
            />
          </section>
        )}

        {status === 'ready'
          && items.length === 0 && (
            <section className="messaging-state-panel">
              <ApiState
                kind="empty"
                title="No conversations yet"
                message="Your persisted conversations will appear here after a match starts chatting."
              />
            </section>
          )}

        {status === 'ready'
          && active && (
            <>
              <ConversationList
                conversations={items}
                activeId={active.id}
                onSelect={setActiveId}
              />

              {threadStatus === 'loading' && (
                <section className="chat-thread messaging-thread-state">
                  <ApiState
                    kind="loading"
                    compact
                    message="Loading messages..."
                  />
                </section>
              )}

              {threadStatus === 'error'
                && threadFailure && (
                  <section className="chat-thread messaging-thread-state">
                    <ApiState
                      kind="error"
                      compact
                      title="Thread unavailable"
                      message={
                        threadFailure.message
                      }
                      onRetry={
                        threadFailure.retryable
                          ? () =>
                            setThreadReloadKey(
                              current =>
                                current + 1,
                            )
                          : undefined
                      }
                    />
                  </section>
                )}

              {threadStatus === 'ready' && (
                <ChatThread
                  conversation={active}
                />
              )}

              <MatchDetailsPanel
                conversation={active}
              />
            </>
          )}
      </main>
    </div>
  );
}
