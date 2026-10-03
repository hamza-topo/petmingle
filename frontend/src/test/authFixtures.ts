import type { AuthContextValue } from '../auth/AuthProvider';

export function authenticatedAuthState(): AuthContextValue {
  return {
    status: 'authenticated',
    user: {
      id: 10,
      name: 'Hamza',
      email: 'hamza@example.com',
    },
    pet: {
      id: 42,
      user_id: 10,
      name: 'Nala',
    },
    error: null,
    isAuthenticated: true,
    signIn: async () => {},
    signOut: async () => {},
  };
}