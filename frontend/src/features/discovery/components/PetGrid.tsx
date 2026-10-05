import {
  ChevronDown,
  Grid2X2,
  List,
} from 'lucide-react';

import { PetCard } from '../../../components/PetCard';
import { SectionHeading } from '../../../components/SectionHeading';
import type { DiscoveryPet } from '../discovery.api';

export function PetGrid({
  pets,
  hasMore,
  loadingMore,
  onLoadMore,
}: {
  pets: DiscoveryPet[];
  hasMore: boolean;
  loadingMore: boolean;
  onLoadMore: () => void;
}) {
  return (
    <section
      id="nearby-pets"
      className="nearby-pets"
      aria-labelledby="nearby-heading"
    >
      <SectionHeading
        id="nearby-heading"
        action={
          <div className="nearby-toolbar">
            <label htmlFor="pet-sort">
              Sort by
            </label>

            <div className="discovery-select">
              <select
                id="pet-sort"
                value="nearest"
                disabled
                aria-label="Discovery sort order"
              >
                <option value="nearest">
                  Distance (Nearest)
                </option>
              </select>
              <ChevronDown
                size={16}
                aria-hidden="true"
              />
            </div>

            <div
              className="pet-view-controls"
              role="group"
              aria-label="Pet view"
            >
              <button
                type="button"
                aria-label="Grid view"
                aria-pressed="true"
              >
                <Grid2X2
                  size={18}
                  aria-hidden="true"
                />
              </button>

              <button
                type="button"
                disabled
                aria-label="List view — unavailable"
              >
                <List
                  size={18}
                  aria-hidden="true"
                />
              </button>
            </div>
          </div>
        }
      >
        More Pets Nearby
      </SectionHeading>

      {pets.length > 0 ? (
        <ul
          className="nearby-pet-grid"
          aria-label="Nearby pets"
        >
          {pets.map(pet => (
            <li key={pet.id}>
              <PetCard
                pet={pet}
                variant="nearby"
              />
            </li>
          ))}
        </ul>
      ) : (
        <p
          className="discovery-grid-empty"
          role="status"
        >
          No additional nearby pets on this page.
        </p>
      )}

      {hasMore && (
        <button
          type="button"
          className="discovery-load-more"
          onClick={onLoadMore}
          disabled={loadingMore}
        >
          {loadingMore
            ? 'Loading more pets...'
            : 'Load more pets'}
        </button>
      )}
    </section>
  );
}
