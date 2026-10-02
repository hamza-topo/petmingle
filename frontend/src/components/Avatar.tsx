import clsx from 'clsx';
import type { ReferenceAsset } from '../assets/landingAssets';
import { ReferenceImage } from './ReferenceImage';

/** Identity stays with the caller: the image may represent an owner or a pet. */
export function Avatar({ asset, className }: { asset: ReferenceAsset; className?: string }) {
  return <ReferenceImage asset={asset} className={clsx('identity-avatar', className)} />;
}
