import {
  beforeEach,
  describe,
  expect,
  it,
  vi,
} from 'vitest';

import { apiRequest } from '../../api/client';
import { petInteractionRequest } from './interaction.api';

vi.mock('../../api/client', () => ({
  apiRequest: vi.fn(),
}));

const mockedApiRequest = vi.mocked(apiRequest);

describe('pet interaction API', () => {
  beforeEach(() => {
    mockedApiRequest.mockReset();
  });

  it('posts a like using only the target Pet ID', async () => {
    mockedApiRequest.mockResolvedValueOnce({
      success: true,
      message: 'Like processed.',
      data: {
        id: 15,
        from_pet_id: 8,
        to_pet_id: 42,
        interaction: 'liked',
      },
    });

    await expect(
      petInteractionRequest({
        token: 'test-token',
        targetPetId: 42,
        interaction: 'liked',
      }),
    ).resolves.toEqual({
      id: 15,
      from_pet_id: 8,
      to_pet_id: 42,
      interaction: 'liked',
    });

    expect(mockedApiRequest).toHaveBeenCalledWith(
      '/likes',
      {
        method: 'POST',
        token: 'test-token',
        body: JSON.stringify({
          to_pet_id: 42,
        }),
      },
    );
  });

  it('posts a dislike using the same Pet ID contract', async () => {
    mockedApiRequest.mockResolvedValueOnce({
      success: true,
      message: 'Dislike processed.',
      data: {
        id: 16,
        from_pet_id: 8,
        to_pet_id: 42,
        interaction: 'disliked',
      },
    });

    await petInteractionRequest({
      token: 'test-token',
      targetPetId: 42,
      interaction: 'disliked',
    });

    expect(mockedApiRequest).toHaveBeenCalledWith(
      '/dislikes',
      {
        method: 'POST',
        token: 'test-token',
        body: JSON.stringify({
          to_pet_id: 42,
        }),
      },
    );
  });

  it('rejects a response for another Pet ID', async () => {
    mockedApiRequest.mockResolvedValueOnce({
      success: true,
      message: 'Like processed.',
      data: {
        id: 15,
        from_pet_id: 8,
        to_pet_id: 999,
        interaction: 'liked',
      },
    });

    await expect(
      petInteractionRequest({
        token: 'test-token',
        targetPetId: 42,
        interaction: 'liked',
      }),
    ).rejects.toThrow(
      'Pet interaction response does not match the requested target.',
    );
  });

  it('rejects a response with the wrong persisted interaction', async () => {
    mockedApiRequest.mockResolvedValueOnce({
      success: true,
      message: 'Dislike processed.',
      data: {
        id: 16,
        from_pet_id: 8,
        to_pet_id: 42,
        interaction: 'disliked',
      },
    });

    await expect(
      petInteractionRequest({
        token: 'test-token',
        targetPetId: 42,
        interaction: 'liked',
      }),
    ).rejects.toThrow(
      'Pet interaction response does not match the requested target.',
    );
  });
});
