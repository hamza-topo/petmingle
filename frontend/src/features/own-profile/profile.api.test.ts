import {
  beforeEach,
  describe,
  expect,
  it,
  vi,
} from 'vitest';

import { apiRequest } from '../../api/client';
import { currentPetProfileRequest } from './profile.api';

vi.mock('../../api/client', () => ({
  apiRequest: vi.fn(),
}));

const mockedApiRequest = vi.mocked(apiRequest);

describe('current pet profile API', () => {
  beforeEach(() => {
    mockedApiRequest.mockReset();
  });

  it('loads the authenticated pet and resolves its race', async () => {
    mockedApiRequest
      .mockResolvedValueOnce({
        success: true,
        message: 'Pet has been found.',
        data: {
          id: 42,
          user_id: 10,
          species_id: 3,
          race_id: 7,
          name: 'Milo',
          age: 4,
          sexe: 1,
          color: 'brown',
          images: [],
          about: 'Friendly and curious.',
        },
      })
      .mockResolvedValueOnce({
        success: true,
        message: 'Race has been found.',
        data: {
          id: 7,
          species_id: 3,
          name: 'Labrador Retriever',
        },
      });

    await expect(
      currentPetProfileRequest({
        petId: 42,
        userId: 10,
        token: 'test-token',
      }),
    ).resolves.toEqual({
      id: 42,
      userId: 10,
      speciesId: 3,
      raceId: 7,
      name: 'Milo',
      ageYears: 4,
      breed: 'Labrador Retriever',
      biography: 'Friendly and curious.',
    });

    expect(mockedApiRequest).toHaveBeenNthCalledWith(
      1,
      '/pets/42',
      {
        method: 'GET',
        token: 'test-token',
      },
    );

    expect(mockedApiRequest).toHaveBeenNthCalledWith(
      2,
      '/races/7',
      {
        method: 'GET',
        token: 'test-token',
      },
    );
  });

  it('rejects a pet that does not belong to the authenticated user', async () => {
    mockedApiRequest.mockResolvedValueOnce({
      success: true,
      message: 'Pet has been found.',
      data: {
        id: 42,
        user_id: 999,
        species_id: 3,
        race_id: 7,
        name: 'Milo',
        age: 4,
        sexe: 1,
        color: 'brown',
        images: [],
        about: 'Friendly and curious.',
      },
    });

    await expect(
      currentPetProfileRequest({
        petId: 42,
        userId: 10,
        token: 'test-token',
      }),
    ).rejects.toThrow(
      'Current pet identity does not match the authenticated account.',
    );

    expect(mockedApiRequest).toHaveBeenCalledTimes(1);
  });

  it('uses a safe biography fallback when the backend biography is empty', async () => {
    mockedApiRequest
      .mockResolvedValueOnce({
        success: true,
        message: 'Pet has been found.',
        data: {
          id: 42,
          user_id: 10,
          species_id: 3,
          race_id: 7,
          name: 'Milo',
          age: 4,
          sexe: 1,
          color: null,
          images: [],
          about: null,
        },
      })
      .mockResolvedValueOnce({
        success: true,
        message: 'Race has been found.',
        data: {
          id: 7,
          species_id: 3,
          name: 'Labrador Retriever',
        },
      });

    const profile = await currentPetProfileRequest({
      petId: 42,
      userId: 10,
      token: 'test-token',
    });

    expect(profile.biography).toBe('No biography yet.');
  });
});
