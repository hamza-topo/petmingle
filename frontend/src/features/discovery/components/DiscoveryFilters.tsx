import { useState } from 'react';
import { ChevronDown, MapPin, Search } from 'lucide-react';
import { ActionButton } from '../../../components/Action';
import { defaultFilterSelection, discoveryContext, filterGroups } from '../discovery.fixtures';

export function DiscoveryFilters() {
  const [selection, setSelection] = useState({ ...defaultFilterSelection });
  return (
    <aside className="discovery-filters" aria-labelledby="filter-heading">
      <div className="filter-heading"><h2 id="filter-heading">Filter Pets</h2><button type="button" onClick={() => setSelection({ ...defaultFilterSelection })}>Clear All</button></div>
      <div className="distance-filter">
        <label htmlFor="pet-distance"><MapPin size={21} fill="currentColor" aria-hidden="true" />Distance</label>
        <div className="discovery-select">
          <select id="pet-distance" defaultValue="10"><option value="10">Within 10 miles</option></select>
          <ChevronDown size={18} aria-hidden="true" />
        </div>
      </div>
      {filterGroups.map(({ key, label, icon: Icon, options }) => (
        <fieldset className="filter-group" key={key} aria-describedby="filter-preview-note">
          <legend><Icon size={20} aria-hidden="true" />{label}</legend>
          <div className="filter-choices">
            {options.map((option) => (
              <label className="filter-choice" key={option}>
                <input type="radio" name={key} value={option} checked={selection[key] === option} onChange={() => setSelection((previous) => ({ ...previous, [key]: option }))} />
                <span>{option}</span>
              </label>
            ))}
          </div>
        </fieldset>
      ))}
      <p className="sr-only" id="filter-preview-note">Selections are a local visual preview only. The reference count and nearby pets do not change.</p>
      <ActionButton className="show-pets" onClick={() => document.getElementById('nearby-pets')?.scrollIntoView({ block: 'nearest' })} aria-describedby="filter-preview-note"><Search size={24} aria-hidden="true" />Show {discoveryContext.petCount} Pets</ActionButton>
    </aside>
  );
}
