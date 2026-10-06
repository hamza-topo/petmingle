import {
  render,
  screen,
  waitFor,
  within,
} from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { MemoryRouter } from 'react-router';
import {
  afterEach,
  beforeEach,
  describe,
  expect,
  it,
  vi,
} from 'vitest';

import { App } from '../../app/App';
import { useAuth } from '../../auth/AuthProvider';
import { tokenStorage } from '../../auth/tokenStorage';
import { authenticatedAuthState } from '../../test/authFixtures';
import {
  accountLocationsRequest,
  createAccountLocationRequest,
  updateAccountLocationRequest,
} from '../account-location/location.api';
import { taxonomyRequest } from '../profile-creation/taxonomy.api';
import {
  discoveryRequest,
  type DiscoveryPet,
  type DiscoveryResult,
} from './discovery.api';
import { petInteractionRequest } from './interaction.api';

vi.mock('../../auth/AuthProvider', () => ({
  useAuth: vi.fn(),
}));

vi.mock('../account-location/location.api', async importOriginal => {
  const actual = await importOriginal<typeof import('../account-location/location.api')>();
  return {
    ...actual,
  accountLocationsRequest: vi.fn(),
  createAccountLocationRequest: vi.fn(),
  updateAccountLocationRequest: vi.fn(),
  };
});

vi.mock('../profile-creation/taxonomy.api', () => ({
  taxonomyRequest: vi.fn(),
}));

vi.mock('./interaction.api', () => ({
  petInteractionRequest: vi.fn(),
}));

vi.mock('./discovery.api', async importOriginal => {
  const actual =
    await importOriginal<typeof import('./discovery.api')>();

  return {
    ...actual,
    discoveryRequest: vi.fn(),
  };
});

const mockedUseAuth = vi.mocked(useAuth);
const mockedAccountLocationsRequest =
  vi.mocked(accountLocationsRequest);
const mockedCreateAccountLocationRequest =
  vi.mocked(createAccountLocationRequest);
const mockedUpdateAccountLocationRequest =
  vi.mocked(updateAccountLocationRequest);
const mockedTaxonomyRequest =
  vi.mocked(taxonomyRequest);
const mockedDiscoveryRequest =
  vi.mocked(discoveryRequest);
const mockedPetInteractionRequest =
  vi.mocked(petInteractionRequest);

function pet(
  id: number,
  name: string,
  distanceKm: number,
): DiscoveryPet {
  return {
    id,
    ownerId: id + 100,
    speciesId: 3,
    raceId: 7,
    ownerName: `Owner ${name}`,
    name,
    breed: 'Labrador Retriever',
    ageYears: 4,
    sex: 1,
    images: [`pets/${name.toLowerCase()}.jpg`],
    photo: {
      src: `/storage/pets/${name.toLowerCase()}.jpg`,
      alt: `${name} pet photo`,
      placeholder: name,
    },
    photoCount: 1,
    about: `${name} persisted biography.`,
    distanceKm,
    isNew: id === 42,
    interaction: null,
  };
}

const firstPage: DiscoveryResult = {
  pets: [
    pet(42, 'Milo', 0.8),
    pet(43, 'Luna', 1.4),
    pet(44, 'Bella', 2.2),
  ],
  meta: {
    current_page: 1,
    last_page: 1,
    per_page: 24,
    total: 3,
  },
};

const taxonomy = {
  species: [
    {
      id: 3,
      name: 'Dog',
      description: 'Dogs',
    },
    {
      id: 4,
      name: 'Cat',
      description: 'Cats',
    },
  ],
  races: [
    {
      id: 7,
      species_id: 3,
      name: 'Labrador Retriever',
    },
    {
      id: 8,
      species_id: 3,
      name: 'Golden Retriever',
    },
    {
      id: 9,
      species_id: 4,
      name: 'Domestic Shorthair',
    },
  ],
};

beforeEach(() => {
  mockedUseAuth.mockReturnValue(
    authenticatedAuthState(),
  );

  tokenStorage.set('test-token');

  mockedAccountLocationsRequest.mockReset();
  mockedCreateAccountLocationRequest.mockReset();
  mockedUpdateAccountLocationRequest.mockReset();
  mockedTaxonomyRequest.mockReset();
  mockedDiscoveryRequest.mockReset();
  mockedPetInteractionRequest.mockReset();

  mockedAccountLocationsRequest.mockResolvedValue([
    {
      id: 5,
      user_id: 10,
      latitude: 31.6295,
      longitude: -7.9811,
    },
  ]);

  mockedCreateAccountLocationRequest.mockResolvedValue({
    id: 6,
    user_id: 10,
    latitude: 31.6295,
    longitude: -7.9811,
  });

  mockedUpdateAccountLocationRequest.mockResolvedValue({
    id: 5,
    user_id: 10,
    latitude: 30.4278,
    longitude: -9.5981,
  });

  mockedTaxonomyRequest.mockResolvedValue(taxonomy);
  mockedDiscoveryRequest.mockResolvedValue(firstPage);

  mockedPetInteractionRequest.mockImplementation(
    async ({ targetPetId, interaction }) => ({
      id: targetPetId + 1000,
      from_pet_id: 1,
      to_pet_id: targetPetId,
      interaction,
    }),
  );
});

