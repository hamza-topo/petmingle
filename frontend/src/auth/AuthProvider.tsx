import {
  createContext,
  type PropsWithChildren,
  useContext,
  useEffect,
  useMemo,
  useState,
} from 'react';
import { ApiError } from '../api/errors';
import {
  authenticatedIdentityRequest,
  signInRequest,
  signOutRequest,
} from './auth.api';
import type {
  AuthenticatedPet,
  AuthenticatedUser,
  SignInInput,
} from './auth.types';
import { tokenStorage } from './tokenStorage';

export type AuthStatus =
  | 'loading'
  | 'authenticated'
  | 'unauthenticated'
  | 'error';

export type AuthContextValue = {
  status: AuthStatus;
  user: AuthenticatedUser | null;
  pet: AuthenticatedPet | null;
  error: string | null;
  isAuthenticated: boolean;
  signIn: (input: SignInInput) => Promise<void>;
  signOut: () => Promise<void>;
};

const AuthContext = createContext<AuthContextValue | null>(null);

export function AuthProvider({ children }: PropsWithChildren) {
  const [status, setStatus] = useState<AuthStatus>('loading');
  const [user, setUser] = useState<AuthenticatedUser | null>(null);
  const [pet, setPet] = useState<AuthenticatedPet | null>(null);
  const [error, setError] = useState<string | null>(null);

  function clearIdentity() {
    setUser(null);
    setPet(null);
  }

  useEffect(() => {
    let cancelled = false;

    async function bootstrap() {
      const token = tokenStorage.get();

      if (!token) {
        if (!cancelled) {
          setStatus('unauthenticated');
        }

        return;
      }

      try {
        const identity = await authenticatedIdentityRequest(token);

        if (cancelled) {
          return;
        }

        setUser(identity.user);
        setPet(identity.pet);
        setError(null);
        setStatus('authenticated');
      } catch (caught) {
        if (cancelled) {
          return;
        }

        clearIdentity();

        if (caught instanceof ApiError && caught.status === 401) {
          tokenStorage.clear();
          setError(null);
          setStatus('unauthenticated');

          return;
        }

        setError(
          caught instanceof Error
            ? caught.message
            : 'Unable to restore the authenticated session.',
        );
        setStatus('error');
      }
    }

    void bootstrap();

    return () => {
      cancelled = true;
    };
  }, []);

  async function signIn(input: SignInInput): Promise<void> {
    setError(null);

    const authentication = await signInRequest(input);

    try {
      const identity = await authenticatedIdentityRequest(
        authentication.token,
      );

      tokenStorage.set(authentication.token);

      setUser(identity.user);
      setPet(identity.pet);
      setStatus('authenticated');
    } catch (caught) {
      /*
       * The backend already issued a token. If identity initialization fails,
       * attempt to revoke that token instead of knowingly leaving an unused
       * session behind.
       */
      try {
        await signOutRequest(authentication.token);
      } catch {
        // The original identity error remains the relevant failure.
      }

      tokenStorage.clear();
      clearIdentity();

      throw caught;
    }
  }

  async function signOut(): Promise<void> {
    const token = tokenStorage.get();

    if (!token) {
      clearIdentity();
      setError(null);
      setStatus('unauthenticated');

      return;
    }

    try {
      await signOutRequest(token);
    } catch (caught) {
      /*
       * An already-invalid token means the server considers the session gone.
       * Other failures are preserved so logout can be retried.
       */
      if (!(caught instanceof ApiError && caught.status === 401)) {
        throw caught;
      }
    }

    tokenStorage.clear();
    clearIdentity();
    setError(null);
    setStatus('unauthenticated');
  }

  const value = useMemo<AuthContextValue>(
    () => ({
      status,
      user,
      pet,
      error,
      isAuthenticated: status === 'authenticated',
      signIn,
      signOut,
    }),
    [status, user, pet, error],
  );

  return (
    <AuthContext.Provider value={value}>
      {children}
    </AuthContext.Provider>
  );
}

export function useAuth(): AuthContextValue {
  const context = useContext(AuthContext);

  if (!context) {
    throw new Error('useAuth must be used inside AuthProvider.');
  }

  return context;
}