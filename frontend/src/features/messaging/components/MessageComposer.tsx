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
  disabled = false,
  error = null,
}: {
  onSend: (content: string) => Promise<void>;
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
          onChange={event =>
            setDraft(event.target.value)
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
