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
import { currentPetProfileRequest } from './profile.api';
import {
  ownPet,
  plusPlans,
  profileStats,
} from './profile.fixtures';

vi.mock('../../auth/AuthProvider', () => ({
  useAuth: vi.fn(),
}));

vi.mock('./profile.api', () => ({
  currentPetProfileRequest: vi.fn(),
}));

const mockedUseAuth = vi.mocked(useAuth);
const mockedCurrentPetProfileRequest =
  vi.mocked(currentPetProfileRequest);

const backendPet = {
  id: 42,
  userId: 10,
  speciesId: 3,
  raceId: 7,
  name: 'Milo',
  ageYears: 4,
  breed: 'Labrador Retriever',
  biography: 'Friendly and curious backend biography.',
};

beforeEach(() => {
  mockedUseAuth.mockReturnValue(
    authenticatedAuthState(),
  );

  tokenStorage.set('test-token');

  mockedCurrentPetProfileRequest.mockReset();
  mockedCurrentPetProfileRequest.mockResolvedValue(
    backendPet,
  );
});

afterEach(() => {
  tokenStorage.clear();
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
      details.getByText(ownPet.location),
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

  it('renders exactly the three fixture statistics', async () => {
    renderProfile();
    await waitForProfile();

    const stats = within(
      screen.getByLabelText(
        'Pet profile statistics',
      ),
    );

    expect(
      stats.getAllByRole('term'),
    ).toHaveLength(profileStats.length);

    for (const stat of profileStats) {
      expect(
        stats.getByText(stat.label),
      ).toBeVisible();

      expect(
        stats.getByText(String(stat.value)),
      ).toBeVisible();
    }
  });

  it('renders four thumbnails and changes the active main image locally', async () => {
    const user = userEvent.setup();

    renderProfile();
    await waitForProfile();

    const gallery = within(
      screen.getByRole('group', {
        name: 'Pet photo gallery',
      }),
    );

    expect(
      gallery.getAllByRole('button', {
        name: /View photo/,
      }),
    ).toHaveLength(ownPet.gallery.length);

    expect(
      gallery.getByRole('button', {
        name: 'View photo 1',
      }),
    ).toHaveAttribute(
      'aria-pressed',
      'true',
    );

    await user.click(
      gallery.getByRole('button', {
        name: 'View photo 3',
      }),
    );

    expect(
      gallery.getByRole('button', {
        name: 'View photo 3',
      }),
    ).toHaveAttribute(
      'aria-pressed',
      'true',
    );

    expect(
      gallery.getByRole('button', {
        name: 'View photo 1',
      }),
    ).toHaveAttribute(
      'aria-pressed',
      'false',
    );

    const overview = screen.getByRole(
      'region',
      {
        name: 'Pet profile overview',
      },
    );

    expect(
      overview.querySelector(
        '.own-main-photo [role="img"]',
      ),
    ).toHaveAccessibleName(
      /Nala — Running outdoors/,
    );
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
