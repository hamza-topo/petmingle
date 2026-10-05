import { render, screen, within } from '@testing-library/react';
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
import { discoveryContext, featuredDiscoveryPet, filterGroups, nearbyPets } from './discovery.fixtures';
import { useAuth } from '../../auth/AuthProvider';
import { authenticatedAuthState } from '../../test/authFixtures';
import { tokenStorage } from '../../auth/tokenStorage';
import {
  accountLocationsRequest,
  createAccountLocationRequest,
  updateAccountLocationRequest,
} from '../account-location/location.api';

vi.mock('../../auth/AuthProvider', () => ({
  useAuth: vi.fn(),
}));

vi.mock('../account-location/location.api', () => ({
  accountLocationsRequest: vi.fn(),
  createAccountLocationRequest: vi.fn(),
  updateAccountLocationRequest: vi.fn(),
}));

const mockedUseAuth = vi.mocked(useAuth);
const mockedAccountLocationsRequest =
  vi.mocked(accountLocationsRequest);
const mockedCreateAccountLocationRequest =
  vi.mocked(createAccountLocationRequest);
const mockedUpdateAccountLocationRequest =
  vi.mocked(updateAccountLocationRequest);

beforeEach(() => {
  mockedUseAuth.mockReturnValue(authenticatedAuthState());

  tokenStorage.set('test-token');

  mockedAccountLocationsRequest.mockReset();
  mockedCreateAccountLocationRequest.mockReset();
  mockedUpdateAccountLocationRequest.mockReset();

  mockedAccountLocationsRequest.mockResolvedValue([]);
  mockedCreateAccountLocationRequest.mockResolvedValue({
    id: 1,
    user_id: 10,
    latitude: 31.6295,
    longitude: -7.9811,
  });
  mockedUpdateAccountLocationRequest.mockResolvedValue({
    id: 2,
    user_id: 10,
    latitude: 30.4278,
    longitude: -9.5981,
  });
});

afterEach(() => {
  tokenStorage.clear();
});
function renderDiscovery() {
  return render(<MemoryRouter initialEntries={['/discover']}><App /></MemoryRouter>);
}

