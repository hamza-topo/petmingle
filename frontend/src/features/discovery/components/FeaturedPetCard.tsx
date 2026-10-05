import {
  Heart,
  Image,
  MapPin,
  X,
} from 'lucide-react';

import { ActionButton } from '../../../components/Action';
import { PetLocation } from '../../../components/PetLocation';
import { ReferenceImage } from '../../../components/ReferenceImage';
import type {
  DiscoveryPet,
  PetInteractionState,
} from '../discovery.api';

export function FeaturedPetCard({
  pet,
  interaction,
  pending,
  onLike,
  onDislike,
}: {
  pet: DiscoveryPet;
  interaction: PetInteractionState;
  pending: boolean;
  onLike: () => void;
  onDislike: () => void;
}) {
  return (
    <article
      className="featured-discovery-pet"
      aria-labelledby={`featured-pet-${pet.id}`}
    >
      <div className="featured-pet-gallery">
        <ReferenceImage
          asset={pet.photo}
          className="featured-pet-photo"
        />

        <span className="featured-label">
          <MapPin size={19} aria-hidden="true" />
          Closest nearby
        </span>

        <span className="gallery-count">
          <Image size={16} aria-hidden="true" />
          {pet.photoCount > 0
            ? `${pet.photoCount} saved photo${pet.photoCount === 1 ? '' : 's'}`
            : 'No saved photos'}
        </span>
      </div>

      <div className="featured-pet-details">
        <div>
          <div className="featured-name-row">
            <h2 id={`featured-pet-${pet.id}`}>
              {pet.name}
            </h2>

            {pet.isNew && (
              <span className="discovery-new-pet">
                New
              </span>
            )}
          </div>

          <p className="featured-pet-identity">
            {pet.breed}
            <span aria-hidden="true"> · </span>
            {pet.ageYears}{' '}
            {pet.ageYears === 1 ? 'year' : 'years'} old
          </p>

          <PetLocation
            distanceKm={pet.distanceKm}
          />
        </div>

        <p className="featured-pet-description">
          {pet.about ?? 'No biography available yet.'}
        </p>

        <p className="discovery-owner-note">
          Shared by {pet.ownerName}
        </p>

        <div
          className="featured-pet-actions"
          aria-label={`Interactions with ${pet.name}`}
        >
          <ActionButton
            variant="secondary"
            className="featured-pass"
            onClick={onDislike}
            disabled={pending}
            aria-pressed={interaction === 'disliked'}
          >
            <X size={26} aria-hidden="true" />
            {interaction === 'disliked'
              ? 'Passed'
              : 'Pass'}
          </ActionButton>

          <ActionButton
            onClick={onLike}
            disabled={pending}
            aria-pressed={interaction === 'liked'}
          >
            <Heart
              size={26}
              fill={
                interaction === 'liked'
                  ? 'currentColor'
                  : 'none'
              }
              aria-hidden="true"
            />
            {pending
              ? 'Saving...'
              : interaction === 'liked'
                ? 'Liked'
                : 'Like'}
          </ActionButton>
        </div>
      </div>
    </article>
  );
}
