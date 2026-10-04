import { useEffect, useMemo, useState } from 'react';
import {
  Controller,
  useForm,
  useWatch,
} from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import {
  ArrowRight,
  CalendarDays,
  ChevronDown,
  Dog,
  Heart,
  PawPrint,
  Search,
  Sun,
  TreePine,
  Users,
  Zap,
} from 'lucide-react';
import type { LucideIcon } from 'lucide-react';
import { useNavigate } from 'react-router';

import { ApiError } from '../../api/errors';
import { describeApiFailure } from '../../api/presentation';
import { useAuth } from '../../auth/AuthProvider';
import { tokenStorage } from '../../auth/tokenStorage';
import { ActionButton } from '../../components/Action';
import { ApiState } from '../../components/ApiState';
import { SiteHeader } from '../../components/SiteHeader';
import { FormField } from './components/FormField';
import { PhotoUploader } from './components/PhotoUploader';
import { ProfileProgress } from './components/ProfileProgress';
import { petCreateRequest } from './pet-create.api';
import {
  initialProfile,
  profileSchema,
  traits,
  type PetProfileValues,
} from './profile.schema';
import { taxonomyRequest } from './taxonomy.api';
import type { Taxonomy } from './taxonomy.types';

const traitIcons = [
  PawPrint,
  Users,
  TreePine,
  Heart,
  Search,
  Sun,
];

type SelectField =
  | 'speciesId'
  | 'raceId'
  | 'age'
  | 'size'
  | 'energy'
  | 'playdate';

type SelectOption = [string, string];

const serverFieldMap: Record<
  string,
  keyof PetProfileValues
> = {
  species_id: 'speciesId',
  race_id: 'raceId',
  name: 'name',
  age: 'age',
  image: 'photo',
};

