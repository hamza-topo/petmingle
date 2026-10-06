import { apiOrigin } from '../api/config';

export type PrivateRealtimeEvent = {
  name: 'new.message' | 'new.match' | 'is-writing-to';
  data: unknown;
};

export type RealtimeConnectionState =
  | 'disabled'
  | 'connecting'
  | 'connected'
  | 'reconnecting'
  | 'unauthorized';

type RealtimeCallbacks = {
  onEvent: (event: PrivateRealtimeEvent) => void;
  onStateChange?: (
    state: RealtimeConnectionState,
  ) => void;
  onReconnect?: () => void;
};

export type SocketLike = {
  onmessage:
    | ((event: { data: string }) => void)
    | null;
  onclose: (() => void) | null;
  onerror: (() => void) | null;
  send: (data: string) => void;
  close: () => void;
};

export type RealtimeDependencies = {
  createSocket: (url: string) => SocketLike;
  fetchFn: typeof fetch;
  setTimer: (
    callback: () => void,
    delay: number,
  ) => ReturnType<typeof setTimeout>;
  clearTimer: (
    timer: ReturnType<typeof setTimeout>,
  ) => void;
};

export type RealtimeSettings = {
  enabled: boolean;
  appKey: string;
  cluster: string;
  wsHost: string | null;
  wsPort: number | null;
  wsScheme: 'ws' | 'wss';
  authUrl: string;
};

type PusherEnvelope = {
  event?: unknown;
  data?: unknown;
};

function parseData(value: unknown): unknown {
  if (typeof value !== 'string') {
    return value;
  }

  try {
    return JSON.parse(value);
  } catch {
    return value;
  }
}

function settingsFromEnvironment(): RealtimeSettings | null {
  const appKey =
    import.meta.env.VITE_PUSHER_APP_KEY?.trim() ?? '';

  const enabled =
    import.meta.env.VITE_REALTIME_ENABLED?.trim()
      ?.toLowerCase() !== 'false';

  if (!enabled || !appKey) {
    return null;
  }

  const cluster =
    import.meta.env.VITE_PUSHER_APP_CLUSTER?.trim()
    || 'mt1';

  const rawHost =
    import.meta.env.VITE_PUSHER_WS_HOST?.trim();

  const rawPort =
    import.meta.env.VITE_PUSHER_WS_PORT?.trim();

  const scheme =
    import.meta.env.VITE_PUSHER_WS_SCHEME?.trim()
      === 'ws'
      ? 'ws'
      : 'wss';

  return {
    enabled: true,
    appKey,
    cluster,
    wsHost: rawHost || null,
    wsPort: rawPort ? Number(rawPort) : null,
    wsScheme: scheme,
    authUrl: `${apiOrigin()}/broadcasting/auth`,
  };
}

function socketUrl(
  settings: RealtimeSettings,
): string {
  const host = settings.wsHost
    ?? `ws-${settings.cluster}.pusher.com`;

  const defaultPort =
    settings.wsScheme === 'wss' ? 443 : 80;

  const port =
    settings.wsPort
    && settings.wsPort !== defaultPort
      ? `:${settings.wsPort}`
      : '';

  const query = new URLSearchParams({
    protocol: '7',
    client: 'petmingle-web',
    version: '1.0',
    flash: 'false',
  });

  return (
    `${settings.wsScheme}://${host}${port}`
    + `/app/${encodeURIComponent(settings.appKey)}?`
    + query.toString()
  );
}

export class PrivateRealtimeClient {
  private socket: SocketLike | null = null;
  private reconnectTimer:
    | ReturnType<typeof setTimeout>
    | null = null;
  private stopped = true;
  private reconnectAttempt = 0;
  private hasSubscribed = false;

  constructor(
    private token: string,
    private userId: number,
    private callbacks: RealtimeCallbacks,
    private settings:
      | RealtimeSettings
      | null = settingsFromEnvironment(),
    private dependencies: RealtimeDependencies = {
      createSocket: url => new WebSocket(url),
      fetchFn: fetch.bind(globalThis),
      setTimer: (callback, delay) =>
        setTimeout(callback, delay),
      clearTimer: timer => clearTimeout(timer),
    },
  ) {}

  connect(): void {
    if (!this.settings) {
      this.callbacks.onStateChange?.('disabled');
      return;
    }

    if (!Number.isInteger(this.userId) || this.userId <= 0) {
      this.callbacks.onStateChange?.('unauthorized');
      return;
    }

    this.stopped = false;
    this.openSocket(false);
  }

