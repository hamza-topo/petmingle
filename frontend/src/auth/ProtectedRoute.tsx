import { Navigate, Outlet, useLocation } from 'react-router';
import { useAuth } from './AuthProvider';

export function ProtectedRoute() {
  const auth = useAuth();
  const location = useLocation();

  if (auth.status === 'loading') {
    return (
      <main className="auth-route-state">
        <p role="status">Restoring your session…</p>
      </main>
    );
  }

  if (auth.status === 'error') {
    return (
      <main className="auth-route-state">
        <div className="auth-route-message">
          <h1>We couldn’t verify your session</h1>
          <p role="alert">
            PetMingle could not reach the server. Refresh the page and try
            again.
          </p>
        </div>
      </main>
    );
  }

  if (!auth.isAuthenticated) {
    return (
      <Navigate
        to="/signin"
        replace
        state={{
          from: `${location.pathname}${location.search}${location.hash}`,
        }}
      />
    );
  }

  return <Outlet />;
}