import {
  Heart,
  X,
} from 'lucide-react';
import clsx from 'clsx';

import type { ReferenceAsset } from '../assets/landingAssets';
import { ReferenceImage } from './ReferenceImage';
import { PetLocation } from './PetLocation';
import type { PetTrait } from './PetTraitBadge';
import type { PetInteractionState } from '../features/discovery/discovery.api';

export interface FeaturedPet {
  id: string | number;
  name: string;
  breed: string;
  photo: ReferenceAsset;
}

export interface NearbyPet extends FeaturedPet {
  distanceKm?: number;
  distanceMiles?: number;
  traits?: PetTrait[];
}

type PetCardProps =
  | {
      pet: FeaturedPet;
      variant?: 'featured';
    }
  | {
      pet: NearbyPet;
      variant: 'nearby';
      interaction: PetInteractionState;
      pending: boolean;
      onLike: () => void;
      onDislike: () => void;
    };

export function PetCard(props: PetCardProps) {
  const { pet } = props;

  return (
    <article
      className={clsx(
        'pet-card',
        props.variant === 'nearby'
          && 'pet-card--nearby',
      )}
      aria-labelledby={`pet-${pet.id}`}
    >
      <ReferenceImage
        asset={pet.photo}
        className="pet-card-photo"
      />

      <div className="pet-card-details">
        <div className="min-w-0">
          <h3 id={`pet-${pet.id}`}>
            {pet.name}
          </h3>
          <p>{pet.breed}</p>
        </div>

        {props.variant === 'nearby' && (
          <div
            className="pet-card-interactions"
            aria-label={`Interactions with ${pet.name}`}
          >
            <button
              type="button"
              className="pet-card-interaction"
              aria-label={`Pass ${pet.name}`}
              aria-pressed={
                props.interaction === 'disliked'
              }
              disabled={props.pending}
              onClick={props.onDislike}
            >
              <X size={18} aria-hidden="true" />
            </button>

            <button
              type="button"
              className="pet-card-interaction"
              aria-label={`Like ${pet.name}`}
              aria-pressed={
                props.interaction === 'liked'
              }
              disabled={props.pending}
              onClick={props.onLike}
            >
              <Heart
                size={18}
                fill={
                  props.interaction === 'liked'
                    ? 'currentColor'
                    : 'none'
                }
                aria-hidden="true"
              />
            </button>
          </div>
        )}
      </div>

      {props.variant === 'nearby' && (
        <div className="nearby-pet-metadata">
          <PetLocation
            distanceKm={props.pet.distanceKm}
            distanceMiles={props.pet.distanceMiles}
          />
        </div>
      )}
    </article>
  );
}
