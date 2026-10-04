import clsx from 'clsx';
import type { ReferenceAsset } from '../assets/landingAssets';
import { ReferenceImage } from './ReferenceImage';

type AvatarProps = {
  asset?: ReferenceAsset;
  name?: string;
  className?: string;
};

/** Identity stays with the caller: render a supplied asset or a safe name-based fallback. */
export function Avatar({
  asset,
  name,
  className,
}: AvatarProps) {
  const fallbackAsset: ReferenceAsset = {
    src: null,
    alt: `${name ?? 'Account'} avatar`,
    placeholder: name ?? 'Account',
  };

  return (
    <ReferenceImage
      asset={asset ?? fallbackAsset}
      className={clsx('identity-avatar', className)}
    />
  );
}
