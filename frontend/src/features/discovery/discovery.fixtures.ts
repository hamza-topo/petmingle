import { nalaIdentity } from '../../fixtures/petIdentity';
import { Cat, Heart, PawPrint, Shield, Trees, UsersRound, Zap } from 'lucide-react';
import type { LucideIcon } from 'lucide-react';
import type { ReferenceAsset } from '../../assets/landingAssets';
import type { NearbyPet } from '../../components/PetCard';
import type { PetTrait } from '../../components/PetTraitBadge';

interface FeaturedDiscoveryPet {
  id: string;
  name: string;
  breed: string;
  ageYears: number;
  distanceMiles: number;
  location: string;
  verified: boolean;
  description: string;
  photo: ReferenceAsset;
  photoCount: number;
  traits: PetTrait[];
  companion: { name: string; breed: string; ageYears: number; photo: ReferenceAsset };
}

export const discoveryContext: {
  location: string;
  petCount: number;
  owner: { name: string; photo: ReferenceAsset };
  matches: number;
  messages: number;
} = {
  location: 'San Francisco, CA',
  petCount: 127,
  owner: { name: 'Sarah', photo: { src: null, alt: 'Sarah’s profile photo', placeholder: 'Sarah' } },
  matches: 12,
  messages: 3,
};

export const featuredDiscoveryPet: FeaturedDiscoveryPet = {
  ...nalaIdentity,
  distanceMiles: 1.2, location: discoveryContext.location, verified: true,
  description: "Nala is a happy, affectionate golden who loves making new friends — both furry and human! She’s always up for an adventure, whether it’s a hike in the hills or a day at the dog park. Her best friend Mochi (the kitty!) goes everywhere with her. They’re a package deal.",
  photo: { src: null, alt: 'Nala the golden retriever with Mochi the cat', placeholder: 'Nala & Mochi · photo placeholder' },
  photoCount: 5,
  traits: [
    { label: 'Playful', tone: 'pink', icon: PawPrint },
    { label: 'Friendly', tone: 'blue', icon: PawPrint },
    { label: 'Outdoorsy', tone: 'teal', icon: Trees },
    { label: 'Good with Cats', tone: 'teal', icon: Cat },
    { label: 'Loves People', tone: 'purple', icon: Shield },
    { label: 'Adventurous', tone: 'blue' },
  ],
  companion: { name: 'Mochi', breed: 'Domestic Shorthair', ageYears: 2,
    photo: { src: null, alt: 'Mochi the domestic shorthair cat', placeholder: 'Mochi' } },
};

export const nearbyPets: NearbyPet[] = [
  { id: 'toby', name: 'Toby', breed: 'Pembroke Welsh Corgi', distanceMiles: 1.8,
    photo: { src: null, alt: 'Toby, a Pembroke Welsh corgi', placeholder: 'Toby · photo placeholder' },
    traits: [{ label: 'Playful', tone: 'pink' }, { label: 'Friendly', tone: 'blue' }] },
  { id: 'luna', name: 'Luna', breed: 'Cavapoo', distanceMiles: 2.1,
    photo: { src: null, alt: 'Luna, a cavapoo', placeholder: 'Luna · photo placeholder' },
    traits: [{ label: 'Affectionate', tone: 'teal' }, { label: 'Social', tone: 'purple' }] },
  { id: 'simba', name: 'Simba', breed: 'Ragdoll', distanceMiles: 2.3,
    photo: { src: null, alt: 'Simba, a ragdoll cat', placeholder: 'Simba · photo placeholder' },
    traits: [{ label: 'Calm', tone: 'purple' }, { label: 'Gentle', tone: 'teal' }] },
  { id: 'buddy', name: 'Buddy', breed: 'Border Collie', distanceMiles: 2.6,
    photo: { src: null, alt: 'Buddy, a border collie', placeholder: 'Buddy · photo placeholder' },
    traits: [{ label: 'Energetic', tone: 'pink' }, { label: 'Smart', tone: 'blue' }] },
  { id: 'mochi', name: 'Mochi', breed: 'Domestic Shorthair', distanceMiles: 3.1,
    photo: featuredDiscoveryPet.companion.photo,
    traits: [{ label: 'Curious', tone: 'teal' }, { label: 'Playful', tone: 'purple' }] },
  { id: 'bella', name: 'Bella', breed: 'French Bulldog', distanceMiles: 3.4,
    photo: { src: null, alt: 'Bella, a French bulldog', placeholder: 'Bella · photo placeholder' },
    traits: [{ label: 'Friendly', tone: 'blue' }, { label: 'Silly', tone: 'pink' }] },
];

export type FilterKey = 'species' | 'size' | 'energy' | 'personality';
export interface FilterGroup {
  key: FilterKey;
  label: string;
  icon: LucideIcon;
  options: readonly string[];
}

export const filterGroups: FilterGroup[] = [
  { key: 'species', label: 'Species', icon: PawPrint, options: ['All', 'Dogs', 'Cats', 'Other'] },
  { key: 'size', label: 'Size (dogs)', icon: UsersRound, options: ['All', 'Small', 'Medium', 'Large', 'Extra Large'] },
  { key: 'energy', label: 'Energy Level', icon: Zap, options: ['All', 'Low', 'Medium', 'High'] },
  { key: 'personality', label: 'Personality', icon: Heart, options: ['All', 'Playful', 'Calm', 'Friendly', 'Independent', 'Adventurous'] },
];

export const defaultFilterSelection: Record<FilterKey, string> = {
  species: 'All', size: 'All', energy: 'All', personality: 'All',
};
