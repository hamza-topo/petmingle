import { PetTraitBadge } from '../../../components/PetTraitBadge';
import { ownPet, profileDetails } from '../profile.fixtures';
import type { CurrentPetProfile } from '../profile.types';
import { ProfileEdit } from './PetProfileSummary';

export function ProfileAbout({
  pet,
  onEdit,
}: {
  pet: CurrentPetProfile;
  onEdit?: () => void;
}) {
  return (
    <section
      className="own-about"
      aria-labelledby="own-about-title"
    >
      <div className="own-section-heading">
        <h2 id="own-about-title">
          About {pet.name}
        </h2>

        <ProfileEdit
          label="Edit pet biography"
          onClick={onEdit}
        />
      </div>

      <p>{pet.biography}</p>

      <div className="own-traits">
        {ownPet.traits.map(trait => (
          <PetTraitBadge
            key={trait.label}
            trait={trait}
          />
        ))}
      </div>
    </section>
  );
}

export function ProfileDetails({
  pet,
  onEdit,
}: {
  pet: CurrentPetProfile;
  onEdit?: () => void;
}) {
  const details = profileDetails.map(column =>
    column.map(detail => {
      if (detail.label === 'Age') {
        return {
          ...detail,
          value: `${pet.ageYears} years old`,
        };
      }

      if (detail.label === 'Breed') {
        return {
          ...detail,
          value: pet.breed,
        };
      }

      return detail;
    }),
  );

  return (
    <section
      className="own-details"
      aria-labelledby="own-details-title"
    >
      <div className="own-section-heading">
        <h2 id="own-details-title">
          Details
        </h2>

        <ProfileEdit
          label="Edit pet details"
          onClick={onEdit}
        />
      </div>

      <div className="own-details-columns">
        {details.map((column, index) => (
          <dl key={index}>
            {column.map(
              ({
                label,
                value,
                icon: Icon,
              }) => (
                <div key={label}>
                  <dt>
                    <Icon
                      size={25}
                      aria-hidden="true"
                    />
                    <span>{label}</span>
                  </dt>

                  <dd>{value}</dd>
                </div>
              ),
            )}
          </dl>
        ))}
      </div>
    </section>
  );
}
