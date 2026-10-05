import {
  beforeEach,
  describe,
  expect,
  it,
  vi,
} from 'vitest';

import { apiRequest } from '../../api/client';
import {
  currentPetProfileRequest,
  removePetImageRequest,
  replacePetImageRequest,
  updatePetProfileRequest,
} from './profile.api';

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
      images: [],
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

  it('submits supported profile fields through the update contract', async () => {
    mockedApiRequest.mockResolvedValueOnce({
      success: true,
      message: 'Pet has been updated successfully.',
      data: {
        id: 42,
        user_id: 10,
        species_id: 3,
        race_id: 7,
        name: 'Milo',
        age: 5,
        sexe: 1,
        color: 'brown',
        images: [],
        about: 'Updated biography',
      },
    });

    await expect(
      updatePetProfileRequest({
        petId: 42,
        token: 'test-token',
        input: {
          speciesId: 3,
          raceId: 7,
          name: '  Milo  ',
          ageYears: 5,
          biography: ' Updated biography ',
        },
      }),
    ).resolves.toEqual(
      expect.objectContaining({
        id: 42,
        name: 'Milo',
        age: 5,
        about: 'Updated biography',
      }),
    );

    expect(mockedApiRequest).toHaveBeenCalledWith(
      '/pets/42',
      {
        method: 'PUT',
        token: 'test-token',
        body: JSON.stringify({
          species_id: 3,
          race_id: 7,
          name: 'Milo',
          age: 5,
          about: 'Updated biography',
        }),
      },
    );
  });

  it('replaces pet media through multipart method spoofing', async () => {
    mockedApiRequest.mockResolvedValueOnce({
      success: true,
      message: 'Pet has been updated successfully.',
      data: {
        id: 42,
        user_id: 10,
        species_id: 3,
        race_id: 7,
        name: 'Milo',
        age: 4,
        sexe: 1,
        color: 'brown',
        images: ['pets/new-photo.png'],
        about: 'Friendly and curious.',
      },
    });

    const image = new File(
      ['photo'],
      'new-photo.png',
      {
        type: 'image/png',
      },
    );

    await replacePetImageRequest({
      petId: 42,
      token: 'test-token',
      image,
    });

    expect(mockedApiRequest).toHaveBeenCalledTimes(1);

    const [, request] = mockedApiRequest.mock.calls[0] as [
      string,
      RequestInit,
    ];

    expect(mockedApiRequest.mock.calls[0]?.[0]).toBe(
      '/pets/42',
    );
    expect(request.method).toBe('POST');

    const body = request.body as FormData;

    expect(body.get('_method')).toBe('PUT');
    expect(body.get('image')).toBe(image);
  });

  it('removes persisted pet media through the update contract', async () => {
    mockedApiRequest.mockResolvedValueOnce({
      success: true,
      message: 'Pet has been updated successfully.',
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
    });

    await removePetImageRequest({
      petId: 42,
      token: 'test-token',
    });

    expect(mockedApiRequest).toHaveBeenCalledWith(
      '/pets/42',
      {
        method: 'PUT',
        token: 'test-token',
        body: JSON.stringify({
          remove_image: true,
        }),
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
