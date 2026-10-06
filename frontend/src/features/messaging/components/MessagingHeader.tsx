import {
  ChevronDown,
  MapPin,
  Search,
} from 'lucide-react';

import { SignOutButton } from '../../../auth/SignOutButton';
import { useAuth } from '../../../auth/AuthProvider';
import { Avatar } from '../../../components/Avatar';
import { NotificationButton } from '../../../components/NotificationButton';
import { PetMingleLogo } from '../../../components/PetMingleLogo';
import { PrimaryNavigation } from '../../../components/PrimaryNavigation';

export function MessagingHeader() {
  const { user } = useAuth();
  const accountName = user?.name ?? 'Account';

  return (
    <header className="messaging-header">
      <PetMingleLogo />
      <PrimaryNavigation variant="messaging" />

      <div className="messaging-utilities">
        <button
          type="button"
          className="chat-round-control"
          disabled
          aria-label="Search — unavailable"
        >
          <Search size={24} aria-hidden="true" />
        </button>

        <button
          type="button"
          className="chat-location"
          disabled
        >
          <MapPin size={21} aria-hidden="true" />
          <span>Location</span>
          <ChevronDown size={16} aria-hidden="true" />
        </button>

        <NotificationButton
          className="chat-round-control chat-notifications"
          dotClassName="notification-dot"
          size={23}
        />

        <button
          type="button"
          className="chat-account"
          disabled
          aria-label={`${accountName} account — unavailable`}
        >
          <Avatar name={accountName} />
          <ChevronDown size={20} aria-hidden="true" />
        </button>
      </div>

      <SignOutButton className="messaging-sign-out" />
    </header>
  );
}
