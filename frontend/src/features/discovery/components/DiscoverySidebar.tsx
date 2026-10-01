import { Binoculars, Heart, MessageCircle, Star, UserRound } from 'lucide-react';
import { NavLink } from 'react-router';
import { discoveryContext } from '../discovery.fixtures';

const navigation = [
  { label: 'Matches', description: 'Pets & people you like', icon: Heart, count: discoveryContext.matches },
  { label: 'Messages', description: 'Start new conversations', icon: MessageCircle, count: discoveryContext.messages },
  { label: 'Profile', description: 'Your pets & preferences', icon: UserRound },
  { label: 'PetMingle Plus', description: 'Unlock more connections', icon: Star },
];

export function DiscoverySidebar() {
  return (
    <nav className="discovery-sidebar" aria-label="PetMingle navigation">
      <NavLink to="/discover" end className="discovery-nav-item">
        <Binoculars size={35} aria-hidden="true" />
        <span><strong>Discover</strong><small>Find amazing pets</small></span>
      </NavLink>
      {navigation.map(({ label, description, icon: Icon, count }) => (
        <button key={label} className="discovery-nav-item" type="button" disabled title={`${label} is unavailable in this preview`}>
          <Icon size={35} aria-hidden="true" />
          <span><strong>{label}</strong><small>{description}</small></span>
          {count && <span className="navigation-count" aria-label={`${count} unread`}>{count}</span>}
        </button>
      ))}
    </nav>
  );
}
