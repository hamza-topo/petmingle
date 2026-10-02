import { Bell, ChevronDown, MapPin, Search } from 'lucide-react';
import { NavLink } from 'react-router';
import { PetMingleLogo } from '../../../components/PetMingleLogo';
import { ReferenceImage } from '../../../components/ReferenceImage';
import { messagingAccount } from '../messaging.fixtures';
export function MessagingHeader() {
  return <header className="messaging-header">
    <PetMingleLogo />
    <nav aria-label="Primary navigation">
      <NavLink to="/" end>Home</NavLink><NavLink to="/discover">Explore</NavLink><NavLink to="/messages">Match &amp; Chat</NavLink>
      <button type="button" disabled>Stories</button><button type="button" disabled>Resources</button>
    </nav>
    <div className="messaging-utilities">
      <button type="button" className="chat-round-control" disabled aria-label="Search — unavailable"><Search size={24} /></button>
      <button type="button" className="chat-location" disabled><MapPin size={21} /><span>{messagingAccount.location}</span><ChevronDown size={16} /></button>
      <button type="button" className="chat-round-control chat-notifications" disabled aria-label="Notifications — unavailable"><Bell size={23} /><span /></button>
      <button type="button" className="chat-account" disabled aria-label="Owner account — unavailable"><ReferenceImage asset={messagingAccount.photo} /><ChevronDown size={20} /></button>
    </div>
  </header>;
}
