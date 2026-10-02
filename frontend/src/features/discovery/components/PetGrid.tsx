import { ChevronDown, Grid2X2, List } from 'lucide-react';
import { PetCard } from '../../../components/PetCard';
import { SectionHeading } from '../../../components/SectionHeading';
import { nearbyPets } from '../discovery.fixtures';

export function PetGrid() {
  return (
    <section id="nearby-pets" className="nearby-pets" aria-labelledby="nearby-heading">
      <SectionHeading id="nearby-heading" action={
        <div className="nearby-toolbar">
          <label htmlFor="pet-sort">Sort by</label>
          <div className="discovery-select"><select id="pet-sort" defaultValue="nearest"><option value="nearest">Distance (Nearest)</option></select><ChevronDown size={16} aria-hidden="true" /></div>
          <div className="pet-view-controls" role="group" aria-label="Pet view">
            <button type="button" aria-label="Grid view" aria-pressed="true"><Grid2X2 size={18} aria-hidden="true" /></button>
            <button type="button" disabled aria-label="List view — unavailable in this preview"><List size={18} aria-hidden="true" /></button>
          </div>
        </div>
      }>More Amazing Pets Nearby</SectionHeading>
      <ul className="nearby-pet-grid" aria-label="Nearby pets">
        {nearbyPets.map((pet) => <li key={pet.id}><PetCard pet={pet} variant="nearby" /></li>)}
      </ul>
    </section>
  );
}
