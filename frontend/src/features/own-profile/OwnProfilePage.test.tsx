import {
  render,
  screen,
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

import { ApiError } from '../../api/errors';
import { App } from '../../app/App';
import { useAuth } from '../../auth/AuthProvider';
import { tokenStorage } from '../../auth/tokenStorage';
import { authenticatedAuthState } from '../../test/authFixtures';
import {
  currentPetProfileRequest,
  removePetImageRequest,
  replacePetImageRequest,
  updatePetProfileRequest,
} from './profile.api';
import { taxonomyRequest } from '../profile-creation/taxonomy.api';
import { accountLocationsRequest } from '../account-location/location.api';
import {
  ownPet,
  plusPlans,
} from './profile.fixtures';

vi.mock('../../auth/AuthProvider', () => ({
  useAuth: vi.fn(),
}));

vi.mock('./profile.api', () => ({
  currentPetProfileRequest: vi.fn(),
  removePetImageRequest: vi.fn(),
  replacePetImageRequest: vi.fn(),
  updatePetProfileRequest: vi.fn(),
}));

vi.mock('../profile-creation/taxonomy.api', () => ({
  taxonomyRequest: vi.fn(),
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

const mockedUseAuth = vi.mocked(useAuth);
const mockedCurrentPetProfileRequest =
  vi.mocked(currentPetProfileRequest);
const mockedUpdatePetProfileRequest =
  vi.mocked(updatePetProfileRequest);
const mockedReplacePetImageRequest =
  vi.mocked(replacePetImageRequest);
const mockedRemovePetImageRequest =
  vi.mocked(removePetImageRequest);
const mockedTaxonomyRequest =
  vi.mocked(taxonomyRequest);
const mockedAccountLocationsRequest =
  vi.mocked(accountLocationsRequest);
const refreshIdentity = vi.fn();

const backendPet = {
  id: 42,
  userId: 10,
  speciesId: 3,
  raceId: 7,
  name: 'Milo',
  ageYears: 4,
  breed: 'Labrador Retriever',
  biography: 'Friendly and curious backend biography.',
  images: ['pets/milo.jpg'],
  statistics: {
    matches: 7,
    likesSent: 11,
  },
};

beforeEach(() => {
  refreshIdentity.mockReset();
  refreshIdentity.mockResolvedValue(undefined);

  mockedUseAuth.mockReturnValue({
    ...authenticatedAuthState(),
    refreshIdentity,
  });

  tokenStorage.set('test-token');

  mockedAccountLocationsRequest.mockReset();
  mockedAccountLocationsRequest.mockResolvedValue([
    {
      id: 4,
      user_id: 10,
      latitude: 31.6295,
      longitude: -7.9811,
    },
  ]);

  mockedCurrentPetProfileRequest.mockReset();
  mockedCurrentPetProfileRequest.mockResolvedValue(
    backendPet,
  );

  mockedUpdatePetProfileRequest.mockReset();
  mockedReplacePetImageRequest.mockReset();
  mockedRemovePetImageRequest.mockReset();
  mockedReplacePetImageRequest.mockResolvedValue({
    id: 42,
    user_id: 10,
    species_id: 3,
    race_id: 7,
    name: 'Milo',
    age: 4,
    sexe: 1,
    color: 'brown',
    images: ['pets/replaced.png'],
    about: backendPet.biography,
  });

  mockedRemovePetImageRequest.mockResolvedValue({
    id: 42,
    user_id: 10,
    species_id: 3,
    race_id: 7,
    name: 'Milo',
    age: 4,
    sexe: 1,
    color: 'brown',
    images: [],
    about: backendPet.biography,
  });

  mockedUpdatePetProfileRequest.mockResolvedValue({
    id: 42,
    user_id: 10,
    species_id: 3,
    race_id: 7,
    name: 'Milo',
    age: 4,
    sexe: 1,
    color: 'brown',
    images: [],
    about: backendPet.biography,
  });

  mockedTaxonomyRequest.mockReset();
  vi.stubGlobal(
    'URL',
    Object.assign(URL, {
      createObjectURL: vi.fn(
        () => 'blob:profile-preview',
      ),
      revokeObjectURL: vi.fn(),
    }),
  );

  mockedTaxonomyRequest.mockResolvedValue({
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
  });
});

afterEach(() => {
  tokenStorage.clear();
  vi.unstubAllGlobals();
});

function renderProfile() {
  return render(
    <MemoryRouter
      initialEntries={['/profile']}
    >
      <App />
    </MemoryRouter>,
  );
}

async function waitForProfile() {
  return screen.findByRole('heading', {
    level: 1,
    name: backendPet.name,
  });
}

describe('Own pet profile', () => {
  it('loads the authenticated pet ID and renders authoritative backend fields', async () => {
    renderProfile();

    expect(
      screen.getByRole('status'),
    ).toHaveTextContent('Loading pet profile...');

    await waitForProfile();

    expect(
      mockedCurrentPetProfileRequest,
    ).toHaveBeenCalledWith({
      petId: 42,
      userId: 10,
      token: 'test-token',
    });

    expect(
      screen.getAllByRole('img', {
        name: 'Milo saved photo 1',
      }),
    ).toHaveLength(2);

    expect(
      screen.getAllByRole('img', {
        name: 'Milo saved photo 1',
      })[0],
    ).toHaveAttribute(
      'src',
      '/storage/pets/milo.jpg',
    );

    expect(
      screen.getByRole('region', {
        name: `About ${backendPet.name}`,
      }),
    ).toHaveTextContent(backendPet.biography);

    expect(
      screen.getByText(
        `${backendPet.ageYears} years old`,
      ),
    ).toBeVisible();

    const details = within(
      screen.getByRole('region', {
        name: 'Details',
      }),
    );

    expect(
      details.getByText(backendPet.breed),
    ).toBeVisible();

    expect(
      details.getByText(
        `${backendPet.ageYears} years old`,
      ),
    ).toBeVisible();

    expect(
      details.getByText('31.629500, -7.981100'),
    ).toBeVisible();

    expect(
      details.getByText('62 lbs'),
    ).toBeVisible();

    for (const trait of ownPet.traits) {
      expect(
        screen.getByText(trait.label),
      ).toBeVisible();
    }
  });

  it('edits persisted fields, refreshes identity, and reloads the profile', async () => {
    const user = userEvent.setup();

    const updatedPet = {
      ...backendPet,
      name: 'Luna',
      ageYears: 5,
      biography: 'Updated backend biography.',
    };

    mockedCurrentPetProfileRequest
      .mockResolvedValueOnce(backendPet)
      .mockResolvedValueOnce(updatedPet);

    mockedUpdatePetProfileRequest.mockResolvedValueOnce({
      id: 42,
      user_id: 10,
      species_id: 3,
      race_id: 7,
      name: 'Luna',
      age: 5,
      sexe: 1,
      color: 'brown',
      images: [],
      about: 'Updated backend biography.',
    });

    renderProfile();
    await waitForProfile();

    await user.click(
      screen.getByRole('button', {
        name: 'Edit pet name',
      }),
    );

    const editForm = await screen.findByRole('form', {
      name: 'Edit pet profile',
    });

    expect(editForm).toHaveTextContent(
      'Location, weight, traits, compatibility',
    );

    await user.clear(
      within(editForm).getByLabelText('Pet name'),
    );
    await user.type(
      within(editForm).getByLabelText('Pet name'),
      'Luna',
    );

    await user.clear(
      within(editForm).getByLabelText('Age'),
    );
    await user.type(
      within(editForm).getByLabelText('Age'),
      '5',
    );

    await user.clear(
      within(editForm).getByLabelText('Biography'),
    );
    await user.type(
      within(editForm).getByLabelText('Biography'),
      'Updated backend biography.',
    );

    await user.click(
      within(editForm).getByRole('button', {
        name: 'Save changes',
      }),
    );

    expect(
      mockedUpdatePetProfileRequest,
    ).toHaveBeenCalledWith({
      petId: 42,
      token: 'test-token',
      input: {
        speciesId: 3,
        raceId: 7,
        name: 'Luna',
        ageYears: 5,
        biography: 'Updated backend biography.',
      },
    });

    expect(refreshIdentity).toHaveBeenCalledTimes(1);

    expect(
      await screen.findByRole('heading', {
        level: 1,
        name: 'Luna',
      }),
    ).toBeVisible();

    expect(
      mockedCurrentPetProfileRequest,
    ).toHaveBeenCalledTimes(2);
  });

  it('maps backend validation failures to edit controls', async () => {
    const user = userEvent.setup();

    mockedUpdatePetProfileRequest.mockRejectedValueOnce(
      new ApiError(
        'Validation failed.',
        422,
        {
          success: false,
          message: 'Validation failed.',
          errors: {
            name: ['The pet name is invalid.'],
            about: ['The biography is invalid.'],
          },
        },
      ),
    );

    renderProfile();
    await waitForProfile();

    await user.click(
      screen.getByRole('button', {
        name: 'Edit pet biography',
      }),
    );

    const editForm = await screen.findByRole('form', {
      name: 'Edit pet profile',
    });

    await user.click(
      within(editForm).getByRole('button', {
        name: 'Save changes',
      }),
    );

    expect(
      await within(editForm).findByText(
        'The pet name is invalid.',
      ),
    ).toBeVisible();

    expect(
      within(editForm).getByText(
        'The biography is invalid.',
      ),
    ).toBeVisible();

    expect(refreshIdentity).not.toHaveBeenCalled();
  });

  it('shows a safe authorization failure while preserving edit values', async () => {
    const user = userEvent.setup();

    mockedUpdatePetProfileRequest.mockRejectedValueOnce(
      new ApiError(
        'Sensitive policy detail',
        403,
        {
          success: false,
          message: 'Sensitive policy detail',
        },
      ),
    );

    renderProfile();
    await waitForProfile();

    await user.click(
      screen.getByRole('button', {
        name: 'Edit pet details',
      }),
    );

    const editForm = await screen.findByRole('form', {
      name: 'Edit pet profile',
    });

    await user.clear(
      within(editForm).getByLabelText('Pet name'),
    );
    await user.type(
      within(editForm).getByLabelText('Pet name'),
      'Luna',
    );

    await user.click(
      within(editForm).getByRole('button', {
        name: 'Save changes',
      }),
    );

    expect(
      await within(editForm).findByRole('alert'),
    ).toHaveTextContent(
      'You do not have permission to access this information.',
    );

    expect(
      within(editForm).getByLabelText('Pet name'),
    ).toHaveValue('Luna');

    expect(
      screen.queryByText('Sensitive policy detail'),
    ).not.toBeInTheDocument();
  });

  it('renders only persisted profile statistics', async () => {
    renderProfile();
    await waitForProfile();

    const stats = within(
      screen.getByLabelText(
        'Pet profile statistics',
      ),
    );

    expect(stats.getAllByRole('term')).toHaveLength(2);
    expect(stats.getByText('Matches')).toBeVisible();
    expect(stats.getByText('7')).toBeVisible();
    expect(stats.getByText('Likes sent')).toBeVisible();
    expect(stats.getByText('11')).toBeVisible();

    expect(
      screen.queryByText('Profile views'),
    ).not.toBeInTheDocument();

    expect(
      screen.queryByText('Favorites'),
    ).not.toBeInTheDocument();
  });

  it('renders zero for empty persisted profile statistics', async () => {
    mockedCurrentPetProfileRequest.mockResolvedValueOnce({
      ...backendPet,
      statistics: {
        matches: 0,
        likesSent: 0,
      },
    });

    renderProfile();
    await waitForProfile();

    const stats = within(
      screen.getByLabelText(
        'Pet profile statistics',
      ),
    );

    expect(stats.getAllByRole('definition')).toHaveLength(2);

    for (const value of stats.getAllByRole('definition')) {
      expect(value).toHaveTextContent('0');
    }
  });

  it('previews a local replacement, persists it, and releases its object URL', async () => {
    const user = userEvent.setup();

    const updatedProfile = {
      ...backendPet,
      images: ['pets/replaced.png'],
    };

    mockedCurrentPetProfileRequest
      .mockResolvedValueOnce(backendPet)
      .mockResolvedValueOnce(updatedProfile);

    renderProfile();
    await waitForProfile();

    const photo = new File(
      ['photo'],
      'replacement.png',
      {
        type: 'image/png',
      },
    );

    await user.upload(
      screen.getByLabelText('Pet media upload'),
      photo,
    );

    const previews = screen.getAllByRole('img', {
      name: 'Milo pending photo preview',
    });
    expect(previews).toHaveLength(2);
    for (const preview of previews) {
      expect(preview).toHaveAttribute('src', 'blob:profile-preview');
    }

    await user.click(
      screen.getByRole('button', {
        name: 'Save photo',
      }),
    );

    expect(
      mockedReplacePetImageRequest,
    ).toHaveBeenCalledWith({
      petId: 42,
      token: 'test-token',
      image: photo,
    });

    expect(
      URL.revokeObjectURL,
    ).toHaveBeenCalledTimes(1);

    const savedImages =
      await screen.findAllByRole('img', {
        name: 'Milo saved photo 1',
      });

    expect(
      savedImages.some(
        image =>
          image.getAttribute('src')
          === '/storage/pets/replaced.png',
      ),
    ).toBe(true);
  });

  it('keeps saved media intact when replacement fails', async () => {
    const user = userEvent.setup();

    mockedReplacePetImageRequest.mockRejectedValueOnce(
      new ApiError(
        'Sensitive storage failure',
        500,
      ),
    );

    renderProfile();
    await waitForProfile();

    await user.upload(
      screen.getByLabelText('Pet media upload'),
      new File(
        ['photo'],
        'replacement.png',
        {
          type: 'image/png',
        },
      ),
    );

    await user.click(
      screen.getByRole('button', {
        name: 'Save photo',
      }),
    );

    expect(
      await screen.findByRole('alert'),
    ).toHaveTextContent(
      'PetMingle is temporarily unavailable. Please try again.',
    );

    expect(
      screen.queryByRole('img', {
        name: 'Milo pending photo preview',
      }),
    ).not.toBeInTheDocument();

    expect(
      screen.getAllByRole('img', {
        name: 'Milo saved photo 1',
      })[0],
    ).toHaveAttribute(
      'src',
      '/storage/pets/milo.jpg',
    );

    expect(
      mockedCurrentPetProfileRequest,
    ).toHaveBeenCalledTimes(1);
  });

  it('removes persisted media and renders the empty gallery state', async () => {
    const user = userEvent.setup();

    mockedCurrentPetProfileRequest
      .mockResolvedValueOnce(backendPet)
      .mockResolvedValueOnce({
        ...backendPet,
        images: [],
      });

    renderProfile();
    await waitForProfile();

    await user.click(
      screen.getByRole('button', {
        name: 'Remove photo',
      }),
    );

    expect(
      mockedRemovePetImageRequest,
    ).toHaveBeenCalledWith({
      petId: 42,
      token: 'test-token',
    });

    expect(
      await screen.findByText(
        'No saved pet photo yet.',
      ),
    ).toBeVisible();

    expect(
      screen.getByRole('img', {
        name: 'No pet photo yet',
      }),
    ).toBeVisible();
  });

  it('supports a pet with no persisted media from the first render', async () => {
    mockedCurrentPetProfileRequest.mockResolvedValueOnce({
      ...backendPet,
      images: [],
    });

    renderProfile();
    await waitForProfile();

    expect(
      screen.getByText('No saved pet photo yet.'),
    ).toBeVisible();

    expect(
      screen.getByRole('button', {
        name: 'Add Photo',
      }),
    ).toBeVisible();
  });

  it('renders Plus prices and changes the selected plan without enabling purchase', async () => {
    const user = userEvent.setup();

    renderProfile();
    await waitForProfile();

    for (const plan of plusPlans) {
      expect(
        screen.getByRole('radio', {
          name: plan.label,
        }),
      ).toBeVisible();

      expect(
        screen.getByText(plan.monthlyPrice),
      ).toBeVisible();
    }

    expect(
      screen.getByRole('radio', {
        name: '12 Months',
      }),
    ).toBeChecked();

    await user.click(
      screen.getByRole('radio', {
        name: '3 Months',
      }),
    );

    expect(
      screen.getByRole('radio', {
        name: '3 Months',
      }),
    ).toBeChecked();

    expect(
      screen.getByRole('radio', {
        name: '12 Months',
      }),
    ).not.toBeChecked();

    expect(
      screen.getByText('Most Popular'),
    ).toBeVisible();

    expect(
      screen.getByRole('button', {
        name: /Try PetMingle Plus/,
      }),
    ).toBeDisabled();
  });

  it('opens Messaging while Home remains a destination rather than the current route', async () => {
    const user = userEvent.setup();

    renderProfile();
    await waitForProfile();

    expect(
      screen.getByRole('link', {
        name: 'Home',
      }),
    ).not.toHaveAttribute('aria-current');

    const petMenu = screen.getByRole('button', {
      name: `${backendPet.name} pet menu — unavailable`,
    });

    expect(petMenu).toBeVisible();

    expect(
      within(petMenu).getByRole('img'),
    ).toHaveAccessibleName(/Milo avatar/);

    expect(
      within(petMenu).getByRole('img'),
    ).not.toHaveAccessibleName(/Nala/);

    await user.click(
      screen.getByRole('link', {
        name: 'Messages',
      }),
    );

    expect(
      screen.getByRole('heading', {
        name: 'Messages',
        level: 1,
      }),
    ).toBeVisible();
  });

  it('reloads the same current pet identity after a profile remount', async () => {
    const firstRender = renderProfile();

    await waitForProfile();

    expect(
      screen.getByRole('button', {
        name: `${backendPet.name} pet menu — unavailable`,
      }),
    ).toBeVisible();

    firstRender.unmount();

    renderProfile();

    await waitForProfile();

    expect(
      screen.getByRole('button', {
        name: `${backendPet.name} pet menu — unavailable`,
      }),
    ).toBeVisible();

    expect(
      mockedCurrentPetProfileRequest,
    ).toHaveBeenCalledTimes(2);

    expect(
      mockedCurrentPetProfileRequest,
    ).toHaveBeenNthCalledWith(1, {
      petId: 42,
      userId: 10,
      token: 'test-token',
    });

    expect(
      mockedCurrentPetProfileRequest,
    ).toHaveBeenNthCalledWith(2, {
      petId: 42,
      userId: 10,
      token: 'test-token',
    });
  });

  it('shows a safe error state and retries profile loading', async () => {
    const user = userEvent.setup();

    mockedCurrentPetProfileRequest
      .mockRejectedValueOnce(
        new TypeError('Failed to fetch private endpoint'),
      )
      .mockResolvedValueOnce(backendPet);

    renderProfile();

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
      await waitForProfile(),
    ).toBeVisible();

    expect(
      mockedCurrentPetProfileRequest,
    ).toHaveBeenCalledTimes(2);
  });

  it('shows a safe non-retryable state for a missing profile', async () => {
    mockedCurrentPetProfileRequest.mockRejectedValueOnce(
      new ApiError(
        'Internal record details',
        404,
      ),
    );

    renderProfile();

    expect(
      await screen.findByRole('alert'),
    ).toHaveTextContent(
      'The requested information is no longer available.',
    );

    expect(
      screen.queryByText('Internal record details'),
    ).not.toBeInTheDocument();

    expect(
      screen.queryByRole('button', {
        name: 'Try again',
      }),
    ).not.toBeInTheDocument();
  });
});
