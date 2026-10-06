import type {
  Conversation,
} from './messaging.types';

export function pairName(
  conversation: Conversation,
): string {
  return conversation.pets
    .map(pet => pet.name)
    .join(' & ');
}

export function messageTime(
  timestamp: string,
): string {
  return new Date(timestamp).toLocaleTimeString(
    'en-US',
    {
      hour: 'numeric',
      minute: '2-digit',
    },
  );
}

export function messageDay(
  timestamp: string,
): string {
  const messageDate = new Date(timestamp);
  const today = new Date();

  const todayKey = today.toISOString().slice(0, 10);
  const messageKey = messageDate
    .toISOString()
    .slice(0, 10);

  if (messageKey === todayKey) {
    return 'Today';
  }

  const yesterday = new Date(today);
  yesterday.setDate(today.getDate() - 1);

  if (
    messageKey
    === yesterday.toISOString().slice(0, 10)
  ) {
    return 'Yesterday';
  }

  return messageDate.toLocaleDateString(
    'en-US',
    {
      month: 'short',
      day: 'numeric',
      year: 'numeric',
    },
  );
}

export function conversationActivityLabel(
  timestamp: string | null,
): string {
  if (!timestamp) {
    return '';
  }

  const date = new Date(timestamp);
  const now = new Date();

  if (
    date.toISOString().slice(0, 10)
    === now.toISOString().slice(0, 10)
  ) {
    return messageTime(timestamp);
  }

  const yesterday = new Date(now);
  yesterday.setDate(now.getDate() - 1);

  if (
    date.toISOString().slice(0, 10)
    === yesterday.toISOString().slice(0, 10)
  ) {
    return 'Yesterday';
  }

  return date.toLocaleDateString(
    'en-US',
    {
      month: 'short',
      day: 'numeric',
    },
  );
}
