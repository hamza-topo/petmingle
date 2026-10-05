import { MapPin } from 'lucide-react';
import clsx from 'clsx';

export function PetLocation({
  distanceMiles,
  distanceKm,
  location,
  className,
}: {
  distanceMiles?: number;
  distanceKm?: number;
  location?: string;
  className?: string;
}) {
  const distanceLabel =
    distanceKm !== undefined
      ? `${distanceKm.toFixed(1)} km away`
      : distanceMiles !== undefined
        ? `${distanceMiles.toFixed(1)} miles away`
        : null;

  return (
    <p className={clsx('pet-location', className)}>
      <MapPin size={16} aria-hidden="true" />
      <span>
        {distanceLabel}
        {distanceLabel && location && (
          <span aria-hidden="true"> · </span>
        )}
        {location}
      </span>
    </p>
  );
}
