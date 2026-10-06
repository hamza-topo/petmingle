import { Link } from 'react-router';
import { PetMingleLogo } from '../../../components/PetMingleLogo';

export function LandingHeader() {
  return (
    <header className="landing-header">
      <PetMingleLogo />
      <nav aria-label="Primary navigation" className="landing-nav">
        <Link to="/discover">Explore</Link>
        <Link to="/#how-it-works">How It Works</Link>
      </nav>
      <div className="landing-header-actions">
        <Link to="/signin" className="landing-sign-in">Sign In</Link>
        <Link to="/pet/create" className="landing-create">Create a profile</Link>
      </div>
    </header>
  );
}
