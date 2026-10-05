import { apiRequest } from '../../api/client';
import type { PetInteractionState } from './discovery.api';

type ApiEnvelope<T> = {
  success: true;
  message: string;
  data: T;
};

export type PersistedPetInteraction = {
  id: number;
  from_pet_id: number;
  to_pet_id: number;
  interaction: Exclude<PetInteractionState, null>;
};

export async function petInteractionRequest({
  token,
  targetPetId,
  interaction,
}: {
  token: string;
  targetPetId: number;
  interaction: Exclude<PetInteractionState, null>;
}): Promise<PersistedPetInteraction> {
  const endpoint =
    interaction === 'liked'
      ? '/likes'
      : '/dislikes';

  const response =
    await apiRequest<
      ApiEnvelope<PersistedPetInteraction>
    >(endpoint, {
      method: 'POST',
      token,
      body: JSON.stringify({
        to_pet_id: targetPetId,
      }),
    });

  if (
    response.data.to_pet_id !== targetPetId
    || response.data.interaction !== interaction
  ) {
    throw new Error(
      'Pet interaction response does not match the requested target.',
    );
  }

  return response.data;
}
