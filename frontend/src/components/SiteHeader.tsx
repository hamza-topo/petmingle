import {
  ChevronDown,
  MessageCircle,
  PawPrint,
  Search,
} from 'lucide-react';
import { Link } from 'react-router';

import { SignOutButton } from '../auth/SignOutButton';
import { ActionLink } from './Action';
import { Avatar } from './Avatar';
import { PetMingleLogo } from './PetMingleLogo';
import { PrimaryNavigation } from './PrimaryNavigation';

type HeaderIdentity = {
  name: string;
  kind: 'account' | 'pet';
};

export function SiteHeader({
  identity,
}: {
  identity?: HeaderIdentity;
}) {
  const identityLabel =
    identity?.kind === 'pet'
      ? `${identity.name} pet menu — unavailable`
      : `${identity?.name ?? 'Account'} account — unavailable`;

  return (
    <header className="site-header">
      <PetMingleLogo />

      <PrimaryNavigation
        referenceHome={identity?.kind === 'pet'}
      />

      <div className="header-actions">
        <button
          type="button"
          className="search-button"
          aria-label="Search — unavailable in this preview"
          disabled
          title="Search is not available in this preview"
        >
          <Search size={22} aria-hidden="true" />
        </button>

        {identity ? (
          <>
            <Link
              to="/messages"
              className="own-messages-link"
            >
              <MessageCircle
                size={22}
                aria-hidden="true"
              />
              Messages
            </Link>

            <button
              type="button"
              className="own-pet-menu"
              disabled
              aria-label={identityLabel}
            >
              <Avatar name={identity.name} />
              <span>{identity.name}</span>
              <ChevronDown
                size={17}
                aria-hidden="true"
              />
            </button>

            <SignOutButton className="header-sign-out" />
          </>
        ) : (
          <>
            <ActionLink
              to="/signin"
              variant="secondary"
            >
              Sign In
            </ActionLink>

            <ActionLink
              to="/pet/create"
              variant="primary"
            >
              <PawPrint
                size={23}
                fill="currentColor"
                aria-hidden="true"
              />
              Get Started
            </ActionLink>
          </>
        )}
      </div>
    </header>
  );
}
