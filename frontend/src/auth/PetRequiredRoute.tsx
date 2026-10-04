import { Outlet } from 'react-router';

import { useAuth } from './AuthProvider';
import { ActionLink } from '../components/Action';

export function PetRequiredRoute() {
  const { pet } = useAuth();

  if (pet) {
    return <Outlet />;
  }

  return (
    <main className="auth-route-state">
      <div className="auth-route-message">
        <h1>Create your pet profile to continue</h1>

        <p role="status">
          You are signed in. Add your pet before using
          Discovery, Messaging, or Profile.
        </p>

        <ActionLink
          to="/pet/create"
          variant="primary"
        >
          Create pet profile
        </ActionLink>
      </div>
    </main>
  );
}
