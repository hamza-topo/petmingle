import { useState } from 'react';
import { Bell, ChevronDown, MapPin, Search } from 'lucide-react';
import { PetMingleLogo } from '../../../components/PetMingleLogo';
import { ReferenceImage } from '../../../components/ReferenceImage';
import { discoveryContext } from '../discovery.fixtures';

export function DiscoveryHeader() {
  const [search, setSearch] = useState('');
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
        <input type="search" aria-label="Search pets, people, or locations" value={search} onChange={(event) => setSearch(event.target.value)} placeholder="Search pets, people, or locations..." aria-describedby="search-preview-note" />
        <span id="search-preview-note" className="sr-only">Preview text only. Live search is not connected.</span>
      </label>
      <button className="discovery-notifications" type="button" disabled aria-label="Notifications — unread notifications, unavailable in this preview">
        <Bell size={26} aria-hidden="true" /><span className="notification-dot" aria-hidden="true" />
      </button>
      <button className="discovery-account" type="button" disabled aria-label={`${discoveryContext.owner.name} account — unavailable in this preview`}>
        <ReferenceImage asset={discoveryContext.owner.photo} className="owner-avatar" />
        <span>{discoveryContext.owner.name}</span>
        <ChevronDown size={18} aria-hidden="true" />
      </button>
    </header>
  );
}
