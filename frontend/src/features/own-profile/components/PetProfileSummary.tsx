import {
  ChevronRight,
  PawPrint,
  Pencil,
} from 'lucide-react';
import { PetLocation } from '../../../components/PetLocation';
import type { CurrentPetProfile } from '../profile.types';

export function ProfileEdit({
  label,
  onClick,
}: {
  label: string;
  onClick?: () => void;
}) {
  return (
    <button
      type="button"
      className="own-edit"
      disabled={!onClick}
      onClick={onClick}
      aria-label={
        onClick
          ? label
          : `${label} — unavailable`
      }
    >
      <Pencil size={17} />
    </button>
  );
}

export function PetProfileSummary({
  pet,
  onEdit,
  locationLabel,
}: {
  pet: CurrentPetProfile;
  onEdit?: () => void;
  locationLabel: string;
}) {
  const statistics = [
    {
      id: 'matches',
      label: 'Matches',
      value: pet.statistics.matches,
    },
    {
      id: 'likes-sent',
      label: 'Likes sent',
      value: pet.statistics.likesSent,
    },
  ];

  return (
    <section
      className="own-summary"
      aria-labelledby="own-pet-name"
    >
      <div className="own-name">
        <h1 id="own-pet-name">
          {pet.name}
        </h1>

        <ProfileEdit
          label="Edit pet name"
          onClick={onEdit}
        />
      </div>

      <p>
        {pet.ageYears} years old
        <span aria-hidden="true"> · </span>
        {pet.breed}
      </p>

      <PetLocation
        className="own-location"
        location={locationLabel}
      />

      <dl
        className="own-stats"
        aria-label="Pet profile statistics"
      >
        {statistics.map(stat => (
          <div key={stat.id}>
            <dt>{stat.label}</dt>
            <dd>{stat.value}</dd>
          </div>
        ))}
      </dl>

      <button
        type="button"
        className="own-complete"
        onClick={onEdit}
        disabled={!onEdit}
      >
        <PawPrint
          size={30}
          fill="currentColor"
        />

        <span>Complete your profile</span>

        <ChevronRight size={25} />
      </button>
    </section>
  );
}
