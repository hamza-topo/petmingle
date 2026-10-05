import type { ReferenceAsset } from '../../assets/landingAssets';
import { apiRequest } from '../../api/client';
import { mediaUrl } from '../../api/config';

export type DiscoveryPet = {
  id: number;
  ownerId: number;
  speciesId: number;
  raceId: number;
  ownerName: string;
  name: string;
  breed: string;
  ageYears: number;
  sex: number | null;
  images: string[];
  photo: ReferenceAsset;
  photoCount: number;
  about: string | null;
  distanceKm: number;
  isNew: boolean;
};

type DiscoveryApiItem = {
  owner: {
    id: number;
    name: string;
  };
  pet: {
    id: number;
    owner_id: number;
    species_id: number;
    name: string;
    age_years: number;
    sex: number | null;
    race: {
      id: number;
      species_id: number;
      name: string;
    };
    images: string[];
    about: string | null;
  };
  distance_km: number;
  is_new: boolean;
};

export type DiscoveryPageMeta = {
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
};

type DiscoveryApiResponse = {
  success: true;
  message: string;
  data: DiscoveryApiItem[];
  meta: DiscoveryPageMeta;
  links: {
    first: string | null;
    last: string | null;
    prev: string | null;
    next: string | null;
  };
};

export type DiscoveryResult = {
  pets: DiscoveryPet[];
  meta: DiscoveryPageMeta;
};

export type DiscoveryFilterValue = {
  radiusKm: number;
  speciesId: number | null;
  raceId: number | null;
};

export const DEFAULT_DISCOVERY_FILTERS: DiscoveryFilterValue = {
  radiusKm: 5,
  speciesId: null,
  raceId: null,
};

export function discoveryFiltersEqual(
  left: DiscoveryFilterValue,
  right: DiscoveryFilterValue,
): boolean {
  return (
    left.radiusKm === right.radiusKm
    && left.speciesId === right.speciesId
    && left.raceId === right.raceId
  );
}

function isPositiveInteger(value: number): boolean {
  return Number.isInteger(value) && value > 0;
}

export function mapDiscoveryItem(
  item: DiscoveryApiItem,
): DiscoveryPet {
  if (
    !isPositiveInteger(item.owner.id)
    || !isPositiveInteger(item.pet.id)
    || !isPositiveInteger(item.pet.owner_id)
    || !isPositiveInteger(item.pet.species_id)
    || !isPositiveInteger(item.pet.race.id)
    || !isPositiveInteger(item.pet.race.species_id)
    || item.owner.id !== item.pet.owner_id
    || item.pet.species_id
      !== item.pet.race.species_id
    || !Number.isInteger(item.pet.age_years)
    || item.pet.age_years < 0
    || !Number.isFinite(item.distance_km)
    || item.distance_km < 0
  ) {
    throw new Error(
      'Discovery response contains inconsistent identifiers or card data.',
    );
  }

  const images = item.pet.images.filter(
    image =>
      typeof image === 'string'
      && image.trim() !== '',
  );

  const photoPath = images[0];

  return {
    id: item.pet.id,
    ownerId: item.owner.id,
    speciesId: item.pet.species_id,
    raceId: item.pet.race.id,
    ownerName: item.owner.name,
    name: item.pet.name,
    breed: item.pet.race.name,
    ageYears: item.pet.age_years,
    sex: item.pet.sex,
    images,
    photo: {
      src: photoPath
        ? mediaUrl(photoPath)
        : null,
      alt: photoPath
        ? `${item.pet.name} pet photo`
        : `${item.pet.name} pet profile`,
      placeholder: item.pet.name,
    },
    photoCount: images.length,
    about: item.pet.about?.trim() || null,
    distanceKm: item.distance_km,
    isNew: item.is_new,
  };
}

export async function discoveryRequest({
  token,
  radiusKm = 5,
  speciesId = null,
  raceId = null,
  page = 1,
  perPage = 24,
}: {
  token: string;
  radiusKm?: number;
  speciesId?: number | null;
  raceId?: number | null;
  page?: number;
  perPage?: number;
}): Promise<DiscoveryResult> {
  const requestBody: Record<string, number> = {
    radius_km: radiusKm,
    page,
    per_page: perPage,
  };

  if (speciesId !== null) {
    requestBody.species_id = speciesId;
  }

  if (raceId !== null) {
    requestBody.race_id = raceId;
  }

  const response =
    await apiRequest<DiscoveryApiResponse>(
      '/locations/nears',
      {
        method: 'POST',
        token,
        body: JSON.stringify(requestBody),
      },
    );

  return {
    pets: response.data.map(mapDiscoveryItem),
    meta: response.meta,
  };
}
