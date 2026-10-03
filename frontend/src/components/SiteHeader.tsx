import { ChevronDown, MessageCircle, PawPrint, Search } from 'lucide-react';
import { Link } from 'react-router';
import { PetMingleLogo } from './PetMingleLogo';
import { Avatar } from './Avatar';
import { PrimaryNavigation } from './PrimaryNavigation';
import type { ReferenceAsset } from '../assets/landingAssets';
import { ActionLink } from './Action';
import { SignOutButton } from '../auth/SignOutButton';

export function SiteHeader({ petIdentity }: { petIdentity?: { name: string; photo: ReferenceAsset } }) {
  return (
    <header className="site-header">
      <PetMingleLogo />
      <PrimaryNavigation referenceHome={Boolean(petIdentity)} />
      <div className="header-actions">
        <button type="button" className="search-button" aria-label="Search — unavailable in this preview" disabled title="Search is not available in this preview">
          <Search size={22} aria-hidden="true" />
        </button>
        {petIdentity ? <>
          <Link to="/messages" className="own-messages-link"><MessageCircle size={22} aria-hidden="true" />Messages</Link>
          <button type="button" className="own-pet-menu" disabled aria-label={`${petIdentity.name} pet menu — unavailable`}>
            <Avatar asset={petIdentity.photo} /><span>{petIdentity.name}</span>
            <ChevronDown size={17} />
          </button>
            <SignOutButton className="header-sign-out" />
        </> : <>
          <ActionLink to="/signin" variant="secondary">
            Sign In
          </ActionLink>
          <ActionLink to="/pet/create" variant="primary">
            <PawPrint size={23} fill="currentColor" aria-hidden="true" />
            Get Started
          </ActionLink>
        </>}

      </div>
    </header>
  );
}
