import {
  useCallback,
  useEffect,
  useState,
} from 'react';
import { PawPrint } from 'lucide-react';

import { ApiError } from '../../api/errors';
import { describeApiFailure } from '../../api/presentation';
import { tokenStorage } from '../../auth/tokenStorage';
import { ApiState } from '../../components/ApiState';
import {
  accountLocationLabel,
  useAccountLocation,
} from '../account-location/AccountLocationProvider';
import { DiscoveryFilters } from './components/DiscoveryFilters';
import { DiscoveryHeader } from './components/DiscoveryHeader';
import { DiscoverySidebar } from './components/DiscoverySidebar';
import { FeaturedPetCard } from './components/FeaturedPetCard';
import { PetGrid } from './components/PetGrid';
import {
  discoveryRequest,
  type DiscoveryPageMeta,
  type DiscoveryPet,
} from './discovery.api';

type DiscoveryStatus =
  | 'loading'
  | 'ready'
  | 'error';

const initialMeta: DiscoveryPageMeta = {
  current_page: 1,
  last_page: 1,
  per_page: 24,
  total: 0,
};

export function DiscoveryPage() {
  const accountLocation = useAccountLocation();
  const locationLabel =
    accountLocationLabel(accountLocation);

  const [status, setStatus] =
    useState<DiscoveryStatus>('loading');
  const [pets, setPets] = useState<DiscoveryPet[]>(
    [],
  );
  const [meta, setMeta] =
    useState<DiscoveryPageMeta>(initialMeta);
  const [loadError, setLoadError] =
    useState<unknown | null>(null);
  const [loadingMore, setLoadingMore] =
    useState(false);

  const loadPage = useCallback(
    async (
      page: number,
      append = false,
    ): Promise<void> => {
      const token = tokenStorage.get();

      if (!token) {
        setLoadError(
          new ApiError(
            'Authentication token is missing.',
            401,
          ),
        );
        setStatus('error');
        return;
      }

      if (append) {
        setLoadingMore(true);
      } else {
        setStatus('loading');
        setLoadError(null);
      }

      try {
        const result = await discoveryRequest({
          token,
          radiusKm: 5,
          page,
          perPage: 24,
        });

        setPets(current => {
          if (!append) {
            return result.pets;
          }

          const byId = new Map(
            current.map(pet => [pet.id, pet]),
          );

          for (const pet of result.pets) {
            byId.set(pet.id, pet);
          }

          return [...byId.values()];
        });

        setMeta(result.meta);
        setLoadError(null);
        setStatus('ready');
      } catch (caught) {
        setLoadError(caught);

        if (!append) {
          setPets([]);
          setMeta(initialMeta);
          setStatus('error');
        }
      } finally {
        setLoadingMore(false);
      }
    },
    [],
  );

  useEffect(() => {
    if (
      accountLocation.status !== 'ready'
      || !accountLocation.currentLocation
    ) {
      setPets([]);
      setMeta(initialMeta);
      setLoadError(null);
      setStatus('ready');
      return;
    }

    void loadPage(1);
  }, [
    accountLocation.status,
    accountLocation.currentLocation?.id,
    accountLocation.currentLocation?.latitude,
    accountLocation.currentLocation?.longitude,
    loadPage,
  ]);

  const closestPet = pets[0] ?? null;
  const gridPets = pets.slice(1);
  const hasMore =
    meta.current_page < meta.last_page;

  const failure =
    loadError !== null
      ? describeApiFailure(loadError)
      : null;

  return (
    <div className="discovery-page">
      <a
        href="#discovery-main"
        className="skip-link"
      >
        Skip to content
      </a>

      <DiscoveryHeader />

      <main
        className="discovery-layout"
        id="discovery-main"
      >
        <div className="discovery-results">
          <div className="discovery-upper">
            <DiscoverySidebar />

            <section
              className="discovery-spotlight"
              aria-labelledby="discovery-heading"
            >
              <div className="discovery-intro">
                <div>
                  <h1 id="discovery-heading">
                    Discover Amazing{' '}
                    <span className="text-brand-pink">
                      Pets
                    </span>
                  </h1>
                  <p>
                    Meet persisted nearby pet profiles,
                    ordered by distance.
                  </p>
                </div>

                <div className="nearby-count">
                  <span>
                    <PawPrint
                      size={30}
                      fill="currentColor"
                      aria-hidden="true"
                    />
                  </span>
                  <p>
                    <strong>{meta.total}</strong>{' '}
                    {meta.total === 1
                      ? 'pet'
                      : 'pets'}{' '}
                    near
                    <br />
                    <small>{locationLabel}</small>
                  </p>
                </div>
              </div>

              {accountLocation.status === 'loading' && (
                <ApiState
                  kind="loading"
                  message="Loading your location..."
                />
              )}

              {accountLocation.status === 'error' && (
                <ApiState
                  kind="error"
                  title="Location unavailable"
                  message="PetMingle could not load your account location."
                  onRetry={() =>
                    void accountLocation.reload()
                  }
                />
              )}

              {accountLocation.status === 'ready'
                && !accountLocation.currentLocation && (
                  <ApiState
                    kind="empty"
                    title="Set your location"
                    message="Add account coordinates above before loading nearby pets."
                  />
                )}

              {accountLocation.status === 'ready'
                && accountLocation.currentLocation
                && status === 'loading' && (
                  <ApiState
                    kind="loading"
                    message="Loading nearby pets..."
                  />
                )}

              {accountLocation.status === 'ready'
                && accountLocation.currentLocation
                && status === 'error'
                && failure && (
                  <ApiState
                    kind="error"
                    title="Nearby pets unavailable"
                    message={failure.message}
                    onRetry={
                      failure.retryable
                        ? () => void loadPage(1)
                        : undefined
                    }
                  />
                )}

              {accountLocation.status === 'ready'
                && accountLocation.currentLocation
                && status === 'ready'
                && !closestPet && (
                  <ApiState
                    kind="empty"
                    title="No nearby pets yet"
                    message="No persisted pet profiles were found within 5 km."
                  />
                )}

              {accountLocation.status === 'ready'
                && accountLocation.currentLocation
                && status === 'ready'
                && closestPet && (
                  <FeaturedPetCard
                    pet={closestPet}
                  />
                )}
            </section>
          </div>

          {closestPet && (
            <PetGrid
              pets={gridPets}
              hasMore={hasMore}
              loadingMore={loadingMore}
              onLoadMore={() =>
                void loadPage(
                  meta.current_page + 1,
                  true,
                )
              }
            />
          )}
        </div>

        <DiscoveryFilters
          resultCount={meta.total}
        />
      </main>
    </div>
  );
}
