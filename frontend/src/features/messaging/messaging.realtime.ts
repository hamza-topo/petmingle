import type { ChatMessage } from './messaging.types';

type RawRealtimeMessage = {
  id?: unknown;
  conversation_id?: unknown;
  sender_id?: unknown;
  receiver_id?: unknown;
  content?: unknown;
  is_seen?: unknown;
  created_at?: unknown;
};

type RawMessageEnvelope = {
  message?: RawRealtimeMessage;
};

type RawTypingPayload = {
  sender_user_id?: unknown;
  receiver_user_id?: unknown;
  is_writing?: unknown;
};

export type RealtimeMessage = {
  conversationId: string;
  receiverUserId: string;
  message: ChatMessage;
};

export type RealtimeTyping = {
  senderUserId: string;
  receiverUserId: string;
  isWriting: boolean;
};

function positiveInteger(
  value: unknown,
): value is number {
  return (
    typeof value === 'number'
    && Number.isInteger(value)
    && value > 0
  );
}

export function realtimeMessageFrom(
  data: unknown,
): RealtimeMessage | null {
  if (
    typeof data !== 'object'
    || data === null
  ) {
    return null;
  }

  const raw = (
    'message' in data
      ? (data as RawMessageEnvelope).message
      : data
  ) as RawRealtimeMessage | undefined;

  if (
    !raw
    || !positiveInteger(raw.id)
    || !positiveInteger(raw.conversation_id)
    || !positiveInteger(raw.sender_id)
    || !positiveInteger(raw.receiver_id)
    || typeof raw.content !== 'string'
    || typeof raw.created_at !== 'string'
  ) {
    return null;
  }

  return {
    conversationId: String(
      raw.conversation_id,
    ),
    receiverUserId: String(raw.receiver_id),
    message: {
      id: String(raw.id),
      senderId: String(raw.sender_id),
      content: raw.content,
      timestamp: raw.created_at,
      receipt:
        raw.is_seen === true || raw.is_seen === 1
          ? 'read'
          : undefined,
    },
  };
}

export function realtimeTypingFrom(
  data: unknown,
): RealtimeTyping | null {
  if (
    typeof data !== 'object'
    || data === null
  ) {
    return null;
  }

  const raw = data as RawTypingPayload;

  if (
    !positiveInteger(raw.sender_user_id)
    || !positiveInteger(raw.receiver_user_id)
    || typeof raw.is_writing !== 'boolean'
  ) {
    return null;
  }

  return {
    senderUserId: String(
      raw.sender_user_id,
    ),
    receiverUserId: String(
      raw.receiver_user_id,
    ),
    isWriting: raw.is_writing,
  };
}

export function isRealtimeMatchEvent(
  data: unknown,
): boolean {
  if (
    typeof data !== 'object'
    || data === null
  ) {
    return false;
  }

  const record = data as {
    fromMatch?: unknown;
    toMatch?: unknown;
  };

  return (
    typeof record.fromMatch === 'object'
    && record.fromMatch !== null
    && typeof record.toMatch === 'object'
    && record.toMatch !== null
  );
}
