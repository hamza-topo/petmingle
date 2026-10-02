import { useState } from 'react';
import { Controller, useForm, useWatch } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { ArrowRight, CalendarDays, ChevronDown, Dog, Heart, PawPrint, Search, Sun, TreePine, Users, Zap } from 'lucide-react';
import type { LucideIcon } from 'lucide-react';
import { SiteHeader } from '../../components/SiteHeader';
import { ActionButton } from '../../components/Action';
import { FormField } from './components/FormField';
import { PhotoUploader } from './components/PhotoUploader';
import { ProfileProgress } from './components/ProfileProgress';
import { initialProfile, profileSchema, traits, type PetProfileValues } from './profile.schema';

const traitIcons = [PawPrint, Users, TreePine, Heart, Search, Sun];
export function PetCreatePage({ onLocalSubmit }: { onLocalSubmit?: (values: PetProfileValues) => void }) {
  const [submitted, setSubmitted] = useState(false);
  const { register, control, handleSubmit, formState: { errors } } = useForm<PetProfileValues>({
    resolver: zodResolver(profileSchema), defaultValues: initialProfile,
  });
  const name = useWatch({ control, name: 'name' });
  const select = (field: 'species' | 'breed' | 'age' | 'size' | 'energy' | 'playdate', label: string, icon: LucideIcon, options: [string, string][], required = false) => <FormField id={field} label={label} icon={icon} required={required} error={errors[field]?.message}>
    <select id={field} {...register(field)} aria-required={required} aria-invalid={!!errors[field]} aria-describedby={errors[field] ? `${field}-error` : undefined}>
      {options.map(([value, text]) => <option key={value} value={value}>{text}</option>)}
    </select><ChevronDown className="pet-select-chevron" size={20} aria-hidden="true" />
  </FormField>;
  const options = (items: string[]): [string, string][] => items.map(item => [item, item]);
  return <div className="pet-create-page">
    <a href="#pet-create" className="skip-link">Skip to pet profile form</a>
    <SiteHeader />
    <main id="pet-create" className="pet-create-layout">
      <ProfileProgress />
      <form aria-label="Create pet profile" noValidate onChange={() => setSubmitted(false)} onSubmit={handleSubmit(values => { onLocalSubmit?.(values); setSubmitted(true); })}>
        <section className="pet-form-section pet-photo-section" aria-labelledby="photo-title">
          <h2 id="photo-title">Add a photo</h2><p>A clear, happy photo helps your pet make friends!</p>
          <Controller control={control} name="photo" render={({ field }) => <PhotoUploader value={field.value} onChange={file => { field.onChange(file); setSubmitted(false); }} error={errors.photo?.message} />} />
        </section>
        <section className="pet-form-section pet-basic-section" aria-labelledby="basic-title">
          <h2 id="basic-title">Basic information</h2><p>Tell us about your pet.</p>
          <div className="pet-field-grid">
            <FormField id="name" label="Pet name" required error={errors.name?.message}><input id="name" {...register('name')} aria-required="true" aria-invalid={!!errors.name} aria-describedby={errors.name ? 'name-error' : undefined} /></FormField>
            {select('species', 'Species', PawPrint, options(['Dog', 'Cat', 'Other']), true)}
            {select('breed', 'Breed', Dog, options(['Golden Retriever', 'Labrador Retriever', 'Domestic Shorthair', 'Mixed breed', 'Other']), true)}
            {select('age', 'Age', CalendarDays, Array.from({ length: 31 }, (_, age) => [String(age), age === 0 ? 'Under 1 year' : `${age} ${age === 1 ? 'year' : 'years'}`]), true)}
            {select('size', 'Size', Dog, [['Small', 'Small (under 20 lbs)'], ['Medium', 'Medium (20–50 lbs)'], ['Large', 'Large (50+ lbs)']], true)}
          </div>
        </section>
        <section className="pet-form-section pet-personality-section" aria-labelledby="personality-title">
          <h2 id="personality-title">Personality</h2><p>Choose a few words that best describe your pet.</p>
          <div className="pet-trait-choices">{traits.map((trait, index) => { const Icon = traitIcons[index]; return <label key={trait} className={`pet-choice pet-choice--${index % 2 ? 'pink' : 'blue'}`}>
            <input type="checkbox" value={trait} {...register('traits')} /><span><Icon size={25} aria-hidden="true" />{trait}</span>
          </label>; })}</div>
        </section>
        <section className="pet-form-section pet-preferences-section" aria-labelledby="preferences-title">
          <div className="pet-preferences-heading"><h2 id="preferences-title">Playdate preferences</h2><p>Help us find the best matches for {name.trim() || 'your pet'}.</p></div>
          <div className="pet-field-grid">
            {select('energy', 'Energy level', Zap, options(['Low energy', 'Moderate energy', 'High energy']))}
            {select('playdate', 'Ideal playdate type', Users, options(['Active play', 'Gentle play', 'Relaxed walks']))}
          </div>
          <div className="pet-form-actions"><ActionButton type="submit">Continue <ArrowRight size={27} aria-hidden="true" /></ActionButton><button type="button" disabled title="Saving is not available in this local preview">Save and finish later</button></div>
          {submitted && <p className="pet-submit-status" role="status">Pet details are valid.</p>}
        </section>
      </form>
    </main>
  </div>;
}
