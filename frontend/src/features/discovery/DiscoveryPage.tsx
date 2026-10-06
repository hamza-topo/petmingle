import {
  useCallback,
  useEffect,
  useRef,
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
import { taxonomyRequest } from '../profile-creation/taxonomy.api';
import type { Taxonomy } from '../profile-creation/taxonomy.types';
import { DiscoveryFilters } from './components/DiscoveryFilters';
import { DiscoveryHeader } from './components/DiscoveryHeader';
import { DiscoverySidebar } from './components/DiscoverySidebar';
import { FeaturedPetCard } from './components/FeaturedPetCard';
import { PetGrid } from './components/PetGrid';
import {
  DEFAULT_DISCOVERY_FILTERS,
  discoveryFiltersEqual,
  discoveryRequest,
  type DiscoveryFilterValue,
  type DiscoveryPageMeta,
  type DiscoveryPet,
  type PetInteractionState,
} from './discovery.api';
import { petInteractionRequest } from './interaction.api';

type DiscoveryStatus =
  | 'loading'
  | 'ready'
  | 'error';

type TaxonomyStatus =
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
  const [interactions, setInteractions] =
    useState<Map<number, PetInteractionState>>(
      new Map(),
    );
  const [pendingPetIds, setPendingPetIds] =
    useState<Set<number>>(new Set());
  const [interactionError, setInteractionError] =
    useState<string | null>(null);

  const [filters, setFilters] =
    useState<DiscoveryFilterValue>({
      ...DEFAULT_DISCOVERY_FILTERS,
    });

  const [taxonomy, setTaxonomy] =
    useState<Taxonomy | null>(null);
  const [taxonomyStatus, setTaxonomyStatus] =
    useState<TaxonomyStatus>('loading');
  const [taxonomyError, setTaxonomyError] =
    useState<unknown | null>(null);
  const [taxonomyReloadKey, setTaxonomyReloadKey] =
    useState(0);

  const requestId = useRef(0);

  useEffect(() => {
    let cancelled = false;

    async function loadTaxonomy() {
      const token = tokenStorage.get();

      if (!token) {
        if (!cancelled) {
          setTaxonomy(null);
          setTaxonomyStatus('error');
          setTaxonomyError(
            new ApiError(
              'Authentication token is missing.',
              401,
            ),
          );
        }
        return;
      }

      setTaxonomyStatus('loading');
      setTaxonomyError(null);

      try {
        const result = await taxonomyRequest(token);

        if (!cancelled) {
          setTaxonomy(result);
          setTaxonomyStatus('ready');
        }
      } catch (caught) {
        if (!cancelled) {
          setTaxonomy(null);
          setTaxonomyStatus('error');
          setTaxonomyError(caught);
        }
      }
    }

    void loadTaxonomy();

    return () => {
      cancelled = true;
    };
  }, [taxonomyReloadKey]);

  const loadPage = useCallback(
    async (
      pageNumber: number,
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

      const currentRequestId =
        ++requestId.current;

      if (append) {
        setLoadingMore(true);
      } else {
        setStatus('loading');
        setLoadError(null);
      }

      try {
        const result = await discoveryRequest({
          token,
          radiusKm: filters.radiusKm,
          speciesId: filters.speciesId,
          raceId: filters.raceId,
          page: pageNumber,
          perPage: 24,
        });

        if (
          currentRequestId !== requestId.current
        ) {
          return;
        }

        setInteractions(current => {
          const next = new Map(current);

          for (const pet of result.pets) {
            next.set(pet.id, pet.interaction);
          }

          return next;
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
        if (
          currentRequestId !== requestId.current
        ) {
          return;
        }

        setLoadError(caught);

        if (!append) {
          setPets([]);
          setMeta(initialMeta);
          setStatus('error');
        }
      } finally {
        if (
          currentRequestId === requestId.current
        ) {
          setLoadingMore(false);
        }
      }
    },
    [
      filters.radiusKm,
      filters.speciesId,
      filters.raceId,
    ],
  );

  useEffect(() => {
    if (
      accountLocation.status !== 'ready'
      || !accountLocation.currentLocation
    ) {
      requestId.current += 1;
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

  function applyFilters(
    nextFilters: DiscoveryFilterValue,
  ) {
    if (
      discoveryFiltersEqual(
        filters,
        nextFilters,
      )
    ) {
      return;
    }

    setFilters({ ...nextFilters });
  }

  async function persistInteraction(
    petId: number,
    interaction: Exclude<
      PetInteractionState,
      null
    >,
  ) {
    if (pendingPetIds.has(petId)) {
      return;
    }

    const token = tokenStorage.get();

    if (!token) {
      setInteractionError(
        'Authentication token is missing.',
      );
      return;
    }

    setPendingPetIds(current => {
      const next = new Set(current);
      next.add(petId);
      return next;
    });
    setInteractionError(null);

    try {
      const persisted =
        await petInteractionRequest({
          token,
          targetPetId: petId,
          interaction,
        });

      setInteractions(current => {
        const next = new Map(current);
        next.set(
          petId,
          persisted.interaction,
        );
        return next;
      });
    } catch (caught) {
      setInteractionError(
        describeApiFailure(caught).message,
      );
    } finally {
      setPendingPetIds(current => {
        const next = new Set(current);
        next.delete(petId);
        return next;
      });
    }
  }

  function resetFilters() {
    if (
      discoveryFiltersEqual(
        filters,
        DEFAULT_DISCOVERY_FILTERS,
      )
    ) {
      return;
    }

    setFilters({
      ...DEFAULT_DISCOVERY_FILTERS,
    });
  }

  const closestPet = pets[0] ?? null;
  const gridPets = pets.slice(1);
  const hasMore =
    meta.current_page < meta.last_page;

  const failure =
    loadError !== null
      ? describeApiFailure(loadError)
      : null;

  const taxonomyFailure =
    taxonomyError !== null
      ? describeApiFailure(taxonomyError)
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

      <main tabIndex={-1}
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
                    <span className="text-text-pink">
                      Pets
                    </span>
                  </h1>
                  <p>
                    Meet nearby pets,
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
                    message="Choose your area using the location button above to discover pets nearby."
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
                    message="No pets matched your current filters."
                  />
                )}

              {interactionError && (
                <p
                  className="discovery-interaction-error"
                  role="alert"
                >
                  {interactionError}
                </p>
              )}

              {accountLocation.status === 'ready'
                && accountLocation.currentLocation
                && status === 'ready'
                && closestPet && (
                  <FeaturedPetCard
                    pet={closestPet}
                    interaction={
                      interactions.get(
                        closestPet.id,
                      )
                      ?? closestPet.interaction
                    }
                    pending={pendingPetIds.has(
                      closestPet.id,
                    )}
                    onLike={() =>
                      void persistInteraction(
                        closestPet.id,
                        'liked',
                      )
                    }
                    onDislike={() =>
                      void persistInteraction(
                        closestPet.id,
                        'disliked',
                      )
                    }
                  />
                )}
            </section>
          </div>

          {closestPet && (
            <PetGrid
              pets={gridPets}
              hasMore={hasMore}
              loadingMore={loadingMore}
              interactions={interactions}
              pendingPetIds={pendingPetIds}
              onLike={petId =>
                void persistInteraction(
                  petId,
                  'liked',
                )
              }
              onDislike={petId =>
                void persistInteraction(
                  petId,
                  'disliked',
                )
              }
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
          value={filters}
          resultCount={meta.total}
          taxonomy={taxonomy}
          taxonomyLoading={
            taxonomyStatus === 'loading'
          }
          taxonomyError={
            taxonomyFailure?.message ?? null
          }
          loading={status === 'loading'}
          onApply={applyFilters}
          onReset={resetFilters}
          onRetryTaxonomy={() =>
            setTaxonomyReloadKey(
              current => current + 1,
            )
          }
        />
      </main>
    </div>
  );
}

