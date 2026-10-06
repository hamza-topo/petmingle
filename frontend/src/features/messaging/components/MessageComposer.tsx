import {
  Image,
  SendHorizontal,
  Smile,
} from 'lucide-react';

export function MessageComposer() {
  return (
    <form
      className="message-composer"
      aria-label="Message composer unavailable"
      onSubmit={event => event.preventDefault()}
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
        placeholder="Sending will be enabled in the next integration step"
        disabled
      />

      <button
        type="submit"
        className="chat-send"
        disabled
        aria-label="Send message — unavailable"
      >
        <SendHorizontal
          size={27}
          aria-hidden="true"
        />
      </button>
    </form>
  );
}
