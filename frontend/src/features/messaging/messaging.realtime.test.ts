import {
  describe,
  expect,
  it,
} from 'vitest';

import {
  isRealtimeMatchEvent,
  realtimeMessageFrom,
  realtimeTypingFrom,
} from './messaging.realtime';

describe('messaging realtime adapters', () => {
  it('maps a private new-message payload to the existing chat model', () => {
    expect(
      realtimeMessageFrom({
        message: {
          id: 77,
          conversation_id: 101,
          sender_id: 20,
          receiver_id: 10,
          content: 'Realtime hello',
          is_seen: false,
          created_at:
            '2026-10-06T12:00:00.000Z',
        },
      }),
    ).toEqual({
      conversationId: '101',
      receiverUserId: '10',
      message: {
        id: '77',
        senderId: '20',
        content: 'Realtime hello',
        timestamp:
          '2026-10-06T12:00:00.000Z',
        receipt: undefined,
      },
    });
  });

  it('rejects incomplete or mixed-domain message payloads', () => {
    expect(
      realtimeMessageFrom({
        message: {
          id: 77,
          conversation_id: '101',
          sender_id: 20,
          receiver_id: 10,
          content: 'Bad identity',
          created_at:
            '2026-10-06T12:00:00.000Z',
        },
      }),
    ).toBeNull();
  });

  it('maps typing identity and state explicitly', () => {
    expect(
      realtimeTypingFrom({
        sender_user_id: 20,
        receiver_user_id: 10,
        is_writing: true,
      }),
    ).toEqual({
      senderUserId: '20',
      receiverUserId: '10',
      isWriting: true,
    });
  });

  it('recognizes only complete match event envelopes', () => {
    expect(
      isRealtimeMatchEvent({
        fromMatch: {
          id: 1,
        },
        toMatch: {
          id: 2,
        },
      }),
    ).toBe(true);

    expect(
      isRealtimeMatchEvent({
        fromMatch: {
          id: 1,
        },
      }),
    ).toBe(false);
  });
});
