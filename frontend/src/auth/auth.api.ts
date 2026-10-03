import { apiRequest } from '../api/client';
import type {
  AuthenticatedIdentity,
  AuthenticatedIdentityResponse,
  SignInInput,
  SignInResponse,
  SignOutResponse,
} from './auth.types';

export async function signInRequest(
  input: SignInInput,
): Promise<SignInResponse> {
  return apiRequest<SignInResponse>('/sign-in', {
    method: 'POST',
    body: JSON.stringify(input),
  });
}

export async function authenticatedIdentityRequest(
  token: string,
): Promise<AuthenticatedIdentity> {
  const response = await apiRequest<AuthenticatedIdentityResponse>(
    '/me',
    {
      method: 'GET',
      token,
    },
  );

  return response.data;
}

export async function signOutRequest(
  token: string,
): Promise<void> {
  await apiRequest<SignOutResponse>('/sign-out', {
    method: 'POST',
    token,
  });
}