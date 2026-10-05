import {
  ChevronDown,
  MapPin,
  Search,
} from 'lucide-react';

import { ActionButton } from '../../../components/Action';
import { filterGroups } from '../discovery.filter-config';

export function DiscoveryFilters({
  resultCount,
}: {
  resultCount: number;
}) {
  return (
    <aside
      className="discovery-filters"
      aria-labelledby="filter-heading"
    >
      <div className="filter-heading">
        <h2 id="filter-heading">Filter Pets</h2>
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
            value="5"
            disabled
          >
            <option value="5">
              Within 5 km
            </option>
          </select>
          <ChevronDown size={18} aria-hidden="true" />
        </div>
      </div>

      {filterGroups.map(
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
        Advanced filters are not connected to
        persisted Discovery data yet.
      </p>

      <ActionButton
        className="show-pets"
        onClick={() =>
          document
            .getElementById('nearby-pets')
            ?.scrollIntoView({
              block: 'nearest',
            })
        }
      >
        <Search size={24} aria-hidden="true" />
        Showing {resultCount}{' '}
        {resultCount === 1 ? 'Pet' : 'Pets'}
      </ActionButton>
    </aside>
  );
}
