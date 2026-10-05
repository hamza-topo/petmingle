import {
  render,
  screen,
  within,
} from '@testing-library/react';
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
import {
  authenticatedAuthState,
  authenticatedWithoutPetAuthState,
} from '../../test/authFixtures';
import { relationshipsRequest } from './matches.api';

vi.mock('../../auth/AuthProvider', () => ({
  useAuth: vi.fn(),
}));

vi.mock('./matches.api', () => ({
  relationshipsRequest: vi.fn(),
}));

const mockedUseAuth = vi.mocked(useAuth);
const mockedRelationshipsRequest =
  vi.mocked(relationshipsRequest);

const activeMatch = {
  relationshipId: 11,
  kind: 'match' as const,
  sourcePetId: 42,
  targetPetId: 51,
  name: 'Milo',
  ageYears: 4,
  sex: 1,
  photo: {
    src: '/storage/pets/milo.jpg',
    alt: 'Milo pet photo',
    placeholder: 'Milo',
  },
  about: 'Friendly and curious.',
};

const pastMatch = {
  relationshipId: 12,
  kind: 'mismatch' as const,
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
};

beforeEach(() => {
  tokenStorage.set('test-token');
  mockedUseAuth.mockReturnValue(
    authenticatedAuthState(),
  );
  mockedRelationshipsRequest.mockReset();
  mockedRelationshipsRequest.mockResolvedValue({
    matches: [activeMatch],
    mismatches: [pastMatch],
  });
});

afterEach(() => {
  tokenStorage.clear();
});

function renderMatches() {
  return render(
    <MemoryRouter initialEntries={['/matches']}>
      <App />
    </MemoryRouter>,
  );
}

describe('Matches page', () => {
  it('loads persisted active and past matches for the authenticated pet', async () => {
    renderMatches();

    expect(
      screen.getByRole('status'),
    ).toHaveTextContent('Loading your matches...');

    expect(
      await screen.findByRole('heading', {
        level: 1,
        name: 'Your Matches',
      }),
    ).toBeVisible();

    expect(
      mockedRelationshipsRequest,
    ).toHaveBeenCalledWith({
      token: 'test-token',
      currentPetId: 42,
    });

    expect(
      screen.getByRole('link', {
        name: /Matches/,
      }),
    ).toHaveAttribute('aria-current', 'page');

    const activeSection = within(
      screen.getByRole('region', {
        name: 'Active matches',
      }),
    );

    expect(
      activeSection.getByRole('article', {
        name: 'Milo active match',
      }),
    ).toHaveTextContent('Matched');

    const pastSection = within(
      screen.getByRole('region', {
        name: 'Past matches',
      }),
    );

    expect(
      pastSection.getByRole('article', {
        name: 'Luna past match',
      }),
    ).toHaveTextContent('Past match');
  });

  it('renders an explicit empty state when no relationship exists', async () => {
    mockedRelationshipsRequest.mockResolvedValueOnce({
      matches: [],
      mismatches: [],
    });

    renderMatches();

    expect(
      await screen.findByText('No matches yet'),
    ).toBeVisible();

    expect(
      screen.getByText(
        'When two pets like each other, the persisted match will appear here.',
      ),
    ).toBeVisible();
  });

  it('surfaces a safe API failure', async () => {
    mockedRelationshipsRequest.mockRejectedValueOnce(
      new ApiError(
        'Sensitive backend detail',
        403,
      ),
    );

    renderMatches();

    expect(
      await screen.findByRole('alert'),
    ).toHaveTextContent(
      'You do not have permission to access this information.',
    );

    expect(
      screen.queryByText('Sensitive backend detail'),
    ).not.toBeInTheDocument();
  });

  it('keeps the no-pet state ahead of relationship loading', () => {
    mockedUseAuth.mockReturnValue(
      authenticatedWithoutPetAuthState(),
    );

    renderMatches();

    expect(
      screen.getByRole('heading', {
        name: 'Create your pet profile to continue',
      }),
    ).toBeVisible();

    expect(
      mockedRelationshipsRequest,
    ).not.toHaveBeenCalled();
  });
});
