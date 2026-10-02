import { PetTraitBadge } from '../../../components/PetTraitBadge';
import { ownPet, profileDetails } from '../profile.fixtures';
import { ProfileEdit } from './PetProfileSummary';
export function ProfileAbout() { return <section className="own-about" aria-labelledby="own-about-title">
  <div className="own-section-heading"><h2 id="own-about-title">About {ownPet.name}</h2><ProfileEdit label="Edit pet biography" /></div>
  <p>{ownPet.biography}</p><div className="own-traits">{ownPet.traits.map(trait => <PetTraitBadge key={trait.label} trait={trait} />)}</div>
</section>; }
export function ProfileDetails() { return <section className="own-details" aria-labelledby="own-details-title">
  <div className="own-section-heading"><h2 id="own-details-title">Details</h2><ProfileEdit label="Edit pet details" /></div>
  <div className="own-details-columns">{profileDetails.map((column, index) => <dl key={index}>{column.map(({ label, value, icon: Icon }) => <div key={label}><dt><Icon size={25} aria-hidden="true" /><span>{label}</span></dt><dd>{value}</dd></div>)}</dl>)}</div>
</section>; }
