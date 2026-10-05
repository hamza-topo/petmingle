import {
  beforeEach,
  describe,
  expect,
  it,
  vi,
} from 'vitest';

import { apiRequest } from '../../api/client';
import {
  discoveryRequest,
  mapDiscoveryItem,
} from './discovery.api';

vi.mock('../../api/client', () => ({
  apiRequest: vi.fn(),
}));

const mockedApiRequest = vi.mocked(apiRequest);

const apiItem = {
  owner: {
    id: 7,
    name: 'Sarah',
  },
  pet: {
    id: 42,
    owner_id: 7,
    species_id: 3,
    name: 'Milo',
    age_years: 4,
    sex: 1,
    race: {
      id: 9,
      species_id: 3,
      name: 'Labrador Retriever',
    },
    images: [
      'pets/milo.jpg',
      '',
    ],
    about: 'Friendly and curious.',
  },
  distance_km: 1.24,
  is_new: true,
};

describe('Discovery API adapter', () => {
  beforeEach(() => {
    mockedApiRequest.mockReset();
  });

  it('preserves stable IDs and maps supported card data only', async () => {
    mockedApiRequest.mockResolvedValueOnce({
      success: true,
      message: 'Nearby pets.',
      data: [apiItem],
      meta: {
        current_page: 1,
        last_page: 1,
        per_page: 24,
        total: 1,
      },
      links: {
        first: null,
        last: null,
        prev: null,
        next: null,
      },
    });

    await expect(
      discoveryRequest({
        token: 'test-token',
      }),
    ).resolves.toEqual({
      pets: [
        {
          id: 42,
          ownerId: 7,
          speciesId: 3,
          raceId: 9,
          ownerName: 'Sarah',
          name: 'Milo',
          breed: 'Labrador Retriever',
          ageYears: 4,
          sex: 1,
          images: ['pets/milo.jpg'],
          photo: {
            src: '/storage/pets/milo.jpg',
            alt: 'Milo pet photo',
            placeholder: 'Milo',
          },
          photoCount: 1,
          about: 'Friendly and curious.',
          distanceKm: 1.24,
          isNew: true,
        },
      ],
      meta: {
        current_page: 1,
        last_page: 1,
        per_page: 24,
        total: 1,
      },
    });

    expect(mockedApiRequest).toHaveBeenCalledWith(
      '/locations/nears',
      {
        method: 'POST',
        token: 'test-token',
        body: JSON.stringify({
          radius_km: 5,
          page: 1,
          per_page: 24,
        }),
      },
    );
  });

  it('passes explicit pagination to the normalized API contract', async () => {
    mockedApiRequest.mockResolvedValueOnce({
      success: true,
      message: 'Nearby pets.',
      data: [],
      meta: {
        current_page: 2,
        last_page: 3,
        per_page: 12,
        total: 25,
      },
      links: {
        first: null,
        last: null,
        prev: null,
        next: null,
      },
    });

    await discoveryRequest({
      token: 'test-token',
      radiusKm: 10,
      page: 2,
      perPage: 12,
    });

    expect(mockedApiRequest).toHaveBeenCalledWith(
      '/locations/nears',
      {
        method: 'POST',
        token: 'test-token',
        body: JSON.stringify({
          radius_km: 10,
          page: 2,
          per_page: 12,
        }),
      },
    );
  });

  it('uses an explicit placeholder when a persisted pet has no image', () => {
    expect(
      mapDiscoveryItem({
        ...apiItem,
        pet: {
          ...apiItem.pet,
          images: [],
        },
      }).photo,
    ).toEqual({
      src: null,
      alt: 'Milo pet profile',
      placeholder: 'Milo',
    });
  });

  it('rejects inconsistent owner and taxonomy identifiers', () => {
    expect(() =>
      mapDiscoveryItem({
        ...apiItem,
        pet: {
          ...apiItem.pet,
          owner_id: 999,
        },
      }),
    ).toThrow(
      'Discovery response contains inconsistent identifiers or card data.',
    );

    expect(() =>
      mapDiscoveryItem({
        ...apiItem,
        pet: {
          ...apiItem.pet,
          race: {
            ...apiItem.pet.race,
            species_id: 999,
          },
        },
      }),
    ).toThrow(
      'Discovery response contains inconsistent identifiers or card data.',
    );
  });

  it('does not manufacture unsupported UI metadata', () => {
    const mapped = mapDiscoveryItem(apiItem);

    expect(mapped).not.toHaveProperty('verified');
    expect(mapped).not.toHaveProperty('traits');
    expect(mapped).not.toHaveProperty('companion');
    expect(mapped).not.toHaveProperty('location');
  });
});
