import { landingAssets } from '../../../assets/landingAssets';
import { Link } from 'react-router';
import { ReferenceImage } from '../../../components/ReferenceImage';
import { GetStartedButton } from './GetStartedButton';

export function LandingHero() {
  return (
    <section className="landing-hero" aria-labelledby="hero-heading">
      <ReferenceImage asset={landingAssets.hero} className="hero-artwork" />
      <div className="hero-copy">
        <h1 id="hero-heading">Find their <span className="text-brand-pink">people</span></h1>
        <p>A friendly place for pets and<br />the people who love them.</p>
        <div className="hero-actions">
          <GetStartedButton />
          <Link to="/discover" className="action action--secondary">Explore Pets</Link>
        </div>
      </div>
    </section>
  );
}
