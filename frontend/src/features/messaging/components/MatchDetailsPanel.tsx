import { ReferenceImage } from '../../../components/ReferenceImage';
import type { Conversation } from '../messaging.types';

export function MatchDetailsPanel({ conversation }: { conversation: Conversation }) {
  return (
    <aside className="match-details" aria-label="Active match details">
      <details className="chat-context">
        <summary>Leur rencontre <span aria-hidden="true">+</span></summary>
        <div className="match-pets">
          {conversation.pets.map(pet => (
            <article className="match-pet" key={pet.id} aria-label={`${pet.name} details`}>
              <ReferenceImage asset={pet.photo} />
              <div><h3>{pet.name}</h3>{pet.breed && <p>{pet.breed}</p>}{pet.ageYears !== undefined && <p>{pet.ageYears} ans</p>}</div>
            </article>
          ))}
        </div>
        <p className="chat-meet-note">Pour une première balade, choisissez ensemble un lieu public et gardez vos animaux sous surveillance.</p>
      </details>
    </aside>
  );
}
