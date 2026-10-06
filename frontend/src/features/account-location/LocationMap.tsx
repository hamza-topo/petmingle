import { useEffect, useRef, useState } from 'react';
import * as L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import type { LocationCoordinates } from './location.api';

type Props = { point: LocationCoordinates | null; onSelect: (point: LocationCoordinates) => void };
const fallback = { latitude: 31.6295, longitude: -7.9811 };

export default function LocationMap({ point, onSelect }: Props) {
  const container = useRef<HTMLDivElement>(null);
  const map = useRef<L.Map | null>(null);
  const latest = useRef({ point, onSelect });
  latest.current = { point, onSelect };
  const [tileError, setTileError] = useState(false);
  useEffect(() => {
    if (!container.current) return;
    const initial = latest.current.point ?? fallback;
    const instance = L.map(container.current, { zoomControl: true, scrollWheelZoom: false }).setView([initial.latitude, initial.longitude], 12);
    map.current = instance;
    const tiles = L.tileLayer(import.meta.env.VITE_MAP_TILE_URL || 'https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
      attribution: import.meta.env.VITE_MAP_TILE_ATTRIBUTION || '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noreferrer">OpenStreetMap</a> contributors',
    });
    tiles.on('tileerror', () => setTileError(true));
    tiles.addTo(instance);
    instance.on('moveend', () => {
      const center = instance.getCenter().wrap();
      const selected = latest.current.point;
      if (!selected || Math.abs(center.lat - selected.latitude) > 0.000001 || Math.abs(center.lng - selected.longitude) > 0.000001) {
        latest.current.onSelect({ latitude: Math.max(-85, Math.min(85, center.lat)), longitude: center.lng });
      }
    });
    instance.on('click', (event: L.LeafletMouseEvent) => instance.panTo(event.latlng));
    const observer = new ResizeObserver(() => instance.invalidateSize({ pan: false }));
    observer.observe(container.current);
    return () => { observer.disconnect(); instance.remove(); map.current = null; };
  }, []);
  useEffect(() => {
    if (point && map.current) map.current.setView([point.latitude, point.longitude], map.current.getZoom(), { animate: false });
  }, [point?.latitude, point?.longitude]);
  return <div className="location-map-shell">
    <div ref={container} className="location-map" role="region" aria-label="Location map. Drag or use arrow keys to move the pin." />
    <div className="location-map-pin" aria-hidden="true"><span /></div>
    <span className="location-map-hint">Move the map to choose your area</span>
    {tileError && <p className="location-map-warning" role="status">Map tiles unavailable. You can still choose a city or use your location.</p>}
  </div>;
}
