import { ArrowRight } from 'lucide-react';
import { Link } from 'react-router';

export function NearbyStory() {
  return (
    <section className="nearby-story" aria-labelledby="nearby-heading">
      <div>
        <p className="landing-eyebrow">Around the corner / A whole new connection</p>
        <h2 id="nearby-heading">It all starts<br />close to home.</h2>
      </div>
      <div className="nearby-story-copy">
        <p>A familiar path. A new walking companion. Discover nearby pets, connect when the interest is mutual, and let a first hello become your next walk together.</p>
        <Link to="/discover" className="landing-text-link">Explore nearby pets <ArrowRight size={20} aria-hidden="true" /></Link>
      </div>
    </section>
  );
}
