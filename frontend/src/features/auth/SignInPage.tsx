import { zodResolver } from '@hookform/resolvers/zod';
import { useState } from 'react';
import { useForm } from 'react-hook-form';
import { useLocation, useNavigate } from 'react-router';
import { ApiError } from '../../api/errors';
import { useAuth } from '../../auth/AuthProvider';
import { ActionButton } from '../../components/Action';
import { PetMingleLogo } from '../../components/PetMingleLogo';
import {
  signInSchema,
  type SignInValues,
} from './signIn.schema';

export function SignInPage() {
  const location = useLocation();
  const navigate = useNavigate();

  const requestedPath =
    typeof location.state === 'object'
      && location.state !== null
      && 'from' in location.state
      && typeof location.state.from === 'string'
      ? location.state.from
      : '/discover';
  const { signIn } = useAuth();
  const [submissionError, setSubmissionError] = useState<string | null>(
    null,
  );

  const {
    register,
    handleSubmit,
    formState: {
      errors,
      isSubmitting,
    },
  } = useForm<SignInValues>({
    resolver: zodResolver(signInSchema),
    defaultValues: {
      email: '',
      password: '',
    },
  });

  async function onSubmit(values: SignInValues) {
    setSubmissionError(null);

    try {
      await signIn(values);
      navigate(requestedPath, { replace: true });
    } catch (caught) {
      if (caught instanceof ApiError && caught.status === 401) {
        setSubmissionError('Email or password is incorrect.');
        return;
      }

      setSubmissionError(
        'Unable to sign in right now. Please try again.',
      );
    }
  }

  return (
    <div className="sign-in-page">
      <header className="sign-in-header">
        <PetMingleLogo />
      </header>

      <main className="sign-in-main">
        <section
          className="sign-in-panel"
          aria-labelledby="sign-in-title"
        >
          <div className="sign-in-copy">
            <p className="sign-in-eyebrow">Welcome back</p>
            <h1 id="sign-in-title">Sign in to PetMingle</h1>
            <p>
              Continue discovering pets, conversations and your
              PetMingle profile.
            </p>
          </div>

          <form
            className="sign-in-form"
            onSubmit={handleSubmit(onSubmit)}
            noValidate
          >
            <div className="sign-in-field">
              <label htmlFor="sign-in-email">Email</label>

              <input
                id="sign-in-email"
                type="email"
                autoComplete="email"
                aria-invalid={Boolean(errors.email)}
                aria-describedby={
                  errors.email ? 'sign-in-email-error' : undefined
                }
                {...register('email')}
              />

              {errors.email && (
                <p
                  id="sign-in-email-error"
                  className="sign-in-field-error"
                >
                  {errors.email.message}
                </p>
              )}
            </div>

            <div className="sign-in-field">
              <label htmlFor="sign-in-password">Password</label>

              <input
                id="sign-in-password"
                type="password"
                autoComplete="current-password"
                aria-invalid={Boolean(errors.password)}
                aria-describedby={
                  errors.password
                    ? 'sign-in-password-error'
                    : undefined
                }
                {...register('password')}
              />

              {errors.password && (
                <p
                  id="sign-in-password-error"
                  className="sign-in-field-error"
                >
                  {errors.password.message}
                </p>
              )}
            </div>

            {submissionError && (
              <p className="sign-in-error" role="alert">
                {submissionError}
              </p>
            )}

            <ActionButton
              type="submit"
              className="sign-in-submit"
              disabled={isSubmitting}
            >
              {isSubmitting ? 'Signing in…' : 'Sign In'}
            </ActionButton>
          </form>
        </section>
      </main>
    </div>
  );
}