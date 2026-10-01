import clsx from 'clsx';
import { SectionHeading } from '../../../components/SectionHeading';
import { processSteps } from '../landing.fixtures';

export function HowItWorks() {
  return (
    <section id="how-it-works" aria-labelledby="how-heading" className="how-it-works">
      <SectionHeading id="how-heading" description="Four simple steps to a happier, more connected pet community.">
        How It Works
      </SectionHeading>
      <ol className="process-steps">
        {processSteps.map(({ title, description, icon: Icon, tone }, index) => (
          <li key={title} className={clsx('process-step', `tone-${tone}`)}>
            <div className="step-marker">
              <span className="step-number" aria-hidden="true">{index + 1}</span>
              <span className="step-icon"><Icon size={29} strokeWidth={2.4} aria-hidden="true" /></span>
              {index < processSteps.length - 1 && <span className="step-connector" aria-hidden="true">→</span>}
            </div>
            <div className="step-copy">
              <h3>{title}</h3>
              <p>{description}</p>
            </div>
          </li>
        ))}
      </ol>
    </section>
  );
}
