import { nalaIdentity } from '../../fixtures/petIdentity';
import { CalendarDays, ContactRound, Heart, MapPin, MessageCircle, Search, UsersRound } from 'lucide-react';
import type { LucideIcon } from 'lucide-react';
import type { FeaturedPet } from '../../components/PetCard';

interface LandingItem {
  title: string;
  description: string;
  icon: LucideIcon;
  tone: 'pink' | 'blue';
}

export const benefits: LandingItem[] = [
  { title: 'Make Friends', description: 'Connect with friendly\npets and people nearby.', icon: UsersRound, tone: 'pink' },
  { title: 'Explore Together', description: 'Find play dates and\npet-friendly spots.', icon: MapPin, tone: 'blue' },
  { title: 'Share the Joy', description: 'Celebrate special moments\nand real connections.', icon: Heart, tone: 'pink' },
];

export const processSteps: LandingItem[] = [
  { title: 'Create a profile', description: 'Show off their personality\nwith photos and a few details.', icon: ContactRound, tone: 'pink' },
  { title: 'Discover pets', description: 'Browse pets and people\nnear you.', icon: Search, tone: 'blue' },
  { title: 'Match & chat', description: 'Find great matches and\nstart friendly conversations.', icon: MessageCircle, tone: 'pink' },
  { title: 'Plan playdates', description: 'Meet up at parks, events\nand pet-friendly spots.', icon: CalendarDays, tone: 'blue' },
];

export const featuredPets: FeaturedPet[] = [
  { ...nalaIdentity, photo: { src: null, alt: 'Nala, a golden retriever', placeholder: 'Nala · photo placeholder' } },
  { id: 'mochi', name: 'Mochi', breed: 'Domestic Shorthair', photo: { src: null, alt: 'Mochi, a domestic shorthair cat', placeholder: 'Mochi · photo placeholder' } },
  { id: 'toby', name: 'Toby', breed: 'Pembroke Welsh Corgi', photo: { src: null, alt: 'Toby, a Pembroke Welsh corgi', placeholder: 'Toby · photo placeholder' } },
  { id: 'luna', name: 'Luna', breed: 'Cavapoo', photo: { src: null, alt: 'Luna, a cavapoo', placeholder: 'Luna · photo placeholder' } },
  { id: 'simba', name: 'Simba', breed: 'Ragdoll', photo: { src: null, alt: 'Simba, a ragdoll cat', placeholder: 'Simba · photo placeholder' } },
  { id: 'buddy', name: 'Buddy', breed: 'Border Collie', photo: { src: null, alt: 'Buddy, a border collie', placeholder: 'Buddy · photo placeholder' } },
];
