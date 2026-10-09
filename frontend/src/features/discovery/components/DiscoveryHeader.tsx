import { useEffect, useRef, useState } from 'react';
import { ChevronDown } from 'lucide-react';
import { Link, NavLink } from 'react-router';

import { SignOutButton } from '../../../auth/SignOutButton';
import { useAuth } from '../../../auth/AuthProvider';
import { Avatar } from '../../../components/Avatar';
import { PetMingleLogo } from '../../../components/PetMingleLogo';

export function DiscoveryHeader() {
  const { user } = useAuth();
  const [open, setOpen] = useState(false);
  const shell = useRef<HTMLDivElement>(null);
  const trigger = useRef<HTMLButtonElement>(null);
  const name = user?.name ?? 'Mon compte';

  useEffect(() => {
    if (!open) return;
    function dismiss(event: PointerEvent) {
      if (!shell.current?.contains(event.target as Node)) setOpen(false);
    }
    document.addEventListener('pointerdown', dismiss);
    return () => document.removeEventListener('pointerdown', dismiss);
  }, [open]);

  return (
    <header className="discovery-header">
      <PetMingleLogo />
      <nav className="discovery-top-nav" aria-label="Navigation principale">
        <NavLink to="/discover" end>
          Découvrir
        </NavLink>
        <NavLink to="/matches">Rencontres</NavLink>
        <NavLink to="/messages">Messages</NavLink>
      </nav>
      <div
        className="discovery-account-shell"
        ref={shell}
        onKeyDown={(event) => {
          if (event.key === 'Escape') {
            setOpen(false);
            trigger.current?.focus();
          }
        }}
      >
        <button
          ref={trigger}
          className="discovery-account"
          type="button"
          aria-label={`Compte de ${name}`}
          aria-expanded={open}
          aria-controls={open ? 'discovery-account-options' : undefined}
          onClick={() => setOpen((current) => !current)}
        >
          <Avatar
            name={name
              .trim()
              .split(/\s+/)
              .slice(0, 2)
              .map((part) => part[0])
              .join('')
              .toUpperCase()}
            className="owner-avatar"
          />
          <ChevronDown size={18} aria-hidden="true" />
        </button>
        {open && (
          <div
            className="discovery-account-options"
            id="discovery-account-options"
          >
            <p>{name}</p>
            <Link to="/profile">Mon profil</Link>
            <Link to="/profile#petmingle-plus">PetMingle Plus</Link>
            <SignOutButton className="discovery-sign-out" />
          </div>
        )}
      </div>
    </header>
  );
}
