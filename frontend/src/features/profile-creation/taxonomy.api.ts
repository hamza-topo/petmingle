import { apiRequest } from '../../api/client';
import type {
  ApiEnvelope,
  Race,
  Species,
  Taxonomy,
} from './taxonomy.types';

export async function speciesRequest(
  token: string,
): Promise<Species[]> {
  const response = await apiRequest<ApiEnvelope<Species[]>>(
    '/species',
    {
      method: 'GET',
      token,
    },
  );

  return response.data;
}

export async function racesRequest(
  token: string,
): Promise<Race[]> {
  const response = await apiRequest<ApiEnvelope<Race[]>>(
    '/races',
    {
      method: 'GET',
      token,
    },
  );

  return response.data;
}

export async function taxonomyRequest(
  token: string,
): Promise<Taxonomy> {
  const [species, races] = await Promise.all([
    speciesRequest(token),
    racesRequest(token),
  ]);

  return {
    species,
    races,
  };
}