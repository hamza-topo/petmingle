export type Species = {
  id: number;
  name: string;
  description: string | null;
};

export type Race = {
  id: number;
  species_id: number;
  name: string;
};

export type ApiEnvelope<T> = {
  success: true;
  message: string;
  data: T;
};

export type Taxonomy = {
  species: Species[];
  races: Race[];
};