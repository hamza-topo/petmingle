import { SiteHeader } from '../../components/SiteHeader';
import { BenefitStrip } from './components/BenefitStrip';
import { FeaturedPets } from './components/FeaturedPets';
import { HowItWorks } from './components/HowItWorks';
import { JoinCommunityBanner } from './components/JoinCommunityBanner';
import { LandingHero } from './components/LandingHero';

export function LandingPage() {
  return (
    <div className="landing-page">
      <a className="skip-link" href="#main-content">Skip to content</a>
      <SiteHeader />
      <main tabIndex={-1} id="main-content">
        <LandingHero />
        <div className="landing-content">
          <BenefitStrip />
          <div className="content-inset">
            <HowItWorks />
            <FeaturedPets />
          </div>
          <JoinCommunityBanner />
        </div>
      </main>
    </div>
  );
}
