import { apiUrl } from './config';
import { ApiError, type ApiErrorPayload } from './errors';

type ApiRequestOptions = Omit<RequestInit, 'headers'> & {
  token?: string | null;
  headers?: HeadersInit;
};

function isApiErrorPayload(value: unknown): value is ApiErrorPayload {
  return (
    typeof value === 'object'
    && value !== null
    && 'success' in value
    && (value as { success?: unknown }).success === false
  );
}

async function parseResponse(response: Response): Promise<unknown> {
  const text = await response.text();

  if (!text) {
    return null;
  }

  try {
    return JSON.parse(text);
  } catch {
    return null;
  }
}

export async function apiRequest<T>(
  path: string,
  options: ApiRequestOptions = {},
): Promise<T> {
  const headers = new Headers(options.headers);

  headers.set('Accept', 'application/json');

  if (
    options.body !== undefined
    && !(options.body instanceof FormData)
    && !headers.has('Content-Type')
  ) {
    headers.set('Content-Type', 'application/json');
  }

  if (options.token) {
    headers.set('Authorization', `Bearer ${options.token}`);
  }

  const response = await fetch(apiUrl(path), {
    ...options,
    headers,
  });

  const payload = await parseResponse(response);

  if (!response.ok || isApiErrorPayload(payload)) {
    const errorPayload = isApiErrorPayload(payload)
      ? payload
      : undefined;

    throw new ApiError(
      errorPayload?.message ?? 'The request could not be completed.',
      response.status,
      errorPayload,
    );
  }

  return payload as T;
}