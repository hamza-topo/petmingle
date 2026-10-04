import {
  render,
  screen,
} from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { MemoryRouter } from 'react-router';
import {
  beforeEach,
  describe,
  expect,
  it,
  vi,
} from 'vitest';

import { App } from '../app/App';
import { useAuth } from './AuthProvider';
import {
  authenticatedAuthState,
  authenticatedWithoutPetAuthState,
} from '../test/authFixtures';

vi.mock('./AuthProvider', () => ({
  useAuth: vi.fn(),
}));

const mockedUseAuth = vi.mocked(useAuth);

function renderRoute(path: string) {
  return render(
    <MemoryRouter initialEntries={[path]}>
      <App />
    </MemoryRouter>,
  );
}

describe('PetRequiredRoute', () => {
  beforeEach(() => {
    mockedUseAuth.mockReset();
    mockedUseAuth.mockReturnValue(
      authenticatedWithoutPetAuthState(),
    );
  });

  it.each([
    '/discover',
    '/messages',
    '/profile',
  ])(
    'shows the supported no-pet state for %s',
    path => {
      renderRoute(path);

      expect(
        screen.getByRole('heading', {
          level: 1,
          name: 'Create your pet profile to continue',
        }),
      ).toBeVisible();

      expect(
        screen.getByRole('status'),
      ).toHaveTextContent(
        'You are signed in.',
      );

      expect(
        screen.getByRole('link', {
          name: 'Create pet profile',
        }),
      ).toHaveAttribute(
        'href',
        '/pet/create',
      );

      expect(
        screen.queryByText('Nala'),
      ).not.toBeInTheDocument();
    },
  );

  it('lets an authenticated no-pet user navigate to pet creation', async () => {
    const user = userEvent.setup();

    renderRoute('/profile');

    await user.click(
      screen.getByRole('link', {
        name: 'Create pet profile',
      }),
    );

    expect(
      screen.getByRole('form', {
        name: 'Create pet profile',
      }),
    ).toBeVisible();
  });

  it('renders pet-dependent content when a current pet exists', () => {
    mockedUseAuth.mockReturnValue(
      authenticatedAuthState(),
    );

    renderRoute('/discover');

    expect(
      screen.getByRole('heading', {
        level: 1,
        name: 'Discover Amazing Pets',
      }),
    ).toBeVisible();

    expect(
      screen.queryByRole('heading', {
        level: 1,
        name: 'Create your pet profile to continue',
      }),
    ).not.toBeInTheDocument();
  });
});
