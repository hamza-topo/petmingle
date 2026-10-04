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

import { useAuth } from '../../auth/AuthProvider';
import { tokenStorage } from '../../auth/tokenStorage';
import { ActionButton } from '../../components/Action';
import { SiteHeader } from '../../components/SiteHeader';
import { FormField } from './components/FormField';
import { PhotoUploader } from './components/PhotoUploader';
import { ProfileProgress } from './components/ProfileProgress';
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

type PetCreatePageProps = {
  onLocalSubmit?: (values: PetProfileValues) => void;
};

export function PetCreatePage({
  onLocalSubmit,
}: PetCreatePageProps) {
  const { user } = useAuth();
  const [submitted, setSubmitted] = useState(false);

  const [taxonomy, setTaxonomy] = useState<Taxonomy>({
    species: [],
    races: [],
  });

  const [taxonomyLoading, setTaxonomyLoading] =
    useState(true);

  const [taxonomyError, setTaxonomyError] =
    useState<string | null>(null);

  const [taxonomyReloadKey, setTaxonomyReloadKey] =
    useState(0);

  const {
    register,
    control,
    handleSubmit,
    setValue,
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
            'Unable to load pet taxonomy.',
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
      } catch {
        if (!cancelled) {
          setTaxonomyError(
            'Unable to load pet taxonomy.',
          );
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
          onChange={() => setSubmitted(false)}
          onSubmit={handleSubmit(values => {
            onLocalSubmit?.(values);
            setSubmitted(true);
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
                    setSubmitted(false);
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
                  || !!taxonomyError
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
                  || !!taxonomyError
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
              <p role="status">
                Loading pet taxonomy...
              </p>
            )}

            {taxonomyError && (
              <div role="alert">
                <p>
                  Unable to load species and breeds.
                </p>

                <button
                  type="button"
                  onClick={() =>
                    setTaxonomyReloadKey(
                      current => current + 1,
                    )
                  }
                >
                  Try again
                </button>
              </div>
            )}

            {!taxonomyLoading
              && !taxonomyError
              && taxonomy.species.length === 0 && (
                <p role="status">
                  No species are available.
                </p>
              )}

            {!taxonomyLoading
              && !taxonomyError
              && speciesId
              && availableRaces.length === 0 && (
                <p role="status">
                  No breeds are available for the
                  selected species.
                </p>
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
                  || !!taxonomyError
                  || taxonomy.species.length === 0
                }
              >
                Continue

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

            {submitted && (
              <p
                className="pet-submit-status"
                role="status"
              >
                Pet details are valid.
              </p>
            )}
          </section>
        </form>
      </main>
    </div>
  );
}