import { MapPin } from 'lucide-react';
import clsx from 'clsx';

export function PetLocation({ distanceMiles, location, className }: { distanceMiles?: number; location?: string; className?: string }) {
  return (
    <p className={clsx("pet-location", className)}>
      <MapPin size={16} aria-hidden="true" />
      <span>{distanceMiles !== undefined && <>{distanceMiles.toFixed(1)} miles away{location && <span aria-hidden="true"> · </span>}</>}{location}</span>
    </p>
  );
}
