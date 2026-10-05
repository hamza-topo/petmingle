import { ChevronDown } from 'lucide-react';

import { SignOutButton } from '../../auth/SignOutButton';
import { useAuth } from '../../auth/AuthProvider';
import { Avatar } from '../../components/Avatar';
import { PetMingleLogo } from '../../components/PetMingleLogo';
import { PrimaryNavigation } from '../../components/PrimaryNavigation';

export function MatchesHeader() {
  const { user } = useAuth();
  const accountName = user?.name ?? 'Account';

  return (
    <header className="site-header matches-header">
      <PetMingleLogo />

      <PrimaryNavigation variant="messaging" />

      <div className="header-actions matches-header-actions">
        <button
          className="matches-account"
          type="button"
          disabled
          aria-label={`${accountName} account — unavailable`}
        >
          <Avatar name={accountName} />
          <span>{accountName}</span>
          <ChevronDown size={18} aria-hidden="true" />
        </button>

        <SignOutButton className="matches-sign-out" />
      </div>
    </header>
  );
}
