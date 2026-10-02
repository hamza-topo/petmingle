import { ChevronDown, MapPin, Search } from 'lucide-react';
import { PrimaryNavigation } from '../../../components/PrimaryNavigation';
import { NotificationButton } from '../../../components/NotificationButton';
import { PetMingleLogo } from '../../../components/PetMingleLogo';
import { Avatar } from '../../../components/Avatar';
import { messagingAccount } from '../messaging.fixtures';
export function MessagingHeader() {
  return <header className="messaging-header">
    <PetMingleLogo />
    <PrimaryNavigation variant="messaging" />
    <div className="messaging-utilities">
      <button type="button" className="chat-round-control" disabled aria-label="Search — unavailable"><Search size={24} /></button>
      <button type="button" className="chat-location" disabled><MapPin size={21} /><span>{messagingAccount.location}</span><ChevronDown size={16} /></button>
      <NotificationButton className="chat-round-control chat-notifications" dotClassName="notification-dot" size={23} />
      <button type="button" className="chat-account" disabled aria-label="Owner account — unavailable"><Avatar asset={messagingAccount.photo} /><ChevronDown size={20} /></button>
    </div>
  </header>;
}