  disconnect(): void {
    this.stopped = true;

    if (this.reconnectTimer) {
      this.dependencies.clearTimer(
        this.reconnectTimer,
      );
      this.reconnectTimer = null;
    }

    const socket = this.socket;
    this.socket = null;
    socket?.close();
  }

  private openSocket(reconnecting: boolean): void {
    if (this.stopped || !this.settings) {
      return;
    }

    this.callbacks.onStateChange?.(
      reconnecting
        ? 'reconnecting'
        : 'connecting',
    );

    const socket =
      this.dependencies.createSocket(
        socketUrl(this.settings),
      );

    this.socket = socket;

    socket.onmessage = event => {
      void this.handleMessage(
        socket,
        event.data,
      );
    };

    socket.onerror = () => {
      // onclose owns retry scheduling. HTTP remains the fallback.
    };

    socket.onclose = () => {
      if (this.socket === socket) {
        this.socket = null;
      }

      if (!this.stopped) {
        this.scheduleReconnect();
      }
    };
  }

  private async handleMessage(
    socket: SocketLike,
    raw: string,
  ): Promise<void> {
    let envelope: PusherEnvelope;

    try {
      envelope = JSON.parse(raw) as PusherEnvelope;
    } catch {
      return;
    }

    if (typeof envelope.event !== 'string') {
      return;
    }

    if (envelope.event === 'pusher:ping') {
      socket.send(
        JSON.stringify({
          event: 'pusher:pong',
          data: {},
        }),
      );
      return;
    }

    if (
      envelope.event
      === 'pusher:connection_established'
    ) {
      const connection = parseData(
        envelope.data,
      );

      if (
        typeof connection !== 'object'
        || connection === null
        || !('socket_id' in connection)
        || typeof (
          connection as {
            socket_id?: unknown;
          }
        ).socket_id !== 'string'
      ) {
        socket.close();
        return;
      }

      await this.authorizeAndSubscribe(
        socket,
        (
          connection as {
            socket_id: string;
          }
        ).socket_id,
      );

      return;
    }

    if (
      envelope.event
      === 'pusher_internal:subscription_succeeded'
    ) {
      const reconnect = this.hasSubscribed;

      this.hasSubscribed = true;
      this.reconnectAttempt = 0;
      this.callbacks.onStateChange?.('connected');

      if (reconnect) {
        this.callbacks.onReconnect?.();
      }

      return;
    }

    if (
      envelope.event === 'new.message'
      || envelope.event === 'new.match'
      || envelope.event === 'is-writing-to'
    ) {
      this.callbacks.onEvent({
        name: envelope.event,
        data: parseData(envelope.data),
      });
    }
  }

  private async authorizeAndSubscribe(
    socket: SocketLike,
    socketId: string,
  ): Promise<void> {
    if (!this.settings) {
      return;
    }

    const channel =
      `private-App.Models.User.${this.userId}`;

    try {
      const response =
        await this.dependencies.fetchFn(
          this.settings.authUrl,
          {
            method: 'POST',
            headers: {
              Accept: 'application/json',
              Authorization:
                `Bearer ${this.token}`,
              'Content-Type':
                'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
              socket_id: socketId,
              channel_name: channel,
            }),
          },
        );

      if (response.status === 401 || response.status === 403) {
        this.callbacks.onStateChange?.(
          'unauthorized',
        );
        this.stopped = true;
        socket.close();
        return;
      }

      if (!response.ok) {
        socket.close();
        return;
      }

      const payload =
        await response.json() as {
          auth?: unknown;
        };

      if (typeof payload.auth !== 'string') {
        socket.close();
        return;
      }

      socket.send(
        JSON.stringify({
          event: 'pusher:subscribe',
          data: {
            auth: payload.auth,
            channel,
          },
        }),
      );
    } catch {
      socket.close();
    }
  }

  private scheduleReconnect(): void {
    if (
      this.stopped
      || this.reconnectTimer
    ) {
      return;
    }

    this.reconnectAttempt += 1;

    const delay = Math.min(
      1000 * 2 ** (this.reconnectAttempt - 1),
      10000,
    );

    this.callbacks.onStateChange?.(
      'reconnecting',
    );

    this.reconnectTimer =
      this.dependencies.setTimer(() => {
        this.reconnectTimer = null;
        this.openSocket(true);
      }, delay);
  }
}

export function createPrivateRealtimeClient({
  token,
  userId,
  callbacks,
}: {
  token: string;
  userId: number;
  callbacks: RealtimeCallbacks;
}): PrivateRealtimeClient {
  return new PrivateRealtimeClient(
    token,
    userId,
    callbacks,
  );
}
