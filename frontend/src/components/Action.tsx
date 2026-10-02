import type { ComponentPropsWithoutRef } from 'react';
import clsx from 'clsx';
import { Link } from 'react-router';

type Appearance = 'primary' | 'secondary';

type ButtonProps = ComponentPropsWithoutRef<'button'> & {
  variant?: Appearance;
  unavailableReason?: string;
};

export function ActionButton({
  variant = 'primary', className, unavailableReason, children, ...props
}: ButtonProps) {
  return (
    <button
      type="button"
      {...props}
      className={clsx('action', `action--${variant}`, className)}
      disabled={Boolean(unavailableReason) || props.disabled}
      title={unavailableReason ?? props.title}
    >
      {children}
      {unavailableReason && <span className="sr-only"> — {unavailableReason}</span>}
    </button>
  );
}

type LinkProps = ComponentPropsWithoutRef<typeof Link> & { variant?: Appearance };

export function ActionLink({ variant = 'secondary', className, ...props }: LinkProps) {
  return <Link {...props} className={clsx('action', `action--${variant}`, className)} />;
}
