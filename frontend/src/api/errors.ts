export type ApiErrorPayload = {
  success?: false;
  message?: string;
  data?: Record<string, string[]>;
  errors?: Record<string, string[]>;
};

export class ApiError extends Error {
  readonly status: number;
  readonly payload?: ApiErrorPayload;

  constructor(
    message: string,
    status: number,
    payload?: ApiErrorPayload,
  ) {
    super(message);

    this.name = 'ApiError';
    this.status = status;
    this.payload = payload;
  }
}