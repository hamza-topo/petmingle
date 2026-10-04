import { ApiError } from './errors';

export type ApiFailurePresentation = {
  message: string;
  retryable: boolean;
};

export function describeApiFailure(
  error: unknown,
): ApiFailurePresentation {
  if (error instanceof ApiError) {
    if (error.status === 401) {
      return {
        message:
          'Your session is no longer valid. Sign in again to continue.',
        retryable: false,
      };
    }

    if (error.status === 403) {
      return {
        message:
          'You do not have permission to access this information.',
        retryable: false,
      };
    }

    if (error.status === 404) {
      return {
        message:
          'The requested information is no longer available.',
        retryable: false,
      };
    }

    if (error.status === 422) {
      return {
        message:
          'PetMingle could not process this request.',
        retryable: false,
      };
    }

    if (error.status === 429) {
      return {
        message:
          'Too many requests. Please wait a moment and try again.',
        retryable: true,
      };
    }

    if (error.status >= 500) {
      return {
        message:
          'PetMingle is temporarily unavailable. Please try again.',
        retryable: true,
      };
    }
  }

  if (error instanceof TypeError) {
    return {
      message:
        'Unable to reach PetMingle. Check your connection and try again.',
      retryable: true,
    };
  }

  return {
    message:
      'Unable to load this information right now. Please try again.',
    retryable: true,
  };
}
