import { Link, NavLink } from 'react-router';

export function PrimaryNavigation({ variant = 'public', referenceHome = false }: { variant?: 'public' | 'messaging'; referenceHome?: boolean }) {
  return <nav aria-label="Primary navigation" className={variant === 'public' ? 'primary-nav' : undefined}>
    <NavLink to="/" end data-reference-active={referenceHome || undefined}>Home</NavLink>
    <Link to="/discover">Explore</Link>
    {variant === 'messaging' ? <NavLink to="/messages">Match &amp; Chat</NavLink> : <Link to="/#how-it-works">How It Works</Link>}
    <button type="button" disabled title="Stories is not available">Stories</button>
    <button type="button" disabled title="Resources is not available">Resources</button>
  </nav>;
}
