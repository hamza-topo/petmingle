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

import { App } from '../../app/App';
import { useAuth } from '../../auth/AuthProvider';
import { tokenStorage } from '../../auth/tokenStorage';
import { authenticatedAuthState } from '../../test/authFixtures';
import {
  accountLocationsRequest,
  createAccountLocationRequest,
  updateAccountLocationRequest,
} from '../account-location/location.api';
import {
  discoveryRequest,
  type DiscoveryPet,
} from './discovery.api';

vi.mock('../../auth/AuthProvider', () => ({
  useAuth: vi.fn(),
}));

vi.mock('../account-location/location.api', () => ({
  accountLocationsRequest: vi.fn(),
  createAccountLocationRequest: vi.fn(),
  updateAccountLocationRequest: vi.fn(),
}));

vi.mock('./discovery.api', () => ({
  discoveryRequest: vi.fn(),
}));

const mockedUseAuth = vi.mocked(useAuth);
const mockedAccountLocationsRequest =
  vi.mocked(accountLocationsRequest);
const mockedCreateAccountLocationRequest =
  vi.mocked(createAccountLocationRequest);
const mockedUpdateAccountLocationRequest =
  vi.mocked(updateAccountLocationRequest);
const mockedDiscoveryRequest =
  vi.mocked(discoveryRequest);

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
  };
}

