import {
  Heart,
  PawPrint,
  UsersRound,
  Zap,
} from 'lucide-react';
import type { LucideIcon } from 'lucide-react';

export type FilterKey =
  | 'species'
  | 'size'
  | 'energy'
  | 'personality';

export type FilterGroup = {
  key: FilterKey;
  label: string;
  icon: LucideIcon;
  options: readonly string[];
};

export const filterGroups: FilterGroup[] = [
  {
    key: 'species',
    label: 'Species',
    icon: PawPrint,
    options: ['All', 'Dogs', 'Cats', 'Other'],
  },
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