export function PetCreatePage() {
  const { user, refreshIdentity } = useAuth();
  const navigate = useNavigate();
  const [submitting, setSubmitting] = useState(false);
  const [creationCompleted, setCreationCompleted] =
    useState(false);
  const [submitError, setSubmitError] =
    useState<string | null>(null);

  const [taxonomy, setTaxonomy] = useState<Taxonomy>({
    species: [],
    races: [],
  });

  const [taxonomyLoading, setTaxonomyLoading] =
    useState(true);

  const [taxonomyError, setTaxonomyError] =
    useState<unknown | null>(null);

  const [taxonomyReloadKey, setTaxonomyReloadKey] =
    useState(0);

  const {
    register,
    control,
    handleSubmit,
    setValue,
    setError,
    clearErrors,
    formState: { errors },
  } = useForm<PetProfileValues>({
    resolver: zodResolver(profileSchema),
    defaultValues: initialProfile,
  });

  const name = useWatch({
    control,
    name: 'name',
  });

  const speciesId = useWatch({
    control,
    name: 'speciesId',
  });

  useEffect(() => {
    let cancelled = false;

    async function loadTaxonomy() {
      setTaxonomyLoading(true);
      setTaxonomyError(null);

      const token = tokenStorage.get();

      if (!token) {
        if (!cancelled) {
          setTaxonomyError(
            new ApiError(
              'Authentication token is missing.',
              401,
            ),
          );
          setTaxonomyLoading(false);
        }

        return;
      }

      try {
        const result = await taxonomyRequest(token);

        if (cancelled) {
          return;
        }

        setTaxonomy(result);
        setTaxonomyError(null);
      } catch (caught) {
        if (!cancelled) {
          setTaxonomyError(caught);
        }
      } finally {
        if (!cancelled) {
          setTaxonomyLoading(false);
        }
      }
    }

    void loadTaxonomy();

    return () => {
      cancelled = true;
    };
  }, [taxonomyReloadKey]);

  const taxonomyFailure =
    taxonomyError !== null
      ? describeApiFailure(taxonomyError)
      : null;

  const availableRaces = useMemo(() => {
    if (!speciesId) {
      return [];
    }

    const selectedSpeciesId = Number(speciesId);

    return taxonomy.races.filter(
      race =>
        race.species_id === selectedSpeciesId,
    );
  }, [speciesId, taxonomy.races]);

  useEffect(() => {
    setValue('raceId', '');
  }, [speciesId, setValue]);

  const options = (
    items: string[],
  ): SelectOption[] =>
    items.map(item => [item, item]);

  const speciesOptions: SelectOption[] =
    taxonomy.species.map(species => [
      String(species.id),
      species.name,
    ]);

  const raceOptions: SelectOption[] =
    availableRaces.map(race => [
      String(race.id),
      race.name,
    ]);

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

    let mapped = false;

    for (const [serverField, messages] of Object.entries(
      validationErrors,
    )) {
      const field = serverFieldMap[serverField];

      if (!field || !messages[0]) {
        continue;
      }

      setError(field, {
        type: 'server',
        message: messages[0],
      });

      mapped = true;
    }

    return mapped;
  }

  async function submitPet(
    values: PetProfileValues,
  ): Promise<void> {
    if (submitting || creationCompleted) {
      return;
    }

    setSubmitError(null);
    clearErrors([
      'name',
      'speciesId',
      'raceId',
      'age',
      'photo',
    ]);

    const token = tokenStorage.get();

    if (!token) {
      setSubmitError(
        'Your session is no longer valid. Sign in again to continue.',
      );
      return;
    }

    setSubmitting(true);

    try {
      await petCreateRequest(
        {
          speciesId: Number(values.speciesId),
          raceId: Number(values.raceId),
          name: values.name,
          age: Number(values.age),
          photo: values.photo,
        },
        token,
      );
    } catch (caught) {
      if (
        caught instanceof ApiError
        && mapServerValidation(caught)
      ) {
        setSubmitting(false);
        return;
      }

      setSubmitError(
        describeApiFailure(caught).message,
      );
      setSubmitting(false);
      return;
    }

    setCreationCompleted(true);

    try {
      await refreshIdentity();
      navigate('/profile', { replace: true });
    } catch {
      setSubmitError(
        'Your pet profile was created, but PetMingle could not refresh your session. Refresh the page before trying again.',
      );
    } finally {
      setSubmitting(false);
    }
  }

  const select = (
    field: SelectField,
    label: string,
    icon: LucideIcon,
    selectOptions: SelectOption[],
    required = false,
    placeholder?: string,
    disabled = false,
  ) => (
    <FormField
      id={field}
      label={label}
      icon={icon}
      required={required}
      error={errors[field]?.message}
    >
      <select
        id={field}
        {...register(field)}
        disabled={disabled}
        aria-required={required}
        aria-invalid={!!errors[field]}
        aria-describedby={
          errors[field]
            ? `${field}-error`
            : undefined
        }
      >
        {placeholder && (
          <option value="">
            {placeholder}
          </option>
        )}

        {selectOptions.map(([value, text]) => (
          <option
            key={value}
            value={value}
          >
            {text}
          </option>
        ))}
      </select>

      <ChevronDown
        className="pet-select-chevron"
        size={20}
        aria-hidden="true"
      />
    </FormField>
  );

  return (
    <div className="pet-create-page">
      <a
        href="#pet-create"
        className="skip-link"
      >
        Skip to pet profile form
      </a>

      <SiteHeader
        identity={
          user
            ? {
                name: user.name,
                kind: 'account',
              }
            : undefined
        }
      />

      <main
        id="pet-create"
        className="pet-create-layout"
      >
        <ProfileProgress />

        <form
          aria-label="Create pet profile"
          noValidate
          onChange={() => setSubmitError(null)}
          onSubmit={handleSubmit(values => {
            void submitPet(values);
          })}
        >
          <section
            className="pet-form-section pet-photo-section"
            aria-labelledby="photo-title"
          >
            <h2 id="photo-title">
              Add a photo
            </h2>

            <p>
              A clear, happy photo helps your pet
              make friends!
            </p>

            <Controller
              control={control}
              name="photo"
              render={({ field }) => (
                <PhotoUploader
                  value={field.value}
                  onChange={file => {
                    field.onChange(file);
                    setSubmitError(null);
                  }}
                  error={errors.photo?.message}
                />
              )}
            />
          </section>

          <section
            className="pet-form-section pet-basic-section"
            aria-labelledby="basic-title"
          >
            <h2 id="basic-title">
              Basic information
            </h2>

            <p>
              Tell us about your pet.
            </p>

            <div className="pet-field-grid">
              <FormField
                id="name"
                label="Pet name"
                required
                error={errors.name?.message}
              >
                <input
                  id="name"
                  {...register('name')}
                  aria-required="true"
                  aria-invalid={!!errors.name}
                  aria-describedby={
                    errors.name
                      ? 'name-error'
                      : undefined
                  }
                />
              </FormField>

              {select(
                'speciesId',
                'Species',
                PawPrint,
                speciesOptions,
                true,
                taxonomyLoading
                  ? 'Loading species...'
                  : 'Choose a species',
                taxonomyLoading
                  || !!taxonomyFailure
                  || taxonomy.species.length === 0,
              )}

              {select(
                'raceId',
                'Breed',
                Dog,
                raceOptions,
                true,
                taxonomyLoading
                  ? 'Loading breeds...'
                  : !speciesId
                    ? 'Choose a species first'
                    : availableRaces.length === 0
                      ? 'No breeds available'
                      : 'Choose a breed',
                taxonomyLoading
                  || !!taxonomyFailure
                  || !speciesId
                  || availableRaces.length === 0,
              )}

              {select(
                'age',
                'Age',
                CalendarDays,
                Array.from(
                  { length: 31 },
                  (_, age): SelectOption => [
                    String(age),
                    age === 0
                      ? 'Under 1 year'
                      : `${age} ${
                        age === 1
                          ? 'year'
                          : 'years'
                      }`,
                  ],
                ),
                true,
              )}

              {select(
                'size',
                'Size',
                Dog,
                [
                  [
                    'Small',
                    'Small (under 20 lbs)',
                  ],
                  [
                    'Medium',
                    'Medium (20–50 lbs)',
                  ],
                  [
                    'Large',
                    'Large (50+ lbs)',
                  ],
                ],
                true,
              )}
            </div>

            {taxonomyLoading && (
              <ApiState
                kind="loading"
                compact
                message="Loading pet taxonomy..."
              />
            )}

            {taxonomyFailure && (
              <ApiState
                kind="error"
                compact
                title="We couldn’t load species and breeds"
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
            )}

            {!taxonomyLoading
              && !taxonomyFailure
              && taxonomy.species.length === 0 && (
                <ApiState
                  kind="empty"
                  compact
                  message="No species are available."
                />
              )}

            {!taxonomyLoading
              && !taxonomyFailure
              && speciesId
              && availableRaces.length === 0 && (
                <ApiState
                  kind="empty"
                  compact
                  message="No breeds are available for the selected species."
                />
              )}
          </section>

          <section
            className="pet-form-section pet-personality-section"
            aria-labelledby="personality-title"
          >
            <h2 id="personality-title">
              Personality
            </h2>

            <p>
              Choose a few words that best describe
              your pet.
            </p>

            <div className="pet-trait-choices">
              {traits.map((trait, index) => {
                const Icon = traitIcons[index];

                return (
                  <label
                    key={trait}
                    className={`pet-choice pet-choice--${
                      index % 2
                        ? 'pink'
                        : 'blue'
                    }`}
                  >
                    <input
                      type="checkbox"
                      value={trait}
                      {...register('traits')}
                    />

                    <span>
                      <Icon
                        size={25}
                        aria-hidden="true"
                      />

                      {trait}
                    </span>
                  </label>
                );
              })}
            </div>
          </section>

          <section
            className="pet-form-section pet-preferences-section"
            aria-labelledby="preferences-title"
          >
            <div className="pet-preferences-heading">
              <h2 id="preferences-title">
                Playdate preferences
              </h2>

              <p>
                Help us find the best matches for{' '}
                {name.trim() || 'your pet'}.
              </p>
            </div>

            <div className="pet-field-grid">
              {select(
                'energy',
                'Energy level',
                Zap,
                options([
                  'Low energy',
                  'Moderate energy',
                  'High energy',
                ]),
              )}

              {select(
                'playdate',
                'Ideal playdate type',
                Users,
                options([
                  'Active play',
                  'Gentle play',
                  'Relaxed walks',
                ]),
              )}
            </div>

            <div className="pet-form-actions">
              <ActionButton
                type="submit"
                disabled={
                  taxonomyLoading
                  || !!taxonomyFailure
                  || taxonomy.species.length === 0
                  || submitting
                  || creationCompleted
                }
              >
                {submitting
                  ? 'Creating profile...'
                  : 'Continue'}

                <ArrowRight
                  size={27}
                  aria-hidden="true"
                />
              </ActionButton>

              <button
                type="button"
                disabled
                title="Saving is not available in this local preview"
              >
                Save and finish later
              </button>
            </div>

            {submitError && (
              <ApiState
                kind="error"
                compact
                message={submitError}
              />
            )}
          </section>
        </form>
      </main>
    </div>
  );
}