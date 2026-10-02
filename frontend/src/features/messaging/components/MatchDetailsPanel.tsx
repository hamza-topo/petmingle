import { CalendarDays, Check, MapPin, Shield, Users } from 'lucide-react';
import { ReferenceImage } from '../../../components/ReferenceImage';
import { ActionButton } from '../../../components/Action';
import { type Conversation } from '../messaging.fixtures';
const tips = ['Meet in a public, pet-friendly place', 'Keep initial meetups short and supervised', 'Make sure pets are up to date on vaccines', "Follow your pet’s comfort cues"];
export function MatchDetailsPanel({ conversation }: { conversation: Conversation }) {
  return <aside className="match-details" aria-label="Active match details">
    <div className="match-pets">{conversation.pets.map(pet => <article className="match-pet" key={pet.id} aria-label={`${pet.name} details`}>
      <ReferenceImage asset={pet.photo} /><div><h3>{pet.name} {pet.sex && <span className={`chat-sex chat-sex--${pet.sex}`} aria-label={pet.sex}>{pet.sex === 'female' ? '♀' : '♂'}</span>}</h3>{pet.breed && <p>{pet.breed}</p>}{pet.ageYears !== undefined && <p>{pet.ageYears} years old</p>}</div>
    </article>)}</div>
    <section className="match-interests" aria-labelledby="interests-title"><div className="match-section-title"><h3 id="interests-title">Shared Interests</h3><button type="button" disabled>View all</button></div>
      <div className="match-interest-grid">{conversation.interests.map(interest => <div key={interest.id}><ReferenceImage asset={interest.artwork} /><p>{interest.label}</p></div>)}</div>
    </section>
    <section className="match-playdate" aria-labelledby="playdate-title"><div className="match-playdate-heading"><span><CalendarDays size={29} /></span><div><h3 id="playdate-title">Plan a Playdate</h3><p>Turn this match into a real-life adventure for {conversation.pets[0].name} and {conversation.pets[1].name}!</p></div></div>
      <ActionButton unavailableReason="Scheduling is not available in this preview"><CalendarDays size={23} />Suggest a Time</ActionButton>
      <ActionButton variant="secondary" unavailableReason="Park selection is not available in this preview"><MapPin size={23} />Pick a Park</ActionButton>
      <button type="button" className="chat-group-playdate" disabled><Users size={25} />Group Playdate</button>
    </section>
    <section className="match-safety" aria-labelledby="safety-title"><div className="match-section-title"><Shield size={27} /><h3 id="safety-title">Safety Tips</h3><button type="button" disabled>View all</button></div><ul>{tips.map(tip => <li key={tip}><Check size={20} aria-hidden="true" /><span>{tip}</span></li>)}</ul></section>
  </aside>;
}
