export type PetApiRecord = {
  id: number;
  user_id: number;
  species_id: number;
  race_id: number;
  name: string;
  age: number;
  sexe: number | null;
  color: string | null;
  images: string[];
  about: string | null;
};

export type RaceApiRecord = {
  id: number;
  species_id: number;
  name: string;
};

export type ApiEnvelope<T> = {
  success: true;
  message: string;
  data: T;
};

export type CurrentPetProfile = {
  id: number;
  userId: number;
  speciesId: number;
  raceId: number;
  name: string;
  ageYears: number;
  breed: string;
  biography: string;
  images: string[];
};
