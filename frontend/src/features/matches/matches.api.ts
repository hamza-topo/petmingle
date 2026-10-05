import type { ReferenceAsset } from '../../assets/landingAssets';
import { apiRequest } from '../../api/client';
import { mediaUrl } from '../../api/config';

type ApiEnvelope<T> = {
  success: true;
  message: string;
  data: T;
};

type RelationshipApiItem = {
  id: number;
  from_pet_id: number;
  to_pet_id: number;
};

type PetApiItem = {
  id: number;
  name: string;
  age: number;
  sexe: number | null;
  images: string[];
  about: string | null;
};

export type RelationshipKind = 'match' | 'mismatch';

export type RelationshipCard = {
  relationshipId: number;
  kind: RelationshipKind;
  sourcePetId: number;
  targetPetId: number;
  name: string;
  ageYears: number;
  sex: number | null;
  photo: ReferenceAsset;
  about: string | null;
};

export type RelationshipsResult = {
  matches: RelationshipCard[];
  mismatches: RelationshipCard[];
};

function isPositiveInteger(value: number): boolean {
  return Number.isInteger(value) && value > 0;
}

function validateRelationship(
  relationship: RelationshipApiItem,
  currentPetId: number,
): RelationshipApiItem {
  if (
    !isPositiveInteger(relationship.id)
    || !isPositiveInteger(relationship.from_pet_id)
    || !isPositiveInteger(relationship.to_pet_id)
    || relationship.from_pet_id !== currentPetId
    || relationship.to_pet_id === currentPetId
  ) {
    throw new Error(
      'Relationship response does not preserve authenticated Pet ID semantics.',
    );
  }

  return relationship;
}

function relationshipPhoto(pet: PetApiItem): ReferenceAsset {
  const photoPath = pet.images.find(
    image =>
      typeof image === 'string'
      && image.trim() !== '',
  );

  return {
    src: photoPath ? mediaUrl(photoPath) : null,
    alt: photoPath
      ? `${pet.name} pet photo`
      : `${pet.name} pet profile`,
    placeholder: pet.name,
  };
}

function mapRelationship(
  relationship: RelationshipApiItem,
  pet: PetApiItem,
  kind: RelationshipKind,
): RelationshipCard {
  if (
    pet.id !== relationship.to_pet_id
    || !isPositiveInteger(pet.id)
    || typeof pet.name !== 'string'
    || pet.name.trim() === ''
    || !Number.isInteger(pet.age)
    || pet.age < 0
    || !Array.isArray(pet.images)
  ) {
    throw new Error(
      'Relationship pet response does not match the persisted target Pet ID.',
    );
  }

  return {
    relationshipId: relationship.id,
    kind,
    sourcePetId: relationship.from_pet_id,
    targetPetId: relationship.to_pet_id,
    name: pet.name,
    ageYears: pet.age,
    sex: pet.sexe,
    photo: relationshipPhoto(pet),
    about: pet.about?.trim() || null,
  };
}

export async function relationshipsRequest({
  token,
  currentPetId,
}: {
  token: string;
  currentPetId: number;
}): Promise<RelationshipsResult> {
  if (!isPositiveInteger(currentPetId)) {
    throw new Error(
      'Authenticated Pet ID is required to load relationships.',
    );
  }

  const [matchesResponse, mismatchesResponse] =
    await Promise.all([
      apiRequest<ApiEnvelope<RelationshipApiItem[]>>(
        '/matches',
        { token },
      ),
      apiRequest<ApiEnvelope<RelationshipApiItem[]>>(
        '/mismatches',
        { token },
      ),
    ]);

  const matches = matchesResponse.data.map(
    relationship =>
      validateRelationship(
        relationship,
        currentPetId,
      ),
  );

  const mismatches = mismatchesResponse.data.map(
    relationship =>
      validateRelationship(
        relationship,
        currentPetId,
      ),
  );

  const targetPetIds = [
    ...new Set(
      [...matches, ...mismatches].map(
        relationship =>
          relationship.to_pet_id,
      ),
    ),
  ];

  const pets = await Promise.all(
    targetPetIds.map(async targetPetId => {
      const response =
        await apiRequest<ApiEnvelope<PetApiItem>>(
          `/pets/${targetPetId}`,
          { token },
        );

      if (response.data.id !== targetPetId) {
        throw new Error(
          'Relationship pet lookup returned a different Pet ID.',
        );
      }

      return [targetPetId, response.data] as const;
    }),
  );

  const petsById = new Map(pets);

  function hydrate(
    relationship: RelationshipApiItem,
    kind: RelationshipKind,
  ): RelationshipCard {
    const targetPet =
      petsById.get(relationship.to_pet_id);

    if (!targetPet) {
      throw new Error(
        'Relationship target pet could not be resolved.',
      );
    }

    return mapRelationship(
      relationship,
      targetPet,
      kind,
    );
  }

  return {
    matches: matches.map(relationship =>
      hydrate(relationship, 'match'),
    ),
    mismatches: mismatches.map(relationship =>
      hydrate(relationship, 'mismatch'),
    ),
  };
}
