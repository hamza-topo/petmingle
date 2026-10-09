import { useState } from 'react';
import { ChevronLeft, ChevronRight, Heart, MapPin, X } from 'lucide-react';

import { mediaUrl } from '../../../api/config';
import { ActionButton } from '../../../components/Action';
import { Avatar } from '../../../components/Avatar';
import { ReferenceImage } from '../../../components/ReferenceImage';
import type { DiscoveryPet, PetInteractionState } from '../discovery.api';

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
  const [photoIndex, setPhotoIndex] = useState(0);
  const photo = pet.images[photoIndex];
  const asset = photo
    ? { ...pet.photo, src: mediaUrl(photo), position: 'center' }
    : pet.photo;

  return (
    <article
      className="featured-discovery-pet"
      aria-labelledby={`featured-pet-${pet.id}`}
    >
      <div className="featured-pet-gallery">
        <ReferenceImage asset={asset} className="featured-pet-photo" />
        {pet.images.length > 1 && (
          <>
            <div
              className="discovery-photo-progress"
              aria-label="Photos du profil"
            >
              {pet.images.map((image, index) => (
                <button
                  key={`${image}-${index}`}
                  type="button"
                  aria-label={`Photo ${index + 1} de ${pet.name}`}
                  aria-pressed={index === photoIndex}
                  onClick={() => setPhotoIndex(index)}
                />
              ))}
            </div>
            <button
              className="gallery-arrow gallery-arrow--previous"
              type="button"
              aria-label="Photo précédente"
              onClick={() =>
                setPhotoIndex(
                  (current) =>
                    (current + pet.images.length - 1) % pet.images.length,
                )
              }
            >
              <ChevronLeft size={22} />
            </button>
            <button
              className="gallery-arrow gallery-arrow--next"
              type="button"
              aria-label="Photo suivante"
              onClick={() =>
                setPhotoIndex((current) => (current + 1) % pet.images.length)
              }
            >
              <ChevronRight size={22} />
            </button>
          </>
        )}
        <span className="gallery-count" aria-live="polite">
          {pet.images.length
            ? `${photoIndex + 1} / ${pet.images.length}`
            : 'Pas encore de photo'}
        </span>
      </div>
      <div className="featured-pet-details">
        <div className="featured-pet-summary">
          <h2 id={`featured-pet-${pet.id}`} tabIndex={-1}>
            {pet.name}
          </h2>
          <p className="featured-pet-identity">
            {pet.ageYears} {pet.ageYears === 1 ? 'an' : 'ans'} · {pet.breed}
          </p>
          <p className="featured-distance">
            <MapPin size={18} aria-hidden="true" />À{' '}
            {pet.distanceKm.toLocaleString('fr-FR', {
              maximumFractionDigits: 1,
            })}{' '}
            km
          </p>
        </div>
        <div className="featured-biography">
          <h3>Un peu de moi</h3>
          <p className="featured-pet-description">
            {pet.about ||
              'Mon compagnon n’a pas encore ajouté de présentation.'}
          </p>
        </div>
        <div className="discovery-owner-note">
          <Avatar
            name={pet.ownerName
              .trim()
              .split(/\s+/)
              .slice(0, 2)
              .map((part) => part[0])
              .join('')
              .toUpperCase()}
            className="discovery-owner-avatar"
          />
          <span>Avec {pet.ownerName}</span>
        </div>
        <div className="featured-decision">
          <div
            className="featured-pet-actions"
            aria-label={`Rencontrer ${pet.name}`}
          >
            <ActionButton
              variant="secondary"
              className="featured-pass"
              onClick={onDislike}
              disabled={pending}
              aria-pressed={interaction === 'disliked'}
            >
              <X size={24} aria-hidden="true" />
              {interaction === 'disliked' ? 'Passé' : 'Passer'}
            </ActionButton>
            <ActionButton
              onClick={onLike}
              disabled={pending || interaction === 'liked'}
              aria-pressed={interaction === 'liked'}
            >
              <Heart
                size={24}
                fill={interaction === 'liked' ? 'currentColor' : 'none'}
                aria-hidden="true"
              />
              {pending
                ? 'En cours…'
                : interaction === 'liked'
                  ? 'Like envoyé'
                  : 'J’aime'}
            </ActionButton>
          </div>
          <p className="discovery-match-note">
            Un like mutuel ouvre la conversation.
          </p>
        </div>
      </div>
    </article>
  );
}
