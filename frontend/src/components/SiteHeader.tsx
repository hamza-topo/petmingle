import { ChevronDown, MessageCircle, PawPrint, Search } from 'lucide-react';
import { Link, NavLink, useLocation } from 'react-router';
import { ActionButton } from './Action';
import { PetMingleLogo } from './PetMingleLogo';
import { ReferenceImage } from './ReferenceImage';
import type { ReferenceAsset } from '../assets/landingAssets';

export function SiteHeader({ petIdentity }: { petIdentity?: { name: string; photo: ReferenceAsset } }) {
  const { pathname } = useLocation();
  return (
    <header className="site-header">
      <PetMingleLogo />
      <nav aria-label="Primary navigation" className="primary-nav">
        <NavLink to="/" end data-reference-active={petIdentity ? true : undefined}>Home</NavLink>
        <Link to="/discover">Explore</Link>
        <a href={pathname === "/" ? "#how-it-works" : "/#how-it-works"}>How It Works</a>
        <button type="button" disabled title="Stories is not available in this preview">Stories</button>
        <button type="button" disabled title="Resources is not available in this preview">Resources</button>
      </nav>
      <div className="header-actions">
        <button type="button" className="search-button" aria-label="Search — unavailable in this preview" disabled title="Search is not available in this preview">
          <Search size={22} aria-hidden="true" />
        </button>
        {petIdentity ? <>
          <Link to="/messages" className="own-messages-link"><MessageCircle size={22} aria-hidden="true" />Messages</Link>
          <button type="button" className="own-pet-menu" disabled aria-label={`${petIdentity.name} pet menu — unavailable`}><ReferenceImage asset={petIdentity.photo} /><span>{petIdentity.name}</span><ChevronDown size={17} /></button>
        </> : <>
        <ActionButton variant="secondary" unavailableReason="Sign in is not available in this preview">Sign In</ActionButton>
        <ActionButton unavailableReason="Signup is not available in this preview">
          <PawPrint size={23} fill="currentColor" aria-hidden="true" />
          Get Started
        </ActionButton>
        </>}
      </div>
    </header>
  );
}
