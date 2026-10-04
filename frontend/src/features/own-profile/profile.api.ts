import { apiRequest } from '../../api/client';
import type {
  ApiEnvelope,
  CurrentPetProfile,
  PetApiRecord,
  RaceApiRecord,
} from './profile.types';

type CurrentPetProfileRequest = {
  petId: number;
  userId: number;
  token: string;
};

export async function currentPetProfileRequest({
  petId,
  userId,
  token,
}: CurrentPetProfileRequest): Promise<CurrentPetProfile> {
  const petResponse = await apiRequest<ApiEnvelope<PetApiRecord>>(
    `/pets/${petId}`,
    {
      method: 'GET',
      token,
    },
  );

  const pet = petResponse.data;

  if (pet.id !== petId || pet.user_id !== userId) {
    throw new Error('Current pet identity does not match the authenticated account.');
  }

  const raceResponse = await apiRequest<ApiEnvelope<RaceApiRecord>>(
    `/races/${pet.race_id}`,
    {
      method: 'GET',
      token,
    },
  );

  const race = raceResponse.data;

  if (
    race.id !== pet.race_id
    || race.species_id !== pet.species_id
  ) {
    throw new Error('Current pet taxonomy does not match the pet record.');
  }

  return {
    id: pet.id,
    userId: pet.user_id,
    speciesId: pet.species_id,
    raceId: pet.race_id,
    name: pet.name,
    ageYears: pet.age,
    breed: race.name,
    biography: pet.about?.trim() || 'No biography yet.',
  };
}
