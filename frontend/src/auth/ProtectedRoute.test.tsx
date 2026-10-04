import { render, screen } from '@testing-library/react';
import {
  MemoryRouter,
  Route,
  Routes,
  useLocation,
} from 'react-router';
import {
  beforeEach,
  describe,
  expect,
  it,
  vi,
} from 'vitest';
import { useAuth } from './AuthProvider';
import { ProtectedRoute } from './ProtectedRoute';

vi.mock('./AuthProvider', () => ({
  useAuth: vi.fn(),
}));

const mockedUseAuth = vi.mocked(useAuth);

function SignInProbe() {
  const location = useLocation();

  const from =
    typeof location.state === 'object'
    && location.state !== null
    && 'from' in location.state
      ? String(location.state.from)
      : 'none';

  return <div>Sign in destination: {from}</div>;
}

function renderProtectedRoute() {
  return render(
    <MemoryRouter initialEntries={['/messages']}>
      <Routes>
        <Route element={<ProtectedRoute />}>
          <Route
            path="/messages"
            element={<div>Protected messages</div>}
          />
        </Route>

        <Route path="/signin" element={<SignInProbe />} />
      </Routes>
    </MemoryRouter>,
  );
}

describe('ProtectedRoute', () => {
  beforeEach(() => {
    mockedUseAuth.mockReset();
  });

  it('renders protected content for an authenticated user', () => {
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
      refreshIdentity: vi.fn(),
    });

    renderProtectedRoute();

    expect(
      screen.getByText('Protected messages'),
    ).toBeInTheDocument();
  });

  it('redirects an unauthenticated user and preserves the destination', () => {
    mockedUseAuth.mockReturnValue({
      status: 'unauthenticated',
      user: null,
      pet: null,
      error: null,
      isAuthenticated: false,
      signIn: vi.fn(),
      signOut: vi.fn(),
    refreshIdentity: vi.fn(),
      refreshIdentity: vi.fn(),
    });

    renderProtectedRoute();

    expect(
      screen.getByText('Sign in destination: /messages'),
    ).toBeInTheDocument();
  });

  it('waits while an existing session is being restored', () => {
    mockedUseAuth.mockReturnValue({
      status: 'loading',
      user: null,
      pet: null,
      error: null,
      isAuthenticated: false,
      signIn: vi.fn(),
      signOut: vi.fn(),
    refreshIdentity: vi.fn(),
      refreshIdentity: vi.fn(),
    });

    renderProtectedRoute();

    expect(
      screen.getByRole('status'),
    ).toHaveTextContent('Restoring your session');

    expect(
      screen.queryByText('Protected messages'),
    ).not.toBeInTheDocument();
  });

  it('does not discard the route when session verification has a network error', () => {
    mockedUseAuth.mockReturnValue({
      status: 'error',
      user: null,
      pet: null,
      error: 'Failed to fetch',
      isAuthenticated: false,
      signIn: vi.fn(),
      signOut: vi.fn(),
    refreshIdentity: vi.fn(),
      refreshIdentity: vi.fn(),
    });

    renderProtectedRoute();

    expect(
      screen.getByRole('alert'),
    ).toHaveTextContent(
      'Unable to reach PetMingle. Refresh the page to try again.',
    );

    expect(
      screen.queryByRole('button', {
        name: 'Try again',
      }),
    ).not.toBeInTheDocument();

    expect(
      screen.queryByText(/Sign in destination/),
    ).not.toBeInTheDocument();
  });
});