import { Heart } from 'lucide-react';
import clsx from 'clsx';
import type { ReferenceAsset } from '../assets/landingAssets';
import { ReferenceImage } from './ReferenceImage';
import { PetLocation } from './PetLocation';
import { PetTraitBadge } from './PetTraitBadge';
import type { PetTrait } from './PetTraitBadge';

export interface FeaturedPet {
  id: string;
  name: string;
  breed: string;
  photo: ReferenceAsset;
}

export interface NearbyPet extends FeaturedPet {
  distanceMiles: number;
  traits: PetTrait[];
}

type PetCardProps = { pet: FeaturedPet; variant?: 'featured' } | { pet: NearbyPet; variant: 'nearby' };

export function PetCard(props: PetCardProps) {
  const { pet } = props;
  return (
    <article className={clsx('pet-card', props.variant === 'nearby' && 'pet-card--nearby')} aria-labelledby={`pet-${pet.id}`}>
      <ReferenceImage asset={pet.photo} className="pet-card-photo" />
      <div className="pet-card-details">
        <div className="min-w-0">
          <h3 id={`pet-${pet.id}`}>{pet.name}</h3>
          <p>{pet.breed}</p>
        </div>
        <button
          type="button"
          className="save-pet"
          aria-label={`Save ${pet.name}`}
          disabled
          title="Saving pets is not available in this preview"
        >
          <Heart size={22} fill="none" strokeWidth={1.6} aria-hidden="true" />
        </button>
      </div>
      {props.variant === 'nearby' && (
        <div className="nearby-pet-metadata">
          <PetLocation distanceMiles={props.pet.distanceMiles} />
          <div className="pet-traits">
            {props.pet.traits.map((trait) => <PetTraitBadge key={trait.label} trait={trait} />)}
          </div>
        </div>
      )}
    </article>
  );
}
