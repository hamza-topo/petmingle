import type { ReactNode } from 'react';

interface SectionHeadingProps {
  id: string;
  children: ReactNode;
  description?: string;
  action?: ReactNode;
}

export function SectionHeading({ id, children, description, action }: SectionHeadingProps) {
  return (
    <div className="section-heading">
      <h2 id={id}>{children}</h2>
      {description && <p>{description}</p>}
      {action}
    </div>
  );
}
