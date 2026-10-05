import {
  beforeEach,
  describe,
  expect,
  it,
  vi,
} from 'vitest';

import { apiRequest } from '../../api/client';
import { mediaUrl } from '../../api/config';
import { relationshipsRequest } from './matches.api';

vi.mock('../../api/client', () => ({
  apiRequest: vi.fn(),
}));

const mockedApiRequest = vi.mocked(apiRequest);

describe('Matches API adapter', () => {
  beforeEach(() => {
    mockedApiRequest.mockReset();
  });

  it('loads active and past relationships using Pet IDs only', async () => {
    mockedApiRequest
      .mockResolvedValueOnce({
        success: true,
        message: 'List of matches.',
        data: [
          {
            id: 11,
            from_pet_id: 42,
            to_pet_id: 51,
          },
        ],
      })
      .mockResolvedValueOnce({
        success: true,
        message: 'List of mismatches.',
        data: [
          {
            id: 12,
            from_pet_id: 42,
            to_pet_id: 63,
          },
        ],
      })
      .mockResolvedValueOnce({
        success: true,
        message: 'Pet has been found.',
        data: {
          id: 51,
          name: 'Milo',
          age: 4,
          sexe: 1,
          images: ['pets/milo.jpg'],
          about: 'Friendly and curious.',
        },
      })
      .mockResolvedValueOnce({
        success: true,
        message: 'Pet has been found.',
        data: {
          id: 63,
          name: 'Luna',
          age: 3,
          sexe: 2,
          images: [],
          about: null,
        },
      });

    await expect(
      relationshipsRequest({
        token: 'test-token',
        currentPetId: 42,
      }),
    ).resolves.toEqual({
      matches: [
        {
          relationshipId: 11,
          kind: 'match',
          sourcePetId: 42,
          targetPetId: 51,
          name: 'Milo',
          ageYears: 4,
          sex: 1,
          photo: {
            src: mediaUrl('pets/milo.jpg'),
            alt: 'Milo pet photo',
            placeholder: 'Milo',
          },
          about: 'Friendly and curious.',
        },
      ],
      mismatches: [
        {
          relationshipId: 12,
          kind: 'mismatch',
          sourcePetId: 42,
          targetPetId: 63,
          name: 'Luna',
          ageYears: 3,
          sex: 2,
          photo: {
            src: null,
            alt: 'Luna pet profile',
            placeholder: 'Luna',
          },
          about: null,
        },
      ],
    });

    expect(mockedApiRequest).toHaveBeenCalledWith(
      '/matches',
      { token: 'test-token' },
    );
    expect(mockedApiRequest).toHaveBeenCalledWith(
      '/mismatches',
      { token: 'test-token' },
    );
    expect(mockedApiRequest).toHaveBeenCalledWith(
      '/pets/51',
      { token: 'test-token' },
    );
    expect(mockedApiRequest).toHaveBeenCalledWith(
      '/pets/63',
      { token: 'test-token' },
    );

    expect(
      mockedApiRequest.mock.calls.some(
        ([path]) =>
          String(path).includes('/users/'),
      ),
    ).toBe(false);
  });

  it('rejects a relationship whose source is not the authenticated pet', async () => {
    mockedApiRequest
      .mockResolvedValueOnce({
        success: true,
        message: 'List of matches.',
        data: [
          {
            id: 11,
            from_pet_id: 10,
            to_pet_id: 51,
          },
        ],
      })
      .mockResolvedValueOnce({
        success: true,
        message: 'List of mismatches.',
        data: [],
      });

    await expect(
      relationshipsRequest({
        token: 'test-token',
        currentPetId: 42,
      }),
    ).rejects.toThrow(
      'Relationship response does not preserve authenticated Pet ID semantics.',
    );
  });

  it('rejects target pet lookups that return a different Pet ID', async () => {
    mockedApiRequest
      .mockResolvedValueOnce({
        success: true,
        message: 'List of matches.',
        data: [
          {
            id: 11,
            from_pet_id: 42,
            to_pet_id: 51,
          },
        ],
      })
      .mockResolvedValueOnce({
        success: true,
        message: 'List of mismatches.',
        data: [],
      })
      .mockResolvedValueOnce({
        success: true,
        message: 'Pet has been found.',
        data: {
          id: 999,
          name: 'Wrong pet',
          age: 4,
          sexe: 1,
          images: [],
          about: null,
        },
      });

    await expect(
      relationshipsRequest({
        token: 'test-token',
        currentPetId: 42,
      }),
    ).rejects.toThrow(
      'Relationship pet lookup returned a different Pet ID.',
    );
  });
});
