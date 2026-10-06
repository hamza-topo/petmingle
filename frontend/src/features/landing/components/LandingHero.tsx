import { ArrowRight } from 'lucide-react';
import { Link } from 'react-router';
import heroPhoto from '../../../assets/brand/meeting.webp';
import { CrossingPaths } from './CrossingPaths';

export function LandingHero() {
  return (
    <section className="landing-hero" aria-labelledby="hero-heading">
      <div className="hero-copy">
        <p className="landing-eyebrow">01 / Everyday encounters</p>
        <h1 id="hero-heading">Their paths{' '}<br />cross.{' '}<br />Yours do too.</h1>
        <p className="hero-description">New walking companions.<br />Neighbours who become friends.</p>
        <Link to="/discover" className="landing-primary">Find a connection <ArrowRight size={23} aria-hidden="true" /></Link>
        <p className="hero-footnote">For pets and their people.</p>
      </div>
      <figure className="hero-figure">
        <img className="hero-photo" src={heroPhoto} width={1536} height={1024} fetchPriority="high" alt="Two dogs and their people meeting on a sunny park path" />
        <figcaption>A walk can change everything.</figcaption>
      </figure>
      <CrossingPaths />
    </section>
  );
}
