import { render, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { MemoryRouter, Route, Routes } from 'react-router';
import { beforeEach, describe, expect, it, vi } from 'vitest';
import { ApiError } from '../../api/errors';
import { useAuth } from '../../auth/AuthProvider';
import { SignInPage } from './SignInPage';

vi.mock('../../auth/AuthProvider', () => ({
  useAuth: vi.fn(),
}));

const mockedUseAuth = vi.mocked(useAuth);
const signIn = vi.fn();

function renderPage() {
  return render(
    <MemoryRouter initialEntries={['/signin']}>
      <Routes>
        <Route path="/signin" element={<SignInPage />} />
        <Route
          path="/discover"
          element={<div>Discovery destination</div>}
        />
      </Routes>
    </MemoryRouter>,
  );
}

describe('SignInPage', () => {
  beforeEach(() => {
    signIn.mockReset();

    mockedUseAuth.mockReturnValue({
      status: 'unauthenticated',
      user: null,
      pet: null,
      error: null,
      isAuthenticated: false,
      signIn,
      signOut: vi.fn(),
    refreshIdentity: vi.fn(),
      refreshIdentity: vi.fn(),
    });
  });

  it('returns to the originally requested protected route after sign in', async () => {
    const user = userEvent.setup();

    signIn.mockResolvedValue(undefined);

    render(
      <MemoryRouter
        initialEntries={[
          {
            pathname: '/signin',
            state: { from: '/messages' },
          },
        ]}
      >
        <Routes>
          <Route path="/signin" element={<SignInPage />} />
          <Route
            path="/messages"
            element={<div>Messages destination</div>}
          />
        </Routes>
      </MemoryRouter>,
    );

    await user.type(
      screen.getByRole('textbox', { name: 'Email' }),
      'hamza@example.com',
    );

    await user.type(
      screen.getByLabelText('Password'),
      'secret123',
    );

    await user.click(
      screen.getByRole('button', { name: 'Sign In' }),
    );

    expect(
      await screen.findByText('Messages destination'),
    ).toBeInTheDocument();
  });

  it('renders email and password fields', () => {
    renderPage();

    expect(
      screen.getByRole('heading', {
        name: 'Sign in to PetMingle',
      }),
    ).toBeInTheDocument();

    expect(
      screen.getByRole('textbox', { name: 'Email' }),
    ).toBeInTheDocument();

    expect(
      screen.getByLabelText('Password'),
    ).toBeInTheDocument();
  });

  it('validates the form before attempting sign in', async () => {
    const user = userEvent.setup();

    renderPage();

    await user.click(
      screen.getByRole('button', { name: 'Sign In' }),
    );

    expect(
      await screen.findByText('Enter your email address.'),
    ).toBeInTheDocument();

    expect(
      screen.getByText('Enter your password.'),
    ).toBeInTheDocument();

    expect(signIn).not.toHaveBeenCalled();
  });

  it('signs in and navigates to Discovery', async () => {
    const user = userEvent.setup();

    signIn.mockResolvedValue(undefined);

    renderPage();

    await user.type(
      screen.getByRole('textbox', { name: 'Email' }),
      'hamza@example.com',
    );

    await user.type(
      screen.getByLabelText('Password'),
      'secret123',
    );

    await user.click(
      screen.getByRole('button', { name: 'Sign In' }),
    );

    expect(signIn).toHaveBeenCalledWith({
      email: 'hamza@example.com',
      password: 'secret123',
    });

    expect(
      await screen.findByText('Discovery destination'),
    ).toBeInTheDocument();
  });

  it('shows a safe message for invalid credentials', async () => {
    const user = userEvent.setup();

    signIn.mockRejectedValue(
      new ApiError(
        'Login credentials are invalid.',
        401,
        {
          success: false,
          message: 'Login credentials are invalid.',
        },
      ),
    );

    renderPage();

    await user.type(
      screen.getByRole('textbox', { name: 'Email' }),
      'hamza@example.com',
    );

    await user.type(
      screen.getByLabelText('Password'),
      'wrong-password',
    );

    await user.click(
      screen.getByRole('button', { name: 'Sign In' }),
    );

    expect(
      await screen.findByRole('alert'),
    ).toHaveTextContent('Email or password is incorrect.');
  });
});