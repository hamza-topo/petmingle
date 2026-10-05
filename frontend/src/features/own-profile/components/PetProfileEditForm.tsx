import {
  type FormEvent,
  useEffect,
  useMemo,
  useState,
} from 'react';

import { ApiError } from '../../../api/errors';
import { describeApiFailure } from '../../../api/presentation';
import { tokenStorage } from '../../../auth/tokenStorage';
import { ApiState } from '../../../components/ApiState';
import { taxonomyRequest } from '../../profile-creation/taxonomy.api';
import type { Taxonomy } from '../../profile-creation/taxonomy.types';
import {
  updatePetProfileRequest,
  type PetProfileUpdateInput,
} from '../profile.api';
import type { CurrentPetProfile } from '../profile.types';

type EditableField =
  | 'name'
  | 'speciesId'
  | 'raceId'
  | 'ageYears'
  | 'biography';

type FieldErrors = Partial<
  Record<EditableField, string>
>;

const serverFieldMap: Record<
  string,
  EditableField
> = {
  name: 'name',
  species_id: 'speciesId',
  race_id: 'raceId',
  age: 'ageYears',
  about: 'biography',
};

export function PetProfileEditForm({
  pet,
  onCancel,
  onSaved,
}: {
  pet: CurrentPetProfile;
  onCancel: () => void;
  onSaved: () => Promise<void> | void;
}) {
  const [name, setName] = useState(pet.name);
  const [speciesId, setSpeciesId] = useState(
    String(pet.speciesId),
  );
  const [raceId, setRaceId] = useState(
    String(pet.raceId),
  );
  const [ageYears, setAgeYears] = useState(
    String(pet.ageYears),
  );
  const [biography, setBiography] = useState(
    pet.biography === 'No biography yet.'
      ? ''
      : pet.biography,
  );

  const [taxonomy, setTaxonomy] =
    useState<Taxonomy | null>(null);
  const [taxonomyError, setTaxonomyError] =
    useState<unknown | null>(null);
  const [taxonomyReloadKey, setTaxonomyReloadKey] =
    useState(0);
  const [fieldErrors, setFieldErrors] =
    useState<FieldErrors>({});
  const [formError, setFormError] =
    useState<string | null>(null);
  const [submitting, setSubmitting] = useState(false);

  useEffect(() => {
    let cancelled = false;

    async function loadTaxonomy() {
      setTaxonomyError(null);

      const token = tokenStorage.get();

      if (!token) {
        setTaxonomyError(
          new ApiError(
            'Authentication token is missing.',
            401,
          ),
        );
        return;
      }

      try {
        const result = await taxonomyRequest(token);

        if (!cancelled) {
          setTaxonomy(result);
        }
      } catch (caught) {
        if (!cancelled) {
          setTaxonomyError(caught);
        }
      }
    }

    void loadTaxonomy();

    return () => {
      cancelled = true;
    };
  }, [taxonomyReloadKey]);

  const availableRaces = useMemo(() => {
    if (!taxonomy || !speciesId) {
      return [];
    }

    return taxonomy.races.filter(
      race => race.species_id === Number(speciesId),
    );
  }, [speciesId, taxonomy]);

  const taxonomyFailure =
    taxonomyError !== null
      ? describeApiFailure(taxonomyError)
      : null;

  function validate(): PetProfileUpdateInput | null {
    const nextErrors: FieldErrors = {};
    const normalizedName = name.trim();
    const normalizedAge = Number(ageYears);
    const normalizedSpeciesId = Number(speciesId);
    const normalizedRaceId = Number(raceId);

    if (!normalizedName) {
      nextErrors.name = 'Enter your pet’s name.';
    } else if (normalizedName.length > 25) {
      nextErrors.name =
        'Pet name may not be longer than 25 characters.';
    }

    if (
      !speciesId
      || !Number.isInteger(normalizedSpeciesId)
      || normalizedSpeciesId <= 0
    ) {
      nextErrors.speciesId = 'Choose a species.';
    }

    if (
      !raceId
      || !Number.isInteger(normalizedRaceId)
      || normalizedRaceId <= 0
    ) {
      nextErrors.raceId = 'Choose a breed.';
    }

    if (
      !ageYears.trim()
      || !Number.isInteger(normalizedAge)
      || normalizedAge < 0
      || normalizedAge > 30
    ) {
      nextErrors.ageYears =
        'Age must be between 0 and 30 years.';
    }

    setFieldErrors(nextErrors);

    if (Object.keys(nextErrors).length > 0) {
      return null;
    }

    return {
      speciesId: normalizedSpeciesId,
      raceId: normalizedRaceId,
      name: normalizedName,
      ageYears: normalizedAge,
      biography,
    };
  }

  function mapServerValidation(
    caught: ApiError,
  ): boolean {
    const validationErrors =
      caught.status === 422
        ? caught.payload?.errors
        : undefined;

    if (!validationErrors) {
      return false;
    }

    const nextErrors: FieldErrors = {};

    for (const [serverField, messages] of Object.entries(
      validationErrors,
    )) {
      const field = serverFieldMap[serverField];

      if (field && messages[0]) {
        nextErrors[field] = messages[0];
      }
    }

    if (Object.keys(nextErrors).length === 0) {
      return false;
    }

    setFieldErrors(nextErrors);
    return true;
  }

  async function handleSubmit(
    event: FormEvent<HTMLFormElement>,
  ) {
    event.preventDefault();

    if (submitting) {
      return;
    }

    setFormError(null);

    const input = validate();

    if (!input) {
      return;
    }

    const token = tokenStorage.get();

    if (!token) {
      setFormError(
        'Your session is no longer valid. Sign in again to continue.',
      );
      return;
    }

    setSubmitting(true);

    try {
      await updatePetProfileRequest({
        petId: pet.id,
        token,
        input,
      });

      await onSaved();
    } catch (caught) {
      if (
        caught instanceof ApiError
        && mapServerValidation(caught)
      ) {
        return;
      }

      setFormError(
        describeApiFailure(caught).message,
      );
    } finally {
      setSubmitting(false);
    }
  }

  return (
    <section
      className="own-profile-edit-panel"
      aria-labelledby="own-profile-edit-title"
    >
      <div className="own-profile-edit-heading">
        <div>
          <h2 id="own-profile-edit-title">
            Edit pet profile
          </h2>
          <p>
            Update the profile fields currently supported
            by PetMingle.
          </p>
        </div>

        <button
          type="button"
          className="own-edit-cancel"
          onClick={onCancel}
          disabled={submitting}
        >
          Cancel
        </button>
      </div>

      {taxonomyFailure ? (
        <ApiState
          kind="error"
          compact
          title="We couldn’t load pet taxonomy"
          message={taxonomyFailure.message}
          onRetry={
            taxonomyFailure.retryable
              ? () =>
                  setTaxonomyReloadKey(
                    current => current + 1,
                  )
              : undefined
          }
        />
      ) : !taxonomy ? (
        <ApiState
          kind="loading"
          compact
          message="Loading editable profile fields..."
        />
      ) : (
        <form
          className="own-profile-edit-form"
          aria-label="Edit pet profile"
          noValidate
          onSubmit={event => void handleSubmit(event)}
        >
          <label>
            <span>Pet name</span>
            <input
              value={name}
              maxLength={25}
              onChange={event => {
                setName(event.target.value);
                setFieldErrors(current => ({
                  ...current,
                  name: undefined,
                }));
              }}
              aria-invalid={!!fieldErrors.name}
              aria-describedby={
                fieldErrors.name
                  ? 'profile-edit-name-error'
                  : undefined
              }
            />
            {fieldErrors.name && (
              <small
                id="profile-edit-name-error"
                role="alert"
              >
                {fieldErrors.name}
              </small>
            )}
          </label>

          <label>
            <span>Species</span>
            <select
              value={speciesId}
              onChange={event => {
                setSpeciesId(event.target.value);
                setRaceId('');
                setFieldErrors(current => ({
                  ...current,
                  speciesId: undefined,
                  raceId: undefined,
                }));
              }}
              aria-invalid={!!fieldErrors.speciesId}
            >
              {taxonomy.species.map(species => (
                <option
                  key={species.id}
                  value={species.id}
                >
                  {species.name}
                </option>
              ))}
            </select>
            {fieldErrors.speciesId && (
              <small role="alert">
                {fieldErrors.speciesId}
              </small>
            )}
          </label>

          <label>
            <span>Breed</span>
            <select
              value={raceId}
              onChange={event => {
                setRaceId(event.target.value);
                setFieldErrors(current => ({
                  ...current,
                  raceId: undefined,
                }));
              }}
              disabled={availableRaces.length === 0}
              aria-invalid={!!fieldErrors.raceId}
            >
              {availableRaces.length === 0 && (
                <option value="">
                  No breeds available
                </option>
              )}
              {availableRaces.map(race => (
                <option
                  key={race.id}
                  value={race.id}
                >
                  {race.name}
                </option>
              ))}
            </select>
            {fieldErrors.raceId && (
              <small role="alert">
                {fieldErrors.raceId}
              </small>
            )}
          </label>

          <label>
            <span>Age</span>
            <input
              type="number"
              min="0"
              max="30"
              value={ageYears}
              onChange={event => {
                setAgeYears(event.target.value);
                setFieldErrors(current => ({
                  ...current,
                  ageYears: undefined,
                }));
              }}
              aria-invalid={!!fieldErrors.ageYears}
            />
            {fieldErrors.ageYears && (
              <small role="alert">
                {fieldErrors.ageYears}
              </small>
            )}
          </label>

          <label className="own-profile-edit-about">
            <span>Biography</span>
            <textarea
              rows={4}
              value={biography}
              onChange={event => {
                setBiography(event.target.value);
                setFieldErrors(current => ({
                  ...current,
                  biography: undefined,
                }));
              }}
              aria-invalid={!!fieldErrors.biography}
            />
            {fieldErrors.biography && (
              <small role="alert">
                {fieldErrors.biography}
              </small>
            )}
          </label>

          <p className="own-profile-edit-deferred">
            Location, weight, traits, compatibility,
            activities and profile statistics are not
            editable yet because they do not currently
            have an approved persistence contract.
          </p>

          {formError && (
            <ApiState
              kind="error"
              compact
              message={formError}
            />
          )}

          <div className="own-profile-edit-actions">
            <button
              type="submit"
              disabled={submitting}
            >
              {submitting
                ? 'Saving profile...'
                : 'Save changes'}
            </button>

            <button
              type="button"
              onClick={onCancel}
              disabled={submitting}
            >
              Cancel
            </button>
          </div>
        </form>
      )}
    </section>
  );
}
