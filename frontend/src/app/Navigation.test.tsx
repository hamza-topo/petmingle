import {
  render,
  screen,
  within,
} from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { MemoryRouter } from 'react-router';
import {
  afterEach,
  expect,
  it,
  vi,
} from 'vitest';

import { useAuth } from '../auth/AuthProvider';
import { tokenStorage } from '../auth/tokenStorage';
import { currentPetProfileRequest } from '../features/own-profile/profile.api';
import { App } from './App';

vi.mock('../auth/AuthProvider', () => ({
  useAuth: vi.fn(),
}));

vi.mock('../features/own-profile/profile.api', () => ({
  currentPetProfileRequest: vi.fn(),
}));

const mockedUseAuth = vi.mocked(useAuth);
const mockedCurrentPetProfileRequest =
  vi.mocked(currentPetProfileRequest);

afterEach(() => {
  tokenStorage.clear();
});

it('connects all five screens and the Plus section using existing visible controls', async () => {
  const user = userEvent.setup();

  tokenStorage.set('test-token');

  mockedUseAuth.mockReturnValue({
    status: 'authenticated',
    user: {
      id: 10,
      name: 'Hamza',
      email: 'hamza@example.com',
    },
    pet: {
      id: 42,
      user_id: 10,
      name: 'Nala',
    },
    error: null,
    isAuthenticated: true,
    signIn: vi.fn(),
    signOut: vi.fn(),
    refreshIdentity: vi.fn(),
  });

  mockedCurrentPetProfileRequest.mockResolvedValue({
    id: 42,
    userId: 10,
    speciesId: 1,
    raceId: 1,
    name: 'Nala',
    ageYears: 3,
    breed: 'Golden Retriever',
    biography: 'Friendly dog',
  });

  render(
    <MemoryRouter>
      <App />
    </MemoryRouter>,
  );

  await user.click(
    within(
      screen.getByRole('region', {
        name: 'Find their people',
      }),
    ).getByRole('link', {
      name: /Get Started/,
    }),
  );

  expect(
    screen.getByRole('form', {
      name: 'Create pet profile',
    }),
  ).toBeVisible();

  await user.click(
    screen.getByRole('link', {
      name: 'Explore',
    }),
  );

  await user.click(
    screen.getByRole('link', {
      name: /^Profile/,
    }),
  );

  expect(
    await screen.findByRole('heading', {
      name: 'Nala',
      level: 1,
    }),
  ).toBeVisible();

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

  await user.click(
    screen.getByRole('link', {
      name: 'Explore',
    }),
  );

  await user.click(
    screen.getByRole('link', {
      name: /^PetMingle Plus/,
    }),
  );

  expect(
    screen.getByRole('radio', {
      name: '12 Months',
    }),
  ).toBeChecked();

  expect(
    HTMLElement.prototype.scrollIntoView,
  ).toHaveBeenCalled();

  await user.click(
    screen.getByRole('link', {
      name: 'Complete your profile',
    }),
  );

  expect(
    screen.getByRole('form', {
      name: 'Create pet profile',
    }),
  ).toBeVisible();

  await user.click(
    screen.getByRole('link', {
      name: 'How It Works',
    }),
  );

  expect(
    screen.getByRole('heading', {
      name: 'Find their people',
      level: 1,
    }),
  ).toBeVisible();
}, 15000);
