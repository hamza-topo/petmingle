import { apiRequest } from '../../api/client';

export type AccountLocation = {
  id: number;
  user_id: number;
  latitude: number | null;
  longitude: number | null;
};

export type LocationCoordinates = {
  latitude: number;
  longitude: number;
};

type ApiEnvelope<T> = {
  success: true;
  message: string;
  data: T;
};

function assertOwnedLocation(
  location: AccountLocation,
  userId: number,
): AccountLocation {
  if (location.user_id !== userId) {
    throw new Error(
      'Account location does not belong to the authenticated user.',
    );
  }

  return location;
}

export async function accountLocationsRequest({
  token,
  userId,
}: {
  token: string;
  userId: number;
}): Promise<AccountLocation[]> {
  const response = await apiRequest<
    ApiEnvelope<AccountLocation[]>
  >('/locations', {
    method: 'GET',
    token,
  });

  return response.data
    .map(location =>
      assertOwnedLocation(location, userId),
    )
    .sort((left, right) => right.id - left.id);
}

export async function createAccountLocationRequest({
  token,
  userId,
  coordinates,
}: {
  token: string;
  userId: number;
  coordinates: LocationCoordinates;
}): Promise<AccountLocation> {
  const response = await apiRequest<
    ApiEnvelope<AccountLocation>
  >('/locations', {
    method: 'POST',
    token,
    body: JSON.stringify(coordinates),
  });

  return assertOwnedLocation(
    response.data,
    userId,
  );
}

export async function updateAccountLocationRequest({
  token,
  userId,
  locationId,
  coordinates,
}: {
  token: string;
  userId: number;
  locationId: number;
  coordinates: LocationCoordinates;
}): Promise<AccountLocation> {
  const response = await apiRequest<
    ApiEnvelope<AccountLocation>
  >(`/locations/${locationId}`, {
    method: 'PUT',
    token,
    body: JSON.stringify(coordinates),
  });

  return assertOwnedLocation(
    response.data,
    userId,
  );
}

export function hasUsableCoordinates(
  location: AccountLocation,
): location is AccountLocation & {
  latitude: number;
  longitude: number;
} {
  return (
    typeof location.latitude === 'number'
    && Number.isFinite(location.latitude)
    && typeof location.longitude === 'number'
    && Number.isFinite(location.longitude)
  );
}

export function formatAccountLocation(
  location: AccountLocation & {
    latitude: number;
    longitude: number;
  },
): string {
  return `${location.latitude.toFixed(6)}, ${location.longitude.toFixed(6)}`;
}
