import {
  Heart,
  Image,
  MapPin,
  PawPrint,
} from 'lucide-react';

import { ActionButton } from '../../../components/Action';
import { PetLocation } from '../../../components/PetLocation';
import { ReferenceImage } from '../../../components/ReferenceImage';
import type { DiscoveryPet } from '../discovery.api';

export function FeaturedPetCard({
  pet,
}: {
  pet: DiscoveryPet;
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

        <div className="featured-pet-actions">
          <ActionButton
            variant="secondary"
            className="featured-save"
            unavailableReason="Saving pets is not available yet"
          >
            <Heart size={26} aria-hidden="true" />
            Save
          </ActionButton>

          <ActionButton
            unavailableReason="Pet interactions are not available yet"
          >
            <PawPrint
              size={28}
              fill="currentColor"
              aria-hidden="true"
            />
            Say Hello
          </ActionButton>
        </div>
      </div>
    </article>
  );
}
