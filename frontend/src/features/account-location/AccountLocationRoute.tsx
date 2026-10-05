import { Outlet } from 'react-router';

import { AccountLocationProvider } from './AccountLocationProvider';

export function AccountLocationRoute() {
  return (
    <AccountLocationProvider>
      <Outlet />
    </AccountLocationProvider>
  );
}
