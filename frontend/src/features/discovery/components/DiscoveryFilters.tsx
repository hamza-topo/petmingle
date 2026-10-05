import {
  useEffect,
  useMemo,
  useState,
} from 'react';
import {
  ChevronDown,
  MapPin,
  Search,
} from 'lucide-react';

import { ActionButton } from '../../../components/Action';
import type { Taxonomy } from '../../profile-creation/taxonomy.types';
import {
  DEFAULT_DISCOVERY_FILTERS,
  discoveryFiltersEqual,
  type DiscoveryFilterValue,
} from '../discovery.api';
import { unsupportedFilterGroups } from '../discovery.filter-config';

const distanceOptions = [5, 10, 25, 50, 100];

export function DiscoveryFilters({
  value,
  resultCount,
  taxonomy,
  taxonomyLoading,
  taxonomyError,
  loading,
  onApply,
  onReset,
  onRetryTaxonomy,
}: {
  value: DiscoveryFilterValue;
  resultCount: number;
  taxonomy: Taxonomy | null;
  taxonomyLoading: boolean;
  taxonomyError: string | null;
  loading: boolean;
  onApply: (filters: DiscoveryFilterValue) => void;
  onReset: () => void;
  onRetryTaxonomy: () => void;
}) {
  const [draft, setDraft] =
    useState<DiscoveryFilterValue>(value);

  useEffect(() => {
    setDraft(value);
  }, [
    value.radiusKm,
    value.speciesId,
    value.raceId,
  ]);

  const races = useMemo(
    () =>
      draft.speciesId === null
        ? []
        : taxonomy?.races.filter(
            race =>
              race.species_id === draft.speciesId,
          ) ?? [],
    [draft.speciesId, taxonomy],
  );

  const dirty = !discoveryFiltersEqual(
    draft,
    value,
  );

  const canReset =
    !discoveryFiltersEqual(
      draft,
      DEFAULT_DISCOVERY_FILTERS,
    )
    || !discoveryFiltersEqual(
      value,
      DEFAULT_DISCOVERY_FILTERS,
    );

  function reset() {
    setDraft({
      ...DEFAULT_DISCOVERY_FILTERS,
    });
    onReset();
  }

  return (
    <aside
      className="discovery-filters"
      aria-labelledby="filter-heading"
    >
      <div className="filter-heading">
        <h2 id="filter-heading">Filter Pets</h2>
        <button
          type="button"
          onClick={reset}
          disabled={!canReset || loading}
        >
          Clear All
        </button>
      </div>

      <div className="distance-filter">
        <label htmlFor="pet-distance">
          <MapPin
            size={21}
            fill="currentColor"
            aria-hidden="true"
          />
          Distance
        </label>

        <div className="discovery-select">
          <select
            id="pet-distance"
            value={String(draft.radiusKm)}
            onChange={event =>
              setDraft(current => ({
                ...current,
                radiusKm: Number(
                  event.target.value,
                ),
              }))
            }
          >
            {distanceOptions.map(distance => (
              <option
                key={distance}
                value={distance}
              >
                Within {distance} km
              </option>
            ))}
          </select>
          <ChevronDown
            size={18}
            aria-hidden="true"
          />
        </div>
      </div>

      <div className="distance-filter">
        <label htmlFor="pet-species">
          Species
        </label>

        <div className="discovery-select">
          <select
            id="pet-species"
            value={
              draft.speciesId === null
                ? ''
                : String(draft.speciesId)
            }
            disabled={
              taxonomyLoading
              || taxonomyError !== null
              || !taxonomy
            }
            onChange={event => {
              const speciesId =
                event.target.value === ''
                  ? null
                  : Number(event.target.value);

              setDraft(current => ({
                ...current,
                speciesId,
                raceId: null,
              }));
            }}
          >
            <option value="">
              {taxonomyLoading
                ? 'Loading species...'
                : taxonomyError
                  ? 'Species unavailable'
                  : 'All species'}
            </option>

            {taxonomy?.species.map(species => (
              <option
                key={species.id}
                value={species.id}
              >
                {species.name}
              </option>
            ))}
          </select>
          <ChevronDown
            size={18}
            aria-hidden="true"
          />
        </div>
      </div>

      <div className="distance-filter">
        <label htmlFor="pet-race">
          Breed
        </label>

        <div className="discovery-select">
          <select
            id="pet-race"
            value={
              draft.raceId === null
                ? ''
                : String(draft.raceId)
            }
            disabled={
              taxonomyLoading
              || taxonomyError !== null
              || !taxonomy
              || draft.speciesId === null
            }
            onChange={event =>
              setDraft(current => ({
                ...current,
                raceId:
                  event.target.value === ''
                    ? null
                    : Number(event.target.value),
              }))
            }
          >
            <option value="">
              {draft.speciesId === null
                ? 'Choose a species first'
                : 'All breeds'}
            </option>

            {races.map(race => (
              <option
                key={race.id}
                value={race.id}
              >
                {race.name}
              </option>
            ))}
          </select>
          <ChevronDown
            size={18}
            aria-hidden="true"
          />
        </div>
      </div>

      {taxonomyError && (
        <div className="discovery-taxonomy-error">
          <p role="alert">{taxonomyError}</p>
          <button
            type="button"
            onClick={onRetryTaxonomy}
          >
            Retry taxonomy
          </button>
        </div>
      )}

      {unsupportedFilterGroups.map(
        ({
          key,
          label,
          icon: Icon,
          options,
        }) => (
          <fieldset
            className="filter-group"
            key={key}
            disabled
            aria-describedby="filter-deferred-note"
          >
            <legend>
              <Icon size={20} aria-hidden="true" />
              {label}
            </legend>

            <div className="filter-choices">
              {options.map(option => (
                <label
                  className="filter-choice"
                  key={option}
                >
                  <input
                    type="radio"
                    name={key}
                    value={option}
                    checked={option === 'All'}
                    readOnly
                  />
                  <span>{option}</span>
                </label>
              ))}
            </div>
          </fieldset>
        ),
      )}

      <p
        className="discovery-filter-deferred"
        id="filter-deferred-note"
      >
        Size, energy and personality are deferred
        until they have persisted backend fields.
      </p>

      <p className="discovery-filter-result-count">
        Current results: {resultCount}
      </p>

      <ActionButton
        className="show-pets"
        disabled={!dirty || loading}
        onClick={() => onApply(draft)}
      >
        <Search size={24} aria-hidden="true" />
        {loading
          ? 'Applying filters...'
          : 'Apply filters'}
      </ActionButton>
    </aside>
  );
}
