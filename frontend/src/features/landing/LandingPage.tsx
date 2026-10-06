import { PetMingleLogo } from '../../components/PetMingleLogo';
import { LandingHeader } from './components/LandingHeader';
import { HowItWorks } from './components/HowItWorks';
import { LandingHero } from './components/LandingHero';
import { NearbyStory } from './components/NearbyStory';

export function LandingPage() {
  return (
    <div className="landing-page">
      <a className="skip-link" href="#main-content">Skip to content</a>
      <LandingHeader />
      <main tabIndex={-1} id="main-content">
        <LandingHero />
        <NearbyStory />
        <HowItWorks />
      </main>
      <footer className="landing-footer">
        <PetMingleLogo />
        <p>For pets. For their people. For everyday connections.</p>
      </footer>
    </div>
  );
}
