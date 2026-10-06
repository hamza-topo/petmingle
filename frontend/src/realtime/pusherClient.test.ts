import {
  describe,
  expect,
  it,
  vi,
} from 'vitest';

import {
  PrivateRealtimeClient,
  type RealtimeDependencies,
  type RealtimeSettings,
  type SocketLike,
} from './pusherClient';

class FakeSocket implements SocketLike {
  onmessage:
    | ((event: { data: string }) => void)
    | null = null;
  onclose: (() => void) | null = null;
  onerror: (() => void) | null = null;
  sent: string[] = [];
  closed = false;

  send(data: string) {
    this.sent.push(data);
  }

  close() {
    this.closed = true;
    this.onclose?.();
  }

  receive(event: string, data: unknown) {
    this.onmessage?.({
      data: JSON.stringify({
        event,
        data:
          typeof data === 'string'
            ? data
            : JSON.stringify(data),
      }),
    });
  }
}

const settings: RealtimeSettings = {
  enabled: true,
  appKey: 'petmingle-key',
  cluster: 'mt1',
  wsHost: 'localhost',
  wsPort: 6001,
  wsScheme: 'ws',
  authUrl:
    'http://localhost:8000/broadcasting/auth',
};

function response(
  status: number,
  payload: unknown,
): Response {
  return {
    ok: status >= 200 && status < 300,
    status,
    json: async () => payload,
  } as Response;
}

describe('PrivateRealtimeClient', () => {
  it('authorizes and subscribes only to the authenticated private user channel', async () => {
    const socket = new FakeSocket();
    const onEvent = vi.fn();
    const onStateChange = vi.fn();
    const fetchFn = vi.fn(
      async () =>
        response(200, {
          auth: 'petmingle-key:signature',
        }),
    );

    const dependencies: RealtimeDependencies = {
      createSocket: vi.fn(() => socket),
      fetchFn:
        fetchFn as unknown as typeof fetch,
      setTimer: setTimeout,
      clearTimer: clearTimeout,
    };

    const client = new PrivateRealtimeClient(
      'secret-token',
      10,
      {
        onEvent,
        onStateChange,
      },
      settings,
      dependencies,
    );

    client.connect();

    expect(
      dependencies.createSocket,
    ).toHaveBeenCalledWith(
      expect.stringContaining(
        'ws://localhost:6001/app/petmingle-key',
      ),
    );

    socket.receive(
      'pusher:connection_established',
      {
        socket_id: '123.456',
      },
    );

    await vi.waitFor(() =>
      expect(fetchFn).toHaveBeenCalledTimes(1),
    );

    const [
      authUrl,
      authOptions,
    ] = fetchFn.mock.calls[0];

    expect(authUrl).toBe(
      'http://localhost:8000/broadcasting/auth',
    );
    expect(
      new Headers(authOptions?.headers)
        .get('Authorization'),
    ).toBe('Bearer secret-token');

    const body =
      authOptions?.body as URLSearchParams;

    expect(body.get('socket_id')).toBe(
      '123.456',
    );
    expect(body.get('channel_name')).toBe(
      'private-App.Models.User.10',
    );

    await vi.waitFor(() =>
      expect(socket.sent).toHaveLength(1),
    );

    expect(
      JSON.parse(socket.sent[0]),
    ).toEqual({
      event: 'pusher:subscribe',
      data: {
        auth: 'petmingle-key:signature',
        channel:
          'private-App.Models.User.10',
      },
    });

    socket.receive(
      'pusher_internal:subscription_succeeded',
      {},
    );

    socket.receive('new.message', {
      message: {
        id: 77,
      },
    });

    expect(onEvent).toHaveBeenCalledWith({
      name: 'new.message',
      data: {
        message: {
          id: 77,
        },
      },
    });

    expect(onStateChange).toHaveBeenCalledWith(
      'connected',
    );

    client.disconnect();
  });

  it('fails closed on unauthorized private subscription and does not reconnect', async () => {
    const socket = new FakeSocket();
    const onStateChange = vi.fn();
    const setTimer = vi.fn();

    const client = new PrivateRealtimeClient(
      'bad-token',
      10,
      {
        onEvent: vi.fn(),
        onStateChange,
      },
      settings,
      {
        createSocket: () => socket,
        fetchFn: vi.fn(
          async () =>
            response(403, {
              message: 'Forbidden',
            }),
        ) as unknown as typeof fetch,
        setTimer:
          setTimer as unknown as RealtimeDependencies['setTimer'],
        clearTimer: vi.fn(),
      },
    );

    client.connect();

    socket.receive(
      'pusher:connection_established',
      {
        socket_id: '123.456',
      },
    );

    await vi.waitFor(() =>
      expect(onStateChange).toHaveBeenCalledWith(
        'unauthorized',
      ),
    );

    expect(socket.closed).toBe(true);
    expect(setTimer).not.toHaveBeenCalled();
  });

  it('reconnects with backoff and signals authoritative resynchronization after resubscribe', async () => {
    const sockets = [
      new FakeSocket(),
      new FakeSocket(),
    ];
    let socketIndex = 0;
    let reconnectCallback:
      | (() => void)
      | null = null;

    const onReconnect = vi.fn();

    const client = new PrivateRealtimeClient(
      'token',
      10,
      {
        onEvent: vi.fn(),
        onReconnect,
      },
      settings,
      {
        createSocket: () =>
          sockets[socketIndex++],
        fetchFn: vi.fn(
          async () =>
            response(200, {
              auth: 'key:signature',
            }),
        ) as unknown as typeof fetch,
        setTimer: callback => {
          reconnectCallback = callback;
          return 1 as unknown as ReturnType<
            typeof setTimeout
          >;
        },
        clearTimer: vi.fn(),
      },
    );

    client.connect();

    sockets[0].receive(
      'pusher:connection_established',
      {
        socket_id: '1.1',
      },
    );

    await vi.waitFor(() =>
      expect(sockets[0].sent).toHaveLength(1),
    );

    sockets[0].receive(
      'pusher_internal:subscription_succeeded',
      {},
    );

    sockets[0].close();

    expect(reconnectCallback).not.toBeNull();

    reconnectCallback?.();

    sockets[1].receive(
      'pusher:connection_established',
      {
        socket_id: '2.2',
      },
    );

    await vi.waitFor(() =>
      expect(sockets[1].sent).toHaveLength(1),
    );

    sockets[1].receive(
      'pusher_internal:subscription_succeeded',
      {},
    );

    expect(onReconnect).toHaveBeenCalledTimes(1);

    client.disconnect();
  });

  it('answers pusher ping without exposing application state', () => {
    const socket = new FakeSocket();

    const client = new PrivateRealtimeClient(
      'token',
      10,
      {
        onEvent: vi.fn(),
      },
      settings,
      {
        createSocket: () => socket,
        fetchFn: vi.fn() as unknown as typeof fetch,
        setTimer: setTimeout,
        clearTimer: clearTimeout,
      },
    );

    client.connect();

    socket.receive(
      'pusher:ping',
      {},
    );

    expect(
      JSON.parse(socket.sent[0]),
    ).toEqual({
      event: 'pusher:pong',
      data: {},
    });

    client.disconnect();
  });
});
