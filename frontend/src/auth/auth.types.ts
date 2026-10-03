export type SignInInput = {
  email: string;
  password: string;
};

export type SignInResponse = {
  success: true;
  token: string;
  token_type: 'Bearer';
};

export type AuthenticatedUser = {
  id: number;
  name: string;
  email: string;
};

export type AuthenticatedPet = {
  id: number;
  user_id: number;
  name: string;
};

export type AuthenticatedIdentity = {
  user: AuthenticatedUser;
  pet: AuthenticatedPet | null;
};

export type AuthenticatedIdentityResponse = {
  success: true;
  message: string;
  data: AuthenticatedIdentity;
};

export type SignOutResponse = {
  success: true;
  message: string;
};