afterEach(() => {
  tokenStorage.clear();
});

function renderDiscovery() {
  return render(
    <MemoryRouter initialEntries={['/discover']}>
      <App />
    </MemoryRouter>,
  );
}

async function waitForDiscovery() {
  await screen.findByRole('heading', {
    level: 2,
    name: 'Milo',
  });

  await waitFor(() => {
    expect(
      screen.getByRole('combobox', {
        name: 'Species',
      }),
    ).toBeEnabled();
  });
}

describe('Discovery page', () => {
  it('persists a like with the target Pet ID and reflects the saved state', async () => {
    const user = userEvent.setup();

    renderDiscovery();
    await waitForDiscovery();

    await user.click(
      screen.getByRole('button', {
        name: 'Like',
      }),
    );

    expect(
      mockedPetInteractionRequest,
    ).toHaveBeenCalledWith({
      token: 'test-token',
      targetPetId: 42,
      interaction: 'liked',
    });

    expect(
      await screen.findByRole('button', {
        name: 'Liked',
      }),
    ).toHaveAttribute('aria-pressed', 'true');
  });

  it('switches an existing like to a persisted dislike', async () => {
    const user = userEvent.setup();

    mockedDiscoveryRequest.mockResolvedValueOnce({
      ...firstPage,
      pets: [
        {
          ...firstPage.pets[0],
          interaction: 'liked',
        },
        ...firstPage.pets.slice(1),
      ],
    });

    renderDiscovery();
    await waitForDiscovery();

    expect(
      screen.getByRole('button', {
        name: 'Liked',
      }),
    ).toHaveAttribute('aria-pressed', 'true');

    await user.click(
      screen.getByRole('button', {
        name: 'Pass',
      }),
    );

    expect(
      mockedPetInteractionRequest,
    ).toHaveBeenLastCalledWith({
      token: 'test-token',
      targetPetId: 42,
      interaction: 'disliked',
    });

    expect(
      await screen.findByRole('button', {
        name: 'Passed',
      }),
    ).toHaveAttribute('aria-pressed', 'true');

    expect(
      screen.getByRole('button', {
        name: 'Like',
      }),
    ).toHaveAttribute('aria-pressed', 'false');
  });

  it('uses stable Pet IDs for interactions from nearby cards', async () => {
    const user = userEvent.setup();

    renderDiscovery();
    await waitForDiscovery();

    await user.click(
      screen.getByRole('button', {
        name: 'Like Luna',
      }),
    );

    expect(
      mockedPetInteractionRequest,
    ).toHaveBeenCalledWith({
      token: 'test-token',
      targetPetId: 43,
      interaction: 'liked',
    });

    expect(
      screen.getByRole('button', {
        name: 'Like Luna',
      }),
    ).toHaveAttribute('aria-pressed', 'true');
  });

  it('keeps the previous interaction state when persistence fails', async () => {
    const user = userEvent.setup();

    mockedPetInteractionRequest.mockRejectedValueOnce(
      new TypeError('Failed to fetch'),
    );

    renderDiscovery();
    await waitForDiscovery();

    await user.click(
      screen.getByRole('button', {
        name: 'Like',
      }),
    );

    expect(
      await screen.findByRole('alert'),
    ).toHaveTextContent(
      'Unable to reach PetMingle. Check your connection and try again.',
    );

    expect(
      screen.getByRole('button', {
        name: 'Like',
      }),
    ).toHaveAttribute('aria-pressed', 'false');
  });

  it('disables repeated interaction clicks while the request is pending', async () => {
    const user = userEvent.setup();

    let resolveInteraction:
      | ((value: {
          id: number;
          from_pet_id: number;
          to_pet_id: number;
          interaction: 'liked';
        }) => void)
      | undefined;

    mockedPetInteractionRequest.mockReturnValueOnce(
      new Promise(resolve => {
        resolveInteraction = resolve;
      }),
    );

    renderDiscovery();
    await waitForDiscovery();

    const like = screen.getByRole('button', {
      name: 'Like',
    });

    await user.click(like);

    expect(
      screen.getByRole('button', {
        name: 'Saving...',
      }),
    ).toBeDisabled();

    expect(
      mockedPetInteractionRequest,
    ).toHaveBeenCalledTimes(1);

    resolveInteraction?.({
      id: 1,
      from_pet_id: 1,
      to_pet_id: 42,
      interaction: 'liked',
    });

    expect(
      await screen.findByRole('button', {
        name: 'Liked',
      }),
    ).toBeEnabled();
  });

  it('loads the documented default Discovery query', async () => {
    renderDiscovery();
    await waitForDiscovery();

    expect(
      mockedTaxonomyRequest,
    ).toHaveBeenCalledWith('test-token');

    expect(
      mockedDiscoveryRequest,
    ).toHaveBeenCalledTimes(1);

    expect(
      mockedDiscoveryRequest,
    ).toHaveBeenCalledWith({
      token: 'test-token',
      radiusKm: 5,
      speciesId: null,
      raceId: null,
      page: 1,
      perPage: 24,
    });

    expect(
      screen.getByRole('combobox', {
        name: 'Distance',
      }),
    ).toHaveValue('5');

    expect(
      screen.getByRole('combobox', {
        name: 'Species',
      }),
    ).toHaveValue('');

    expect(
      screen.getByRole('combobox', {
        name: 'Breed',
      }),
    ).toBeDisabled();
  });

  it('maps distance, species, and race selections to one API refresh', async () => {
    const user = userEvent.setup();

    renderDiscovery();
    await waitForDiscovery();

    await user.selectOptions(
      screen.getByRole('combobox', {
        name: 'Distance',
      }),
      '25',
    );

    await user.selectOptions(
      screen.getByRole('combobox', {
        name: 'Species',
      }),
      '3',
    );

    const breed = screen.getByRole('combobox', {
      name: 'Breed',
    });

    expect(breed).toBeEnabled();

    await user.selectOptions(breed, '7');

    await user.click(
      screen.getByRole('button', {
        name: 'Apply filters',
      }),
    );

    await waitFor(() => {
      expect(
        mockedDiscoveryRequest,
      ).toHaveBeenCalledTimes(2);
    });

    expect(
      mockedDiscoveryRequest,
    ).toHaveBeenLastCalledWith({
      token: 'test-token',
      radiusKm: 25,
      speciesId: 3,
      raceId: 7,
      page: 1,
      perPage: 24,
    });
  });

  it('changing species clears an incompatible draft race', async () => {
    const user = userEvent.setup();

    renderDiscovery();
    await waitForDiscovery();

    const species = screen.getByRole('combobox', {
      name: 'Species',
    });
    const breed = screen.getByRole('combobox', {
      name: 'Breed',
    });

    await user.selectOptions(species, '3');
    await user.selectOptions(breed, '7');

    expect(breed).toHaveValue('7');

    await user.selectOptions(species, '4');

    expect(breed).toHaveValue('');

    expect(
      within(breed).getByRole('option', {
        name: 'Domestic Shorthair',
      }),
    ).toBeInTheDocument();

    expect(
      within(breed).queryByRole('option', {
        name: 'Labrador Retriever',
      }),
    ).not.toBeInTheDocument();
  });

  it('reset restores the default query exactly once', async () => {
    const user = userEvent.setup();

    renderDiscovery();
    await waitForDiscovery();

    await user.selectOptions(
      screen.getByRole('combobox', {
        name: 'Distance',
      }),
      '10',
    );

    await user.selectOptions(
      screen.getByRole('combobox', {
        name: 'Species',
      }),
      '3',
    );

    await user.click(
      screen.getByRole('button', {
        name: 'Apply filters',
      }),
    );

    await waitFor(() => {
      expect(
        mockedDiscoveryRequest,
      ).toHaveBeenCalledTimes(2);
    });

    await user.click(
      screen.getByRole('button', {
        name: 'Clear All',
      }),
    );

    await waitFor(() => {
      expect(
        mockedDiscoveryRequest,
      ).toHaveBeenCalledTimes(3);
    });

    expect(
      mockedDiscoveryRequest,
    ).toHaveBeenLastCalledWith({
      token: 'test-token',
      radiusKm: 5,
      speciesId: null,
      raceId: null,
      page: 1,
      perPage: 24,
    });

    expect(
      screen.getByRole('button', {
        name: 'Clear All',
      }),
    ).toBeDisabled();
  });

  it('does not issue another request when the draft returns to the applied values', async () => {
    const user = userEvent.setup();

    renderDiscovery();
    await waitForDiscovery();

    const apply = screen.getByRole('button', {
      name: 'Apply filters',
    });

    expect(apply).toBeDisabled();

    const distance = screen.getByRole('combobox', {
      name: 'Distance',
    });

    await user.selectOptions(distance, '10');
    expect(apply).toBeEnabled();

    await user.selectOptions(distance, '5');

    expect(apply).toBeDisabled();
    expect(
      mockedDiscoveryRequest,
    ).toHaveBeenCalledTimes(1);
  });

  it('keeps a stable loading state while a new filter query is pending', async () => {
    const user = userEvent.setup();

    let resolveFiltered:
      | ((result: DiscoveryResult) => void)
      | undefined;

    const pending = new Promise<DiscoveryResult>(
      resolve => {
        resolveFiltered = resolve;
      },
    );

    mockedDiscoveryRequest
      .mockResolvedValueOnce(firstPage)
      .mockReturnValueOnce(pending);

    renderDiscovery();
    await waitForDiscovery();

    await user.selectOptions(
      screen.getByRole('combobox', {
        name: 'Distance',
      }),
      '10',
    );

    await user.click(
      screen.getByRole('button', {
        name: 'Apply filters',
      }),
    );

    expect(
      await screen.findByText(
        'Loading nearby pets...',
      ),
    ).toBeVisible();

    expect(
      screen.getByRole('button', {
        name: 'Applying filters...',
      }),
    ).toBeDisabled();

    resolveFiltered?.(firstPage);

    expect(
      await screen.findByRole('heading', {
        level: 2,
        name: 'Milo',
      }),
    ).toBeVisible();
  });

  it('keeps unsupported filters explicitly disabled', async () => {
    renderDiscovery();
    await waitForDiscovery();

    expect(
      screen.getByText(
        'Size, energy and personality filters are not available yet.',
      ),
    ).toBeVisible();

    for (const groupName of [
      'Size (dogs)',
      'Energy Level',
      'Personality',
    ]) {
      const group = within(
        screen.getByRole('group', {
          name: groupName,
        }),
      );

      expect(
        group.getByRole('radio', {
          name: 'All',
        }),
      ).toBeDisabled();
    }
  });

  it('shows a safe network failure and retries with the applied filters', async () => {
    const user = userEvent.setup();

    mockedDiscoveryRequest
      .mockRejectedValueOnce(
        new TypeError('Failed to fetch'),
      )
      .mockResolvedValueOnce(firstPage);

    renderDiscovery();

    expect(
      await screen.findByRole('alert'),
    ).toHaveTextContent(
      'Unable to reach PetMingle. Check your connection and try again.',
    );

    await user.click(
      screen.getByRole('button', {
        name: 'Try again',
      }),
    );

    expect(
      await screen.findByRole('heading', {
        level: 2,
        name: 'Milo',
      }),
    ).toBeVisible();

    expect(
      mockedDiscoveryRequest,
    ).toHaveBeenLastCalledWith({
      token: 'test-token',
      radiusKm: 5,
      speciesId: null,
      raceId: null,
      page: 1,
      perPage: 24,
    });
  });

  it('does not call Discovery until an account location exists', async () => {
    mockedAccountLocationsRequest.mockResolvedValueOnce(
      [],
    );

    renderDiscovery();

    expect(
      await screen.findByText('Set your location'),
    ).toBeVisible();

    expect(
      mockedDiscoveryRequest,
    ).not.toHaveBeenCalled();
  });

  it('keeps active filters for pagination', async () => {
    const user = userEvent.setup();

    mockedDiscoveryRequest
      .mockResolvedValueOnce({
        pets: [
          pet(42, 'Milo', 0.8),
          pet(43, 'Luna', 1.4),
        ],
        meta: {
          current_page: 1,
          last_page: 1,
          per_page: 24,
          total: 2,
        },
      })
      .mockResolvedValueOnce({
        pets: [
          pet(42, 'Milo', 0.8),
          pet(43, 'Luna', 1.4),
        ],
        meta: {
          current_page: 1,
          last_page: 2,
          per_page: 24,
          total: 3,
        },
      })
      .mockResolvedValueOnce({
        pets: [pet(44, 'Bella', 2.2)],
        meta: {
          current_page: 2,
          last_page: 2,
          per_page: 24,
          total: 3,
        },
      });

    renderDiscovery();
    await waitForDiscovery();

    await user.selectOptions(
      screen.getByRole('combobox', {
        name: 'Species',
      }),
      '3',
    );

    await user.click(
      screen.getByRole('button', {
        name: 'Apply filters',
      }),
    );

    await waitFor(() => {
      expect(
        screen.getByRole('button', {
          name: 'Load more pets',
        }),
      ).toBeVisible();
    });

    await user.click(
      screen.getByRole('button', {
        name: 'Load more pets',
      }),
    );

    await waitFor(() => {
      expect(
        mockedDiscoveryRequest,
      ).toHaveBeenCalledTimes(3);
    });

    expect(
      mockedDiscoveryRequest,
    ).toHaveBeenLastCalledWith({
      token: 'test-token',
      radiusKm: 5,
      speciesId: 3,
      raceId: null,
      page: 2,
      perPage: 24,
    });
  });
});
