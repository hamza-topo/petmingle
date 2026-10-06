import {
  type FormEvent,
  useState,
} from 'react';
import {
  Image,
  SendHorizontal,
  Smile,
} from 'lucide-react';

export function MessageComposer({
  onSend,
  onTypingChange,
  disabled = false,
  error = null,
}: {
  onSend: (content: string) => Promise<void>;
  onTypingChange?: (isWriting: boolean) => void;
  disabled?: boolean;
  error?: string | null;
}) {
  const [draft, setDraft] = useState('');
  const [pending, setPending] = useState(false);

  async function submit(
    event: FormEvent<HTMLFormElement>,
  ) {
    event.preventDefault();

    const content = draft.trim();

    if (!content || pending || disabled) {
      return;
    }

    setPending(true);
    onTypingChange?.(false);

    try {
      await onSend(content);
      setDraft('');
    } catch {
      // The caller exposes the API failure while this component
      // deliberately preserves the draft for retry.
    } finally {
      setPending(false);
    }
  }

  const controlsDisabled = disabled || pending;

  return (
    <>
      <form
        className="message-composer"
        aria-label="Send a message"
        onSubmit={submit}
      >
        <button
          type="button"
          className="chat-round-control"
          disabled
          aria-label="Attach image — unavailable"
        >
          <Image size={27} />
        </button>

        <button
          type="button"
          className="chat-round-control"
          disabled
          aria-label="Choose emoji — unavailable"
        >
          <Smile size={27} />
        </button>

        <input
          aria-label="Write a message"
          placeholder={
            pending
              ? 'Sending...'
              : 'Write a message...'
          }
          value={draft}
          maxLength={1000}
          disabled={controlsDisabled}
          onChange={event => {
            const value = event.target.value;

            setDraft(value);
            onTypingChange?.(
              value.trim().length > 0,
            );
          }}
          onBlur={() =>
            onTypingChange?.(false)
          }
        />

        <button
          type="submit"
          className="chat-send"
          disabled={
            controlsDisabled || !draft.trim()
          }
          aria-label={
            pending
              ? 'Sending message'
              : 'Send message'
          }
        >
          <SendHorizontal
            size={27}
            aria-hidden="true"
          />
        </button>
      </form>

      {error && (
        <p
          className="message-send-error"
          role="alert"
        >
          {error}
        </p>
      )}
    </>
  );
}
