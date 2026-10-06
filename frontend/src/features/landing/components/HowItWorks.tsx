import { Link } from 'react-router';

const steps = [
  { title: 'Create a profile', description: 'Photos, personality, and the things your pet loves.' },
  { title: 'Discover nearby pets', description: 'Find new companions around your neighbourhood.' },
  { title: 'Connect and say hello', description: 'When the interest is mutual, start a conversation.' },
];

export function HowItWorks() {
  return (
    <section id="how-it-works" aria-labelledby="how-heading" className="how-it-works">
      <div className="landing-section-heading">
        <h2 id="how-heading">How It Works</h2>
        <Link to="/pet/create" className="landing-text-link">Create your pet’s profile →</Link>
      </div>
      <ol className="process-steps">
        {steps.map(({ title, description }, index) => (
          <li key={title}>
            <span className="step-number" aria-hidden="true">0{index + 1}</span>
            <h3>{title}</h3>
            <p>{description}</p>
          </li>
        ))}
      </ol>
    </section>
  );
}
