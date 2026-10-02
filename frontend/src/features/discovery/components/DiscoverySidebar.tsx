import { Binoculars, Heart, MessageCircle, Star, UserRound } from 'lucide-react';
import { Link, NavLink } from 'react-router';
import { discoveryContext } from '../discovery.fixtures';
const navigation = [
  { label: 'Matches', description: 'Pets & people you like', icon: Heart, count: discoveryContext.matches, to: null },
  { label: 'Messages', description: 'Start new conversations', icon: MessageCircle, count: discoveryContext.messages, to: '/messages' },
  { label: 'Profile', description: 'Your pets & preferences', icon: UserRound, to: '/profile' },
  { label: 'PetMingle Plus', description: 'Unlock more connections', icon: Star, to: '/profile#petmingle-plus' },
];
export function DiscoverySidebar() {
  return <nav className="discovery-sidebar" aria-label="PetMingle navigation">
    <NavLink to="/discover" end className="discovery-nav-item"><Binoculars size={35} aria-hidden="true" /><span><strong>Discover</strong><small>Find amazing pets</small></span></NavLink>
    {navigation.map(({ label, description, icon: Icon, count, to }) => {
      const content = <><Icon size={35} aria-hidden="true" /><span><strong>{label}</strong><small>{description}</small></span>{count && <span className="navigation-count" aria-label={`${count} ${label === 'Messages' ? 'unread messages' : 'matches'}`}>{count}</span>}</>;
      return to ? <Link key={label} className="discovery-nav-item" to={to}>{content}</Link> : <button key={label} className="discovery-nav-item" type="button" disabled title="Matches is not available">{content}</button>;
    })}
  </nav>;
}
