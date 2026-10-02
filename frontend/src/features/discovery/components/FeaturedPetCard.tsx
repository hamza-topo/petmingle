import { ChevronLeft, ChevronRight, Heart, Image, PawPrint, ShieldCheck, Star } from 'lucide-react';
import { ActionButton } from '../../../components/Action';
import { ReferenceImage } from '../../../components/ReferenceImage';
import { Avatar } from '../../../components/Avatar';
import { PetLocation } from '../../../components/PetLocation';
import { PetTraitBadge } from '../../../components/PetTraitBadge';
import { featuredDiscoveryPet as pet } from '../discovery.fixtures';

export function FeaturedPetCard() {
  return (
    <article className="featured-discovery-pet" aria-labelledby="featured-pet-name">
      <div className="featured-pet-gallery">
        <ReferenceImage asset={pet.photo} className="featured-pet-photo" />
        <span className="featured-label"><Star size={21} aria-hidden="true" />Featured</span>
        <button className="gallery-arrow gallery-arrow--previous" type="button" disabled aria-label="Previous photo — reference assets unavailable"><ChevronLeft size={26} aria-hidden="true" /></button>
        <button className="gallery-arrow gallery-arrow--next" type="button" disabled aria-label="Next photo — reference assets unavailable"><ChevronRight size={26} aria-hidden="true" /></button>
        <span className="gallery-count"><Image size={16} aria-hidden="true" />1 / {pet.photoCount}</span>
      </div>
      <div className="featured-pet-details">
        <div>
          <div className="featured-name-row"><h2 id="featured-pet-name">{pet.name}</h2>{pet.verified && <ShieldCheck className="pet-verified" size={28} role="img" aria-label="Verified pet" />}</div>
          <p className="featured-pet-identity">{pet.breed}<span aria-hidden="true"> · </span>{pet.ageYears} years old</p>
          <PetLocation distanceMiles={pet.distanceMiles} location={pet.location} />
        </div>
        <p className="featured-pet-description">{pet.description}</p>
        <div className="pet-traits featured-pet-traits">
          {pet.traits.map((trait) => <PetTraitBadge key={trait.label} trait={trait} />)}
        </div>
        <button className="companion-pet" type="button" disabled title="Companion profiles are unavailable in this preview">
          <Avatar asset={pet.companion.photo} className="companion-avatar" />
          <span><small>Lives with</small><strong>{pet.companion.name}</strong><small>{pet.companion.breed} <span aria-hidden="true"> · </span> {pet.companion.ageYears} years</small></span>
          <ChevronRight size={22} aria-hidden="true" />
        </button>
        <div className="featured-pet-actions">
          <ActionButton variant="secondary" className="featured-save" unavailableReason="Saving pets is unavailable in this preview"><Heart size={26} aria-hidden="true" />Save</ActionButton>
          <ActionButton unavailableReason="Matching is unavailable in this preview"><PawPrint size={28} fill="currentColor" aria-hidden="true" />Say Hello</ActionButton>
        </div>
      </div>
    </article>
  );
}