describe('Discovery page', () => {
  it('renders the Discovery route and the search controls', () => {
    renderDiscovery();
    expect(screen.getByRole('heading', { level: 1, name: 'Discover Amazing Pets' })).toBeVisible();
    expect(screen.getByRole('searchbox', { name: 'Search pets, people, or locations' })).toBeVisible();
    expect(screen.getByRole('heading', { name: 'More Amazing Pets Nearby' })).toBeVisible();
  });

  it('renders the authenticated user identity in the header', () => {
    renderDiscovery();

    const account = screen.getByRole('button', {
      name: 'Hamza account — unavailable in this preview',
    });

    expect(account).toBeVisible();
    expect(account).toHaveTextContent('Hamza');
    expect(
      within(account).getByRole('img'),
    ).toHaveAccessibleName(/Hamza avatar/);

    expect(
      screen.queryByText('Sarah'),
    ).not.toBeInTheDocument();
  });

  it('shows an explicit missing-location state and creates coordinates from user input', async () => {
    const user = userEvent.setup();

    renderDiscovery();

    const locationButton = await screen.findByRole(
      'button',
      {
        name: 'Location not set',
      },
    );

    await user.click(locationButton);

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
      await screen.findByRole('button', {
        name: '31.629500, -7.981100',
      }),
    ).toBeVisible();
  });

  it('updates the current persisted account location instead of creating another one', async () => {
    const user = userEvent.setup();

    mockedAccountLocationsRequest.mockResolvedValueOnce([
      {
        id: 2,
        user_id: 10,
        latitude: 31.6295,
        longitude: -7.9811,
      },
    ]);

    renderDiscovery();

    await user.click(
      await screen.findByRole('button', {
        name: '31.629500, -7.981100',
      }),
    );

    const editor = screen.getByRole('form', {
      name: 'Account location',
    });

    await user.clear(
      within(editor).getByLabelText('Latitude'),
    );
    await user.type(
      within(editor).getByLabelText('Latitude'),
      '30.4278',
    );

    await user.clear(
      within(editor).getByLabelText('Longitude'),
    );
    await user.type(
      within(editor).getByLabelText('Longitude'),
      '-9.5981',
    );

    await user.click(
      within(editor).getByRole('button', {
        name: 'Save coordinates',
      }),
    );

    expect(
      mockedUpdateAccountLocationRequest,
    ).toHaveBeenCalledWith({
      token: 'test-token',
      userId: 10,
      locationId: 2,
      coordinates: {
        latitude: 30.4278,
        longitude: -9.5981,
      },
    });

    expect(
      mockedCreateAccountLocationRequest,
    ).not.toHaveBeenCalled();
  });

  it('shows a safe location load failure and retries it', async () => {
    const user = userEvent.setup();

    mockedAccountLocationsRequest
      .mockRejectedValueOnce(
        new TypeError('Failed to fetch private location endpoint'),
      )
      .mockResolvedValueOnce([]);

    renderDiscovery();

    expect(
      await screen.findByRole('button', {
        name: 'Retry location',
      }),
    ).toBeVisible();

    expect(
      screen.getByRole('button', {
        name: 'Location unavailable',
      }),
    ).toBeDisabled();

    await user.click(
      screen.getByRole('button', {
        name: 'Retry location',
      }),
    );

    expect(
      await screen.findByRole('button', {
        name: 'Location not set',
      }),
    ).toBeVisible();

    expect(
      mockedAccountLocationsRequest,
    ).toHaveBeenCalledTimes(2);
  });

  it('renders navigation with Discover active and links to implemented screens', () => {
    renderDiscovery();
    const nav = within(screen.getByRole('navigation', { name: 'PetMingle navigation' }));
    expect(nav.getByRole('link', { name: /Discover/ })).toHaveAttribute('aria-current', 'page');
    expect(nav.getByRole('button', { name: /^Matches/ })).toBeDisabled();
    for (const [name, href] of [['Messages', '/messages'], ['Profile', '/profile'], ['PetMingle Plus', '/profile#petmingle-plus']]) {
      expect(nav.getByRole('link', { name: new RegExp(name) })).toHaveAttribute('href', href);
    }
  });

  it('renders the featured pet and companion from fixtures', () => {
    renderDiscovery();
    const card = within(screen.getByRole('article', { name: featuredDiscoveryPet.name }));
    expect(card.getByText(featuredDiscoveryPet.description)).toBeVisible();
    expect(card.getByText(featuredDiscoveryPet.companion.name, { selector: 'strong' })).toBeVisible();
    expect(card.getByText(`1 / ${featuredDiscoveryPet.photoCount}`)).toBeVisible();
    for (const trait of featuredDiscoveryPet.traits) expect(card.getByText(trait.label)).toBeVisible();
    expect(card.getByRole('button', { name: /Say Hello/ })).toBeDisabled();
  });

  it('renders all six nearby pet cards with distance and trait metadata', () => {
    renderDiscovery();
    const list = within(screen.getByRole('list', { name: 'Nearby pets' }));
    expect(list.getAllByRole('listitem')).toHaveLength(nearbyPets.length);
    for (const pet of nearbyPets) {
      const card = within(list.getByRole('article', { name: pet.name }));
      expect(card.getByText(pet.breed)).toBeVisible();
      expect(card.getByText(`${pet.distanceMiles.toFixed(1)} miles away`)).toBeVisible();
      for (const trait of pet.traits) expect(card.getByText(trait.label)).toBeVisible();
    }
  });

  it('renders visible filters with the reference defaults', () => {
    renderDiscovery();
    const filters = within(screen.getByRole('complementary', { name: 'Filter Pets' }));
    expect(filters.getByRole('combobox', { name: 'Distance' })).toHaveValue('10');
    for (const group of filterGroups) {
      const choices = within(filters.getByRole('group', { name: group.label }));
      for (const option of group.options) expect(choices.getByRole('radio', { name: option })).toBeInTheDocument();
      expect(choices.getByRole('radio', { name: 'All' })).toBeChecked();
    }
    expect(filters.getByRole('button', { name: `Show ${discoveryContext.petCount} Pets` })).toBeVisible();
  });

  it('selects and resets filters locally without changing the fixture results', async () => {
    const user = userEvent.setup();
    renderDiscovery();
    const species = within(screen.getByRole('group', { name: 'Species' }));
    await user.click(species.getByRole('radio', { name: 'Cats' }));
    expect(species.getByRole('radio', { name: 'Cats' })).toBeChecked();
    expect(species.getByRole('radio', { name: 'All' })).not.toBeChecked();
    expect(within(screen.getByRole('list', { name: 'Nearby pets' })).getAllByRole('listitem')).toHaveLength(nearbyPets.length);
    await user.click(screen.getByRole('button', { name: 'Clear All' }));
    expect(species.getByRole('radio', { name: 'All' })).toBeChecked();
  });

  it('opens Discovery from Landing and returns Home through the logo', async () => {
    const user = userEvent.setup();
    render(<MemoryRouter initialEntries={['/']}><App /></MemoryRouter>);
    await user.click(screen.getByRole('link', { name: 'Explore Pets' }));
    expect(screen.getByRole('heading', { level: 1, name: 'Discover Amazing Pets' })).toBeVisible();
    await user.click(screen.getByRole('link', { name: 'PetMingle home' }));
    expect(screen.getByRole('heading', { level: 1, name: 'Find their people' })).toBeVisible();
  });
});
