import { describe, expect, it } from 'vitest';

import { ApiError } from './errors';
import { describeApiFailure } from './presentation';

describe('describeApiFailure', () => {
  it.each([
    [
      401,
      'Your session is no longer valid. Sign in again to continue.',
      false,
    ],
    [
      403,
      'You do not have permission to access this information.',
      false,
    ],
    [
      404,
      'The requested information is no longer available.',
      false,
    ],
    [
      422,
      'PetMingle could not process this request.',
      false,
    ],
    [
      429,
      'Too many requests. Please wait a moment and try again.',
      true,
    ],
    [
      503,
      'PetMingle is temporarily unavailable. Please try again.',
      true,
    ],
  ])(
    'maps HTTP %i without exposing backend details',
    (status, message, retryable) => {
      const presentation = describeApiFailure(
        new ApiError(
          'Sensitive backend exception details',
          status,
        ),
      );

      expect(presentation).toEqual({
        message,
        retryable,
      });

      expect(presentation.message).not.toContain(
        'Sensitive backend exception details',
      );
    },
  );

  it('maps network failures to a retryable safe message', () => {
    expect(
      describeApiFailure(
        new TypeError('Failed to fetch internal URL'),
      ),
    ).toEqual({
      message:
        'Unable to reach PetMingle. Check your connection and try again.',
      retryable: true,
    });
  });

  it('does not expose unknown exception messages', () => {
    const presentation = describeApiFailure(
      new Error('Database hostname leaked here'),
    );

    expect(presentation).toEqual({
      message:
        'Unable to load this information right now. Please try again.',
      retryable: true,
    });

    expect(presentation.message).not.toContain(
      'Database hostname',
    );
  });
});
