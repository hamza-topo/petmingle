import type { LucideIcon } from 'lucide-react';
import clsx from 'clsx';

export interface PetTrait {
  label: string;
  tone: 'pink' | 'blue' | 'teal' | 'purple';
  icon?: LucideIcon;
}

export function PetTraitBadge({ trait }: { trait: PetTrait }) {
  const Icon = trait.icon;
  return (
    <span className={clsx('pet-trait', `pet-trait--${trait.tone}`)}>
      {Icon && <Icon size={16} aria-hidden="true" />}
      {trait.label}
    </span>
  );
}
