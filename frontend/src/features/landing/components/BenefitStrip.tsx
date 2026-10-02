import clsx from 'clsx';
import { benefits } from '../landing.fixtures';

export function BenefitStrip() {
  return (
    <section aria-label="Why PetMingle" className="benefit-strip">
      {benefits.map(({ title, description, icon: Icon, tone }) => (
        <div key={title} className={clsx('benefit', `tone-${tone}`)}>
          <span className="benefit-icon"><Icon size={43} strokeWidth={2.4} aria-hidden="true" /></span>
          <div>
            <h2>{title}</h2>
            <p>{description}</p>
          </div>
        </div>
      ))}
    </section>
  );
}
