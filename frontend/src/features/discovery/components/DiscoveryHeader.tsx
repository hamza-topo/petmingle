import { ChevronDown, MapPin, Search } from 'lucide-react';
import { PetMingleLogo } from '../../../components/PetMingleLogo';
import { Avatar } from '../../../components/Avatar';
import { NotificationButton } from '../../../components/NotificationButton';
import { discoveryContext } from '../discovery.fixtures';
import { SignOutButton } from '../../../auth/SignOutButton';

export function DiscoveryHeader() {
  return (
    <header className="discovery-header">
      <PetMingleLogo />
      <button className="discovery-location" type="button" disabled title="Location selection is unavailable in this preview">
        <MapPin size={22} aria-hidden="true" />
        {discoveryContext.location}
        <ChevronDown size={18} aria-hidden="true" />
      </button>
      <label className="discovery-search">
        <Search size={23} aria-hidden="true" />
        <span className="sr-only">Search pets, people, or locations</span>
        <input type="search" aria-label="Search pets, people, or locations" disabled placeholder="Search pets, people, or locations..." aria-describedby="search-preview-note" />
        <span id="search-preview-note" className="sr-only">Search is unavailable.</span>
      </label>
      <NotificationButton className="discovery-notifications" dotClassName="notification-dot" size={26} />
      <button className="discovery-account" type="button" disabled aria-label={`${discoveryContext.owner.name} account — unavailable in this preview`}>
        <Avatar asset={discoveryContext.owner.photo} className="owner-avatar" />
        <span>{discoveryContext.owner.name}</span>
        <ChevronDown size={18} aria-hidden="true" />
      </button>
      <SignOutButton className="discovery-sign-out" />
    </header>
  );
}
