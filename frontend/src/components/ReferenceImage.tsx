import clsx from 'clsx';
import type { ReferenceAsset } from '../assets/landingAssets';

interface ReferenceImageProps {
  asset: ReferenceAsset;
  className?: string;
}

export function ReferenceImage({ asset, className }: ReferenceImageProps) {
  if (asset.src) {
    return (
      <img
        className={clsx('reference-image', className)}
        src={asset.src}
        alt={asset.alt}
        style={{ objectPosition: asset.position }}
      />
    );
  }

  return (
    <div
      className={clsx('reference-placeholder', className)}
      role="img"
      aria-label={`${asset.alt} — placeholder; reference asset unavailable`}
    >
      <span>{asset.placeholder}</span>
    </div>
  );
}
