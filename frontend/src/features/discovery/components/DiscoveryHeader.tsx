import {
  ChevronDown,
  Search,
} from 'lucide-react';

import { SignOutButton } from '../../../auth/SignOutButton';
import { useAuth } from '../../../auth/AuthProvider';
import { Avatar } from '../../../components/Avatar';
import { NotificationButton } from '../../../components/NotificationButton';
import { PetMingleLogo } from '../../../components/PetMingleLogo';
import { AccountLocationControl } from '../../account-location/AccountLocationControl';

export function DiscoveryHeader() {
  const { user } = useAuth();
  const accountName = user?.name ?? 'Account';

  return (
    <header className="discovery-header">
      <PetMingleLogo />

      <AccountLocationControl />

      <label className="discovery-search">
        <Search size={23} aria-hidden="true" />
        <span className="sr-only">
          Search pets, people, or locations
        </span>
        <input
          type="search"
          aria-label="Search pets, people, or locations"
          disabled
          placeholder="Search pets, people, or locations..."
          aria-describedby="search-preview-note"
        />
        <span
          id="search-preview-note"
          className="sr-only"
        >
          Search is unavailable.
        </span>
      </label>

      <NotificationButton
        className="discovery-notifications"
        dotClassName="notification-dot"
        size={26}
      />

      <button
        className="discovery-account"
        type="button"
        disabled
        aria-label={`${accountName} account — unavailable in this preview`}
      >
        <Avatar
          name={accountName}
          className="owner-avatar"
        />
        <span>{accountName}</span>
        <ChevronDown size={18} aria-hidden="true" />
      </button>

      <SignOutButton className="discovery-sign-out" />
    </header>
  );
}
