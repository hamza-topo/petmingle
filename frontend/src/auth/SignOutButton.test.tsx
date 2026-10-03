import { render, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import {
  MemoryRouter,
  Route,
  Routes,
} from 'react-router';
import {
  beforeEach,
  describe,
  expect,
  it,
  vi,
} from 'vitest';
import { useAuth } from './AuthProvider';
import { SignOutButton } from './SignOutButton';

vi.mock('./AuthProvider', () => ({
  useAuth: vi.fn(),
}));

const mockedUseAuth = vi.mocked(useAuth);

const signOut = vi.fn();

function renderButton() {
  return render(
    <MemoryRouter initialEntries={['/profile']}>
      <Routes>
        <Route
          path="/profile"
          element={<SignOutButton />}
        />

        <Route
          path="/signin"
          element={<div>Sign in destination</div>}
        />
      </Routes>
    </MemoryRouter>,
  );
}

describe('SignOutButton', () => {
  beforeEach(() => {
    signOut.mockReset();

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
      signOut,
    });
  });

  it('signs out and navigates to sign in', async () => {
    const user = userEvent.setup();

    signOut.mockResolvedValue(undefined);

    renderButton();

    await user.click(
      screen.getByRole('button', { name: 'Sign out' }),
    );

    expect(signOut).toHaveBeenCalledTimes(1);

    expect(
      await screen.findByText('Sign in destination'),
    ).toBeInTheDocument();
  });

  it('shows a safe error when sign out fails', async () => {
    const user = userEvent.setup();

    signOut.mockRejectedValue(
      new TypeError('Failed to fetch'),
    );

    renderButton();

    await user.click(
      screen.getByRole('button', { name: 'Sign out' }),
    );

    expect(
      await screen.findByRole('alert'),
    ).toHaveTextContent(
      'Unable to sign out. Please try again.',
    );

    expect(
      screen.queryByText('Sign in destination'),
    ).not.toBeInTheDocument();
  });
});