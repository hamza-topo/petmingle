import { apiRequest } from '../../api/client';

export type CreatePetInput = {
  speciesId: number;
  raceId: number;
  name: string;
  age: number;
  photo: File | null;
};

export type CreatedPet = {
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

type CreatePetResponse = {
  success: true;
  message: string;
  data: CreatedPet;
};

export async function petCreateRequest(
  input: CreatePetInput,
  token: string,
): Promise<CreatedPet> {
  const body = new FormData();

  body.set('species_id', String(input.speciesId));
  body.set('race_id', String(input.raceId));
  body.set('name', input.name.trim());
  body.set('age', String(input.age));

  if (input.photo) {
    body.set('image', input.photo);
  }

  const response = await apiRequest<CreatePetResponse>(
    '/pets',
    {
      method: 'POST',
      token,
      body,
    },
  );

  return response.data;
}
