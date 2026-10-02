import { ChevronRight, MapPin, PawPrint, Pencil } from 'lucide-react';
import { ownPet, profileStats } from '../profile.fixtures';
export function ProfileEdit({ label }: { label: string }) { return <button type="button" className="own-edit" disabled aria-label={`${label} — unavailable`}><Pencil size={17} /></button>; }
export function PetProfileSummary() {
  return <section className="own-summary" aria-labelledby="own-pet-name">
    <div className="own-name"><h1 id="own-pet-name">{ownPet.name}</h1><ProfileEdit label="Edit pet name" /></div>
    <p>{ownPet.ageYears} years old <span aria-hidden="true"> · </span> {ownPet.breed}</p>
    <p className="own-location"><MapPin size={21} aria-hidden="true" />{ownPet.location}</p>
    <dl className="own-stats" aria-label="Pet profile statistics">{profileStats.map(stat => <div key={stat.id}><dt>{stat.label}</dt><dd>{stat.value}</dd></div>)}</dl>
    <button type="button" className="own-complete" disabled title="Profile completion editing is not available in this preview"><PawPrint size={30} fill="currentColor" /><span>Complete your profile</span><ChevronRight size={25} /></button>
  </section>;
}