const firstPage = {
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

beforeEach(() => {
  mockedUseAuth.mockReturnValue(
    authenticatedAuthState(),
  );

  tokenStorage.set('test-token');

  mockedAccountLocationsRequest.mockReset();
  mockedCreateAccountLocationRequest.mockReset();
  mockedUpdateAccountLocationRequest.mockReset();
  mockedDiscoveryRequest.mockReset();

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

  mockedDiscoveryRequest.mockResolvedValue(
    firstPage,
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

describe('Discovery page', () => {
  it('renders persisted Discovery records without fixture metadata', async () => {
    renderDiscovery();

    expect(
      await screen.findByRole('heading', {
        level: 2,
        name: 'Milo',
      }),
    ).toBeVisible();

    expect(
      screen.getByText('Closest nearby'),
    ).toBeVisible();

    expect(
      screen.getByText(
        'Milo persisted biography.',
      ),
    ).toBeVisible();

    expect(
      screen.getByText('Shared by Owner Milo'),
    ).toBeVisible();

    expect(
      screen.getByText('0.8 km away'),
    ).toBeVisible();

    expect(
      screen.getByText('3 pets near'),
    ).toBeVisible();

    const list = within(
      screen.getByRole('list', {
        name: 'Nearby pets',
      }),
    );

    expect(
      list.getAllByRole('listitem'),
    ).toHaveLength(2);

    expect(
      list.getByRole('article', {
        name: 'Luna',
      }),
    ).toBeVisible();

    expect(
      list.getByRole('article', {
        name: 'Bella',
      }),
    ).toBeVisible();

    expect(
      screen.queryByText('127'),
    ).not.toBeInTheDocument();

    expect(
      screen.queryByRole('img', {
        name: 'Verified pet',
      }),
    ).not.toBeInTheDocument();

    expect(
      screen.queryByText('Lives with'),
    ).not.toBeInTheDocument();

    expect(
      screen.getByRole('button', {
        name: 'Showing 3 Pets',
      }),
    ).toBeVisible();

    expect(
      mockedDiscoveryRequest,
    ).toHaveBeenCalledWith({
      token: 'test-token',
      radiusKm: 5,
      page: 1,
      perPage: 24,
    });
  });

  it('preserves authenticated identity and navigation without fixture badges', async () => {
    renderDiscovery();

    await screen.findByRole('heading', {
      level: 2,
      name: 'Milo',
    });

    const account = screen.getByRole('button', {
      name: 'Hamza account — unavailable in this preview',
    });

    expect(account).toHaveTextContent('Hamza');

    const nav = within(
      screen.getByRole('navigation', {
        name: 'PetMingle navigation',
      }),
    );

    expect(
      nav.getByRole('link', {
        name: /Discover/,
      }),
    ).toHaveAttribute(
      'aria-current',
      'page',
    );

    expect(
      nav.getByRole('button', {
        name: /^Matches/,
      }),
    ).toBeDisabled();

    expect(
      nav.queryByLabelText(/matches$/i),
    ).not.toBeInTheDocument();

    expect(
      nav.queryByLabelText(/unread messages/i),
    ).not.toBeInTheDocument();
  });

  it('renders a stable empty state when the API returns no nearby pets', async () => {
    mockedDiscoveryRequest.mockResolvedValueOnce({
      pets: [],
      meta: {
        current_page: 1,
        last_page: 1,
        per_page: 24,
        total: 0,
      },
    });

    renderDiscovery();

    expect(
      await screen.findByText(
        'No nearby pets yet',
      ),
    ).toBeVisible();

    expect(
      screen.getByText(
        'No persisted pet profiles were found within 5 km.',
      ),
    ).toBeVisible();

    expect(
      screen.queryByRole('list', {
        name: 'Nearby pets',
      }),
    ).not.toBeInTheDocument();

    expect(
      screen.getByRole('button', {
        name: 'Showing 0 Pets',
      }),
    ).toBeVisible();
  });

  it('renders a safe network failure and retries Discovery', async () => {
    const user = userEvent.setup();

    mockedDiscoveryRequest
      .mockRejectedValueOnce(
        new TypeError(
          'Failed to fetch private endpoint',
        ),
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
    ).toHaveBeenCalledTimes(2);
  });

  it('does not call Discovery until an account location exists', async () => {
    mockedAccountLocationsRequest.mockResolvedValueOnce(
      [],
    );

    renderDiscovery();

    expect(
      await screen.findByText(
        'Set your location',
      ),
    ).toBeVisible();

    expect(
      screen.getByRole('button', {
        name: 'Location not set',
      }),
    ).toBeVisible();

    expect(
      mockedDiscoveryRequest,
    ).not.toHaveBeenCalled();
  });

  it('loads Discovery after the user explicitly creates a location', async () => {
    const user = userEvent.setup();

    mockedAccountLocationsRequest.mockResolvedValueOnce(
      [],
    );

    renderDiscovery();

    await user.click(
      await screen.findByRole('button', {
        name: 'Location not set',
      }),
    );

    const editor = screen.getByRole('form', {
      name: 'Account location',
    });

    await user.type(
      within(editor).getByLabelText('Latitude'),
      '31.6295',
    );

    await user.type(
      within(editor).getByLabelText('Longitude'),
      '-7.9811',
    );

    await user.click(
      within(editor).getByRole('button', {
        name: 'Save coordinates',
      }),
    );

    expect(
      await screen.findByRole('heading', {
        level: 2,
        name: 'Milo',
      }),
    ).toBeVisible();

    expect(
      mockedCreateAccountLocationRequest,
    ).toHaveBeenCalledWith({
      token: 'test-token',
      userId: 10,
      coordinates: {
        latitude: 31.6295,
        longitude: -7.9811,
      },
    });

    expect(
      mockedDiscoveryRequest,
    ).toHaveBeenCalledTimes(1);
  });

  it('appends paginated pets without duplicating the closest result', async () => {
    const user = userEvent.setup();

    mockedDiscoveryRequest
      .mockResolvedValueOnce({
        pets: [
          pet(42, 'Milo', 0.8),
          pet(43, 'Luna', 1.4),
        ],
        meta: {
          current_page: 1,
          last_page: 2,
          per_page: 2,
          total: 3,
        },
      })
      .mockResolvedValueOnce({
        pets: [
          pet(44, 'Bella', 2.2),
        ],
        meta: {
          current_page: 2,
          last_page: 2,
          per_page: 2,
          total: 3,
        },
      });

    renderDiscovery();

    await screen.findByRole('heading', {
      level: 2,
      name: 'Milo',
    });

    await user.click(
      screen.getByRole('button', {
        name: 'Load more pets',
      }),
    );

    const list = within(
      screen.getByRole('list', {
        name: 'Nearby pets',
      }),
    );

    expect(
      await list.findByRole('article', {
        name: 'Bella',
      }),
    ).toBeVisible();

    expect(
      list.getAllByRole('listitem'),
    ).toHaveLength(2);

    expect(
      screen.getAllByRole('heading', {
        name: 'Milo',
      }),
    ).toHaveLength(1);

    expect(
      mockedDiscoveryRequest,
    ).toHaveBeenLastCalledWith({
      token: 'test-token',
      radiusKm: 5,
      page: 2,
      perPage: 24,
    });

    expect(
      screen.queryByRole('button', {
        name: 'Load more pets',
      }),
    ).not.toBeInTheDocument();
  });

  it('keeps unsupported filters visibly deferred instead of changing API results locally', async () => {
    renderDiscovery();

    await screen.findByRole('heading', {
      level: 2,
      name: 'Milo',
    });

    expect(
      screen.getByText(
        'Advanced filters are not connected to persisted Discovery data yet.',
      ),
    ).toBeVisible();

    expect(
      screen.getByRole('combobox', {
        name: 'Distance',
      }),
    ).toBeDisabled();

    const species = within(
      screen.getByRole('group', {
        name: 'Species',
      }),
    );

    expect(
      species.getByRole('radio', {
        name: 'All',
      }),
    ).toBeDisabled();
  });
});
