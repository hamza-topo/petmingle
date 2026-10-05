import {
  Heart,
  UsersRound,
  Zap,
} from 'lucide-react';
import type { LucideIcon } from 'lucide-react';

export type UnsupportedFilterGroup = {
  key: 'size' | 'energy' | 'personality';
  label: string;
  icon: LucideIcon;
  options: readonly string[];
};

export const unsupportedFilterGroups: UnsupportedFilterGroup[] = [
  {
    key: 'size',
    label: 'Size (dogs)',
    icon: UsersRound,
    options: [
      'All',
      'Small',
      'Medium',
      'Large',
      'Extra Large',
    ],
  },
  {
    key: 'energy',
    label: 'Energy Level',
    icon: Zap,
    options: ['All', 'Low', 'Medium', 'High'],
  },
  {
    key: 'personality',
    label: 'Personality',
    icon: Heart,
    options: [
      'All',
      'Playful',
      'Calm',
      'Friendly',
      'Independent',
      'Adventurous',
    ],
  },
];
