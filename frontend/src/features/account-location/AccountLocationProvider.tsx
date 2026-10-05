import {
  createContext,
  type PropsWithChildren,
  useCallback,
  useContext,
  useEffect,
  useMemo,
  useState,
} from 'react';

import { ApiError } from '../../api/errors';
import { useAuth } from '../../auth/AuthProvider';
import { tokenStorage } from '../../auth/tokenStorage';
import {
  accountLocationsRequest,
  type AccountLocation,
  createAccountLocationRequest,
  formatAccountLocation,
  hasUsableCoordinates,
  type LocationCoordinates,
  updateAccountLocationRequest,
} from './location.api';

type AccountLocationStatus =
  | 'loading'
  | 'ready'
  | 'error';

export type AccountLocationContextValue = {
  status: AccountLocationStatus;
  locations: AccountLocation[];
  currentLocation:
    | (AccountLocation & {
        latitude: number;
        longitude: number;
      })
    | null;
  error: unknown | null;
  reload: () => Promise<void>;
  saveCoordinates: (
    coordinates: LocationCoordinates,
  ) => Promise<AccountLocation>;
};

const AccountLocationContext =
  createContext<AccountLocationContextValue | null>(
    null,
  );

export function AccountLocationProvider({
  children,
}: PropsWithChildren) {
  const { user } = useAuth();

  const [status, setStatus] =
    useState<AccountLocationStatus>('loading');
  const [locations, setLocations] = useState<
    AccountLocation[]
  >([]);
  const [error, setError] = useState<unknown | null>(
    null,
  );

  const currentLocation = useMemo(
    () =>
      locations.find(hasUsableCoordinates)
      ?? null,
    [locations],
  );

  const reload = useCallback(async () => {
    if (!user) {
      setLocations([]);
      setError(
        new ApiError(
          'Authenticated account is missing.',
          401,
        ),
      );
      setStatus('error');
      return;
    }

    const token = tokenStorage.get();

    if (!token) {
      setLocations([]);
      setError(
        new ApiError(
          'Authentication token is missing.',
          401,
        ),
      );
      setStatus('error');
      return;
    }

    setStatus('loading');
    setError(null);

    try {
      const loaded = await accountLocationsRequest({
        token,
        userId: user.id,
      });

      setLocations(loaded);
      setStatus('ready');
    } catch (caught) {
      setLocations([]);
      setError(caught);
      setStatus('error');
    }
  }, [user]);

  useEffect(() => {
    void reload();
  }, [reload]);

  const saveCoordinates = useCallback(
    async (
      coordinates: LocationCoordinates,
    ): Promise<AccountLocation> => {
      if (!user) {
        throw new ApiError(
          'Authenticated account is missing.',
          401,
        );
      }

      const token = tokenStorage.get();

      if (!token) {
        throw new ApiError(
          'Authentication token is missing.',
          401,
        );
      }

      const saved = currentLocation
        ? await updateAccountLocationRequest({
            token,
            userId: user.id,
            locationId: currentLocation.id,
            coordinates,
          })
        : await createAccountLocationRequest({
            token,
            userId: user.id,
            coordinates,
          });

      setLocations(current => [
        saved,
        ...current.filter(
          location => location.id !== saved.id,
        ),
      ]);
      setError(null);
      setStatus('ready');

      return saved;
    },
    [currentLocation, user],
  );

  const value = useMemo<AccountLocationContextValue>(
    () => ({
      status,
      locations,
      currentLocation,
      error,
      reload,
      saveCoordinates,
    }),
    [
      status,
      locations,
      currentLocation,
      error,
      reload,
      saveCoordinates,
    ],
  );

  return (
    <AccountLocationContext.Provider value={value}>
      {children}
    </AccountLocationContext.Provider>
  );
}

export function useAccountLocation(): AccountLocationContextValue {
  const context = useContext(AccountLocationContext);

  if (!context) {
    throw new Error(
      'useAccountLocation must be used inside AccountLocationProvider.',
    );
  }

  return context;
}

export function accountLocationLabel(
  value: Pick<
    AccountLocationContextValue,
    'status' | 'currentLocation'
  >,
): string {
  if (value.status === 'loading') {
    return 'Loading location…';
  }

  if (value.status === 'error') {
    return 'Location unavailable';
  }

  if (!value.currentLocation) {
    return 'Location not set';
  }

  return formatAccountLocation(value.currentLocation);
}
