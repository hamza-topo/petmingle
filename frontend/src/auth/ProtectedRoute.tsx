import {
  Navigate,
  Outlet,
  useLocation,
} from 'react-router';

import { ApiState } from '../components/ApiState';
import { useAuth } from './AuthProvider';

export function ProtectedRoute() {
  const auth = useAuth();
  const location = useLocation();

  if (auth.status === 'loading') {
    return (
      <main className="auth-route-state">
        <ApiState
          kind="loading"
          message="Restoring your session…"
        />
      </main>
    );
  }

  if (auth.status === 'error') {
    return (
      <main className="auth-route-state">
        <ApiState
          kind="error"
          title="We couldn’t verify your session"
          message="Unable to reach PetMingle. Refresh the page to try again."
        />
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
