import { ReferenceImage } from '../../../components/ReferenceImage';
import type { DiscoveryPet } from '../discovery.api';

export function PetGrid({
  pets,
  hasMore,
  loadingMore,
  onLoadMore,
  onSelect,
}: {
  pets: DiscoveryPet[];
  hasMore: boolean;
  loadingMore: boolean;
  onLoadMore: () => void;
  onSelect: (id: number) => void;
}) {
  return (
    <section
      id="nearby-pets"
      className="nearby-pets"
      aria-labelledby="nearby-heading"
    >
      <h2 id="nearby-heading">Les compagnons à proximité</h2>
      <p className="nearby-order">Du plus proche au plus éloigné.</p>
      <ul className="nearby-pet-grid" aria-label="Profils à proximité">
        {pets.map((pet) => (
          <li key={pet.id}>
            <button
              className="discovery-profile-preview"
              type="button"
              onClick={() => onSelect(pet.id)}
              aria-label={`Voir le profil de ${pet.name}`}
            >
              <ReferenceImage
                asset={pet.photo}
                className="discovery-preview-photo"
              />
              <span>
                <strong>{pet.name}</strong>
                <small>{pet.breed}</small>
                <small>
                  À{' '}
                  {pet.distanceKm.toLocaleString('fr-FR', {
                    maximumFractionDigits: 1,
                  })}{' '}
                  km
                </small>
              </span>
            </button>
          </li>
        ))}
      </ul>
      {hasMore && (
        <button
          type="button"
          className="discovery-load-more"
          onClick={onLoadMore}
          disabled={loadingMore}
        >
          {loadingMore ? 'Chargement…' : 'Voir plus de profils'}
        </button>
      )}
    </section>
  );
}
