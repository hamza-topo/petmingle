import { useEffect, useRef, useState } from 'react';
import * as L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import type { AccountLocation } from '../../account-location/location.api';
import type { DiscoveryPet } from '../discovery.api';

export function DiscoveryMap({ pets, center, radiusKm, selectedPetId, onSelect, interactions }: {
  pets: DiscoveryPet[];
  center: AccountLocation;
  radiusKm: number;
  selectedPetId: number | null;
  onSelect: (id: number) => void;
  interactions?: Map<number, import('../discovery.api').PetInteractionState>;
}) {
  const container = useRef<HTMLDivElement>(null);
  const map = useRef<L.Map | null>(null);
  const select = useRef(onSelect);
  select.current = onSelect;
  const [tileError, setTileError] = useState(false);
  useEffect(() => {
    if (!container.current) return;
    const instance = L.map(container.current, { scrollWheelZoom: false }).setView([center.latitude!, center.longitude!], 13);
    map.current = instance;
    const tiles = L.tileLayer(import.meta.env.VITE_MAP_TILE_URL || 'https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
      attribution: import.meta.env.VITE_MAP_TILE_ATTRIBUTION || '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
    }).addTo(instance);
    tiles.on('tileerror', () => setTileError(true));
    const observer = typeof ResizeObserver === 'undefined' ? null : new ResizeObserver(() => instance.invalidateSize({ pan: false }));
    observer?.observe(container.current);
    return () => { observer?.disconnect(); instance.remove(); map.current = null; };
  }, [center.latitude, center.longitude]);

  useEffect(() => {
    const instance = map.current;
    if (!instance) return;
    const layer = L.layerGroup().addTo(instance);
    const groups = new Map<string, DiscoveryPet[]>();
    for (const pet of pets) {
      if (!pet.mapLocation) continue;
      const key = `${pet.mapLocation.latitude},${pet.mapLocation.longitude}`;
      groups.set(key, [...(groups.get(key) ?? []), pet]);
    }
    for (const group of groups.values()) {
      const pet = group.find(item => item.id === selectedPetId) ?? group[0];
      const content = document.createElement('span');
      content.className = 'pet-map-photo';
      if (pet.photo.src) {
        const img = document.createElement('img');
        img.src = pet.photo.src; img.alt = '';
        img.addEventListener('error', () => { img.remove(); content.append(document.createTextNode(pet.name.slice(0, 1))); }, { once: true });
        content.append(img);
      } else content.textContent = pet.name.slice(0, 1);
      if (group.length > 1) {
        const badge = document.createElement('b'); badge.textContent = String(group.length); content.append(badge);
      }
      const marker = L.marker([pet.mapLocation!.latitude, pet.mapLocation!.longitude], {
        icon: L.divIcon({ html: content, className: `pet-map-marker${(interactions?.get(pet.id) ?? pet.interaction) === 'liked' ? ' is-liked' : ''}${(interactions?.get(pet.id) ?? pet.interaction) === 'disliked' ? ' is-passed' : ''}${group.some(item => item.id === selectedPetId) ? ' is-selected' : ''}`, iconSize: [58, 58], iconAnchor: [29, 29] }),
        title: group.length > 1 ? `${group.length} compagnons dans cette zone` : `Voir ${pet.name}`,
        keyboard: true,
      }).addTo(layer);
      if (group.length === 1) marker.on('click', () => select.current(pet.id));
      else {
        const list = document.createElement('div'); list.className = 'pet-map-group';
        for (const item of group) {
          const button = document.createElement('button'); button.type = 'button'; button.textContent = item.name;
          button.onclick = () => { select.current(item.id); instance.closePopup(); }; list.append(button);
        }
        marker.bindPopup(list);
      }
    }
    return () => { layer.remove(); };
  }, [pets, selectedPetId, interactions, center.latitude, center.longitude]);

  useEffect(() => {
    if (!map.current) return;
    const lat = center.latitude!;
    const lng = center.longitude!;
    const latRadius = radiusKm / 111;
    const lngRadius = latRadius / Math.max(0.1, Math.cos(lat * Math.PI / 180));
    map.current.fitBounds([[lat - latRadius, lng - lngRadius], [lat + latRadius, lng + lngRadius]], { padding: [45, 45], maxZoom: 14, animate: false });
  }, [radiusKm, center.latitude, center.longitude]);

  useEffect(() => {
    const pet = pets.find(item => item.id === selectedPetId);
    if (pet?.mapLocation && map.current) {
      map.current.panInside([pet.mapLocation.latitude, pet.mapLocation.longitude], { padding: [85, 85], animate: !window.matchMedia?.('(prefers-reduced-motion: reduce)').matches });
    }
  }, [selectedPetId, pets]);

  return <section className="discovery-map-shell" aria-label="Carte des compagnons à proximité">
    <div ref={container} className="discovery-pet-map" aria-label="Carte interactive. Utilisez les flèches pour vous déplacer." />
    <div className="discovery-map-caption"><strong>Autour de vous · {radiusKm} km</strong><span>Positions approximatives</span></div>
    <button type="button" className="discovery-map-recenter" onClick={() => map.current?.setView([center.latitude!, center.longitude!], 13, { animate: false })}>Recentrer</button>
    {tileError && <p className="discovery-map-warning" role="status">Le fond de carte est indisponible. Les profils restent accessibles ci-dessous.</p>}
    <details className="discovery-map-list"><summary>{pets.length} compagnons sur la carte</summary><div className="discovery-map-pets" aria-label="Choisir un compagnon">
      {pets.map(pet => <button key={pet.id} type="button" aria-pressed={selectedPetId === pet.id} onClick={() => {
        onSelect(pet.id);
        if (pet.mapLocation) map.current?.panTo([pet.mapLocation.latitude, pet.mapLocation.longitude], { animate: false });
      }}>{pet.photo.src && <img src={pet.photo.src} alt="" />}<span>{pet.name}</span></button>)}
    </div></details>
  </section>;
}
