import { LogOut } from 'lucide-react';
import { useState } from 'react';
import { useNavigate } from 'react-router';
import { useAuth } from './AuthProvider';

type SignOutButtonProps = {
  className?: string;
};

export function SignOutButton({
  className,
}: SignOutButtonProps) {
  const { signOut } = useAuth();
  const navigate = useNavigate();

  const [isSigningOut, setIsSigningOut] = useState(false);
  const [error, setError] = useState<string | null>(null);

  async function handleSignOut() {
    if (isSigningOut) {
      return;
    }

    setIsSigningOut(true);
    setError(null);

    try {
      await signOut();
      navigate('/signin', { replace: true });
    } catch {
      setError('Unable to sign out. Please try again.');
      setIsSigningOut(false);
    }
  }

  return (
    <div className={className}>
      <button
        type="button"
        className="sign-out-button"
        onClick={handleSignOut}
        disabled={isSigningOut}
        aria-label="Sign out"
        title="Sign out"
      >
        <LogOut size={20} aria-hidden="true" />
        <span>{isSigningOut ? 'Signing out…' : 'Sign out'}</span>
      </button>

      {error && (
        <p className="sign-out-error" role="alert">
          {error}
        </p>
      )}
    </div>
  );
}