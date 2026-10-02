import { ChevronDown, MessageCircle, PawPrint, Search } from 'lucide-react';
import { Link } from 'react-router';
import { ActionButton, ActionLink } from './Action';
import { PetMingleLogo } from './PetMingleLogo';
import { Avatar } from './Avatar';
import { PrimaryNavigation } from './PrimaryNavigation';
import type { ReferenceAsset } from '../assets/landingAssets';

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
          <button type="button" className="own-pet-menu" disabled aria-label={`${petIdentity.name} pet menu — unavailable`}><Avatar asset={petIdentity.photo} /><span>{petIdentity.name}</span><ChevronDown size={17} /></button>
        </> : <>
        <ActionButton variant="secondary" unavailableReason="Sign in is not available in this preview">Sign In</ActionButton>
        <ActionLink to="/pet/create" variant="primary">
          <PawPrint size={23} fill="currentColor" aria-hidden="true" />
          Get Started
        </ActionLink>
        </>}
      </div>
    </header>
  );
}
