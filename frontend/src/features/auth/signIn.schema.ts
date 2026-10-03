import { z } from 'zod';

export const signInSchema = z.object({
  email: z
    .string()
    .trim()
    .min(1, 'Enter your email address.')
    .email('Enter a valid email address.'),
  password: z
    .string()
    .min(1, 'Enter your password.'),
});

export type SignInValues = z.infer<typeof signInSchema>;