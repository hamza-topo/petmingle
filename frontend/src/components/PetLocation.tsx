import { MapPin } from 'lucide-react';

export function PetLocation({ distanceMiles, location }: { distanceMiles: number; location?: string }) {
  return (
    <p className="pet-location">
      <MapPin size={16} aria-hidden="true" />
      <span>{distanceMiles.toFixed(1)} miles away{location && <> <span aria-hidden="true"> · </span> {location}</>}</span>
    </p>
  );
}
