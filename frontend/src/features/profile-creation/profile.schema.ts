import { z } from 'zod';

export const traits = [
  'Playful',
  'Friendly',
  'Outdoorsy',
  'Gentle',
  'Curious',
  'Social',
] as const;

export const photoSchema = z.instanceof(File)
  .refine(
    file => ['image/jpeg', 'image/png'].includes(file.type),
    'Choose a JPG or PNG image.',
  )
  .refine(
    file => file.size <= 10 * 1024 * 1024,
    'Choose an image no larger than 10MB.',
  );

export const profileSchema = z.object({
  name: z.string().trim().min(1, 'Enter your pet’s name.'),
  speciesId: z.string().trim().min(1, 'Choose a species.'),
  raceId: z.string().trim().min(1, 'Choose a breed.'),
  age: z.string().regex(/^\d+$/, 'Choose an age.'),
  size: z.enum(['Small', 'Medium', 'Large']),
  traits: z.array(z.enum(traits)),
  energy: z.enum([
    'Low energy',
    'Moderate energy',
    'High energy',
  ]),
  playdate: z.enum([
    'Active play',
    'Gentle play',
    'Relaxed walks',
  ]),
  photo: photoSchema.nullable(),
});

export type PetProfileValues = z.infer<typeof profileSchema>;

export const initialProfile: PetProfileValues = {
  name: '',
  speciesId: '',
  raceId: '',
  age: '',
  size: 'Large',
  traits: ['Playful'],
  energy: 'High energy',
  playdate: 'Active play',
  photo: null,
};