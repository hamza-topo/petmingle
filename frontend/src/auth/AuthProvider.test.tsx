import { render, screen, waitFor } from '@testing-library/react';
import { beforeEach, describe, expect, it, vi } from 'vitest';
import { ApiError } from '../api/errors';
import {
  authenticatedIdentityRequest,
  signInRequest,
  signOutRequest,
} from './auth.api';
import { AuthProvider, useAuth } from './AuthProvider';
import userEvent from '@testing-library/user-event';

vi.mock('./auth.api', () => ({
  authenticatedIdentityRequest: vi.fn(),
  signInRequest: vi.fn(),
  signOutRequest: vi.fn(),
}));

const mockedIdentityRequest = vi.mocked(
  authenticatedIdentityRequest,
);

const mockedSignInRequest = vi.mocked(signInRequest);
const mockedSignOutRequest = vi.mocked(signOutRequest);

function AuthProbe() {
  const auth = useAuth();

  return (
    <>
      <span data-testid="status">{auth.status}</span>
      <span data-testid="user">
        {auth.user?.email ?? 'none'}
      </span>
      <span data-testid="pet">
        {auth.pet?.name ?? 'none'}
      </span>
    </>
  );
}

function RefreshIdentityProbe() {
  const auth = useAuth();

  return (
    <>
      <span data-testid="pet">
        {auth.pet?.name ?? 'none'}
      </span>

      <button
        type="button"
        onClick={() => void auth.refreshIdentity()}
      >
        Refresh identity
      </button>
    </>
  );
}

function SignOutProbe() {
  const auth = useAuth();

  return (
    <>
      <span data-testid="status">{auth.status}</span>

      <button
        type="button"
        onClick={() => void auth.signOut()}
      >
        Sign out
      </button>
    </>
  );
}

function renderProvider() {
  return render(
    <AuthProvider>
      <AuthProbe />
    </AuthProvider>,
  );
}

describe('AuthProvider', () => {
  beforeEach(() => {
    window.localStorage.clear();

    mockedIdentityRequest.mockReset();
    mockedSignInRequest.mockReset();
    mockedSignOutRequest.mockReset();
  });

  it('starts unauthenticated when no token exists', async () => {
    renderProvider();

    await waitFor(() => {
      expect(screen.getByTestId('status')).toHaveTextContent(
        'unauthenticated',
      );
    });

    expect(mockedIdentityRequest).not.toHaveBeenCalled();
    expect(screen.getByTestId('user')).toHaveTextContent('none');
    expect(screen.getByTestId('pet')).toHaveTextContent('none');
  });

  it('restores authenticated identity from an existing token', async () => {
    window.localStorage.setItem(
      'petmingle.auth.token',
      'existing-token',
    );

    mockedIdentityRequest.mockResolvedValue({
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
    });

    renderProvider();

    await waitFor(() => {
      expect(screen.getByTestId('status')).toHaveTextContent(
        'authenticated',
      );
    });

    expect(mockedIdentityRequest).toHaveBeenCalledWith(
      'existing-token',
    );

    expect(screen.getByTestId('user')).toHaveTextContent(
      'hamza@example.com',
    );

    expect(screen.getByTestId('pet')).toHaveTextContent('Nala');
  });

  it('clears a rejected token and becomes unauthenticated', async () => {
    window.localStorage.setItem(
      'petmingle.auth.token',
      'revoked-token',
    );

    mockedIdentityRequest.mockRejectedValue(
      new ApiError(
        'Unauthenticated.',
        401,
        {
          success: false,
          message: 'Unauthenticated.',
        },
      ),
    );

    renderProvider();

    await waitFor(() => {
      expect(screen.getByTestId('status')).toHaveTextContent(
        'unauthenticated',
      );
    });

    expect(
      window.localStorage.getItem('petmingle.auth.token'),
    ).toBeNull();

    expect(screen.getByTestId('user')).toHaveTextContent('none');
    expect(screen.getByTestId('pet')).toHaveTextContent('none');
  });

  it('keeps the token when identity restoration fails temporarily', async () => {
    window.localStorage.setItem(
      'petmingle.auth.token',
      'valid-token',
    );

    mockedIdentityRequest.mockRejectedValue(
      new TypeError('Failed to fetch'),
    );

    renderProvider();

    await waitFor(() => {
      expect(screen.getByTestId('status')).toHaveTextContent(
        'error',
      );
    });

    expect(
      window.localStorage.getItem('petmingle.auth.token'),
    ).toBe('valid-token');
  });

  it('refreshes the current pet after a successful creation', async () => {
    const user = userEvent.setup();

    window.localStorage.setItem(
      'petmingle.auth.token',
      'active-token',
    );

    mockedIdentityRequest
      .mockResolvedValueOnce({
        user: {
          id: 10,
          name: 'Hamza',
          email: 'hamza@example.com',
        },
        pet: null,
      })
      .mockResolvedValueOnce({
        user: {
          id: 10,
          name: 'Hamza',
          email: 'hamza@example.com',
        },
        pet: {
          id: 55,
          user_id: 10,
          name: 'Milo',
        },
      });

    render(
      <AuthProvider>
        <RefreshIdentityProbe />
      </AuthProvider>,
    );

    await waitFor(() => {
      expect(
        screen.getByTestId('pet'),
      ).toHaveTextContent('none');
    });

    await user.click(
      screen.getByRole('button', {
        name: 'Refresh identity',
      }),
    );

    await waitFor(() => {
      expect(
        screen.getByTestId('pet'),
      ).toHaveTextContent('Milo');
    });

    expect(mockedIdentityRequest).toHaveBeenCalledTimes(2);
    expect(mockedIdentityRequest).toHaveBeenLastCalledWith(
      'active-token',
    );
  });

  it('revokes the active token and clears local authentication on sign out', async () => {
    const user = userEvent.setup();

    window.localStorage.setItem(
      'petmingle.auth.token',
      'active-token',
    );

    mockedIdentityRequest.mockResolvedValue({
      user: {
        id: 10,
        name: 'Hamza',
        email: 'hamza@example.com',
      },
      pet: null,
    });

    mockedSignOutRequest.mockResolvedValue(undefined);

    render(
      <AuthProvider>
        <SignOutProbe />
      </AuthProvider>,
    );

    await waitFor(() => {
      expect(screen.getByTestId('status')).toHaveTextContent(
        'authenticated',
      );
    });

    await user.click(
      screen.getByRole('button', { name: 'Sign out' }),
    );

    await waitFor(() => {
      expect(screen.getByTestId('status')).toHaveTextContent(
        'unauthenticated',
      );
    });

    expect(mockedSignOutRequest).toHaveBeenCalledWith(
      'active-token',
    );

    expect(
      window.localStorage.getItem('petmingle.auth.token'),
    ).toBeNull();
  });
});