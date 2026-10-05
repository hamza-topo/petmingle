import { nalaIdentity } from '../../fixtures/petIdentity';
import { CalendarDays, Eye, Heart, History, House, MapPin, PawPrint, Search, Star, Sun, TreePine, Weight } from 'lucide-react';
import type { ReferenceAsset } from '../../assets/landingAssets';
import type { PetTrait } from '../../components/PetTraitBadge';
export interface ProfileOwner { id: string; name: string | null; petIds: string[] }
export interface GalleryPhoto { id: string; asset: ReferenceAsset }
export interface OwnPet { ownerId: string; id: string; name: string; ageYears: number; breed: string; location: string; biography: string; gallery: GalleryPhoto[]; traits: PetTrait[] }
export interface PlusPlan { id: string; label: string; monthlyPrice: string; billing: string; benefits: string[]; recommended?: boolean; saving?: string }
export const profileOwner: ProfileOwner = { id: 'profile-owner', name: null, petIds: ['nala'] };
export const ownPet: OwnPet = {
  ...nalaIdentity, ownerId: profileOwner.id, location: 'San Diego, CA',
  biography: 'Nala is a happy, playful girl who loves the beach, belly rubs, and making new friends! She’s super friendly with other dogs and people, always up for an adventure and a good game of fetch.',
  gallery: ['Portrait', 'Playing with a ball', 'Running outdoors', 'Resting outdoors'].map((description, index) => ({ id: `nala-photo-${index + 1}`, asset: { src: null, alt: `Nala — ${description}`, placeholder: `Photo ${index + 1}` } })),
  traits: [{ label: 'Friendly', tone: 'pink', icon: Heart }, { label: 'High Energy', tone: 'blue', icon: Sun }, { label: 'Loves the Beach', tone: 'pink', icon: Heart }, { label: 'Great with Dogs', tone: 'teal', icon: TreePine }],
};
export const profileDetails = [
  [{ label: 'Age', value: `${ownPet.ageYears} years old`, icon: CalendarDays }, { label: 'Breed', value: ownPet.breed, icon: PawPrint }, { label: 'Weight', value: '62 lbs', icon: Weight }],
  [{ label: 'Location', value: ownPet.location, icon: MapPin }, { label: 'Spayed/Neutered', value: 'Yes', icon: PawPrint }, { label: 'Good with', value: 'Dogs, People, Kids', icon: House }, { label: 'Favorite Activities', value: 'Fetch, Hiking, Beach', icon: Star }],
];
export const plusFeatures = [
  { title: 'Advanced Filters', text: 'Find the perfect playmates by size, energy level, breed, and more.', icon: Search },
  { title: 'Boost Profile Visibility', text: 'Get your pet seen by more local matches.', icon: Eye },
  { title: 'See Who Liked Your Pet', text: 'Find out who’s interested in meeting Nala.', icon: Heart },
  { title: 'Unlimited Rewinds', text: 'Missed a great match? Go back anytime.', icon: History },
];
export const plusPlans: PlusPlan[] = [
  { id: 'annual', label: '12 Months', monthlyPrice: '$7.99', billing: 'Billed $95.88 annually', recommended: true, saving: 'Save 33%', benefits: ['All premium features', 'Best value', 'More time to make friends'] },
  { id: 'quarterly', label: '3 Months', monthlyPrice: '$11.99', billing: 'Billed $35.97 quarterly', benefits: ['All premium features', 'Great for short-term', 'More matches, more fun'] },
  { id: 'monthly', label: '1 Month', monthlyPrice: '$14.99', billing: 'Billed monthly', benefits: ['All premium features', 'Try it out anytime', 'Cancel anytime'] },
];
