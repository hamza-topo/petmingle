import { lazy, Suspense, useEffect, useId, useRef, useState } from 'react';
import { ChevronDown, LocateFixed, MapPin, Search, X } from 'lucide-react';
import { describeApiFailure } from '../../api/presentation';
import { accountLocationLabel, useAccountLocation } from './AccountLocationProvider';
import { reversePlace, searchPlaces, type Place } from './geocoder';
import type { LocationCoordinates } from './location.api';
import './location-picker.css';

const LocationMap = lazy(() => import('./LocationMap'));

export function AccountLocationControl() {
  const location = useAccountLocation();
  const editorId = useId();
  const trigger = useRef<HTMLButtonElement>(null);
  const [open, setOpen] = useState(false);
  const wasOpen = useRef(false);
  useEffect(() => {
    if (!open && wasOpen.current) trigger.current?.focus();
    wasOpen.current = open;
  }, [open]);
  return <div className="discovery-location-shell">
    <button className="discovery-location" ref={trigger} type="button" onClick={() => setOpen(true)} disabled={location.status !== 'ready'} aria-expanded={open} aria-haspopup="dialog" aria-controls={open ? editorId : undefined}>
      <MapPin size={22} aria-hidden="true" /><span>{accountLocationLabel(location)}</span><ChevronDown size={18} aria-hidden="true" />
    </button>
    {location.status === 'error' && <button type="button" className="discovery-location-retry" onClick={() => void location.reload()}>Retry location</button>}
    {open && <LocationPicker id={editorId} initial={location.currentLocation} save={location.saveCoordinates} close={() => setOpen(false)} />}
  </div>;
}

function LocationPicker({ id, initial, save, close }: {
  id: string;
  initial: LocationCoordinates | null;
  save: (point: LocationCoordinates) => Promise<unknown>;
  close: () => void;
}) {
  const dialog = useRef<HTMLDialogElement>(null);
  const input = useRef<HTMLInputElement>(null);
  const [point, setPoint] = useState<LocationCoordinates | null>(initial);
  const [query, setQuery] = useState('');
  const [results, setResults] = useState<Place[]>([]);
  const [searching, setSearching] = useState(false);
  const [locating, setLocating] = useState(false);
  const [saving, setSaving] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [notice, setNotice] = useState<string | null>(null);
  const search = useRef<AbortController | null>(null);
  const reverse = useRef<AbortController | null>(null);
  const active = useRef(true);
  const revision = useRef(0);

  useEffect(() => {
    active.current = true;
    const element = dialog.current;
    element?.showModal?.();
    if (element && !element.open) element.setAttribute('open', '');
    input.current?.focus();
    const previous = document.body.style.overflow;
    document.body.style.overflow = 'hidden';
    return () => {
      active.current = false;
      search.current?.abort();
      reverse.current?.abort();
      document.body.style.overflow = previous;
    };
  }, []);

  // Only settled map/GPS selections need reverse geocoding. Search results already carry a name.
  useEffect(() => {
    if (!point || point.label) return;
    const controller = new AbortController();
    reverse.current = controller;
    const timer = window.setTimeout(() => {
      void reversePlace(point, controller.signal).then(label => {
        if (!controller.signal.aborted && active.current) {
          setPoint(current => current ? { ...current, label } : current);
          setNotice(label ? null : 'City name unavailable. Your selected area can still be saved.');
        }
      }).catch(() => {
        if (!controller.signal.aborted && active.current) setNotice('City name unavailable. Your selected area can still be saved.');
      });
    }, 800);
    return () => { window.clearTimeout(timer); controller.abort(); };
  }, [point?.latitude, point?.longitude, point?.label]);

  function select(next: LocationCoordinates) {
    if (saving) return;
    revision.current += 1;
    reverse.current?.abort();
    setPoint(next);
    setResults([]);
    setError(null);
    setNotice(null);
  }

  async function findPlaces() {
    search.current?.abort();
    const controller = new AbortController();
    search.current = controller;
    setSearching(true);
    setError(null);
    setResults([]);
    try {
      const found = await searchPlaces(query, controller.signal);
      if (controller.signal.aborted || !active.current) return;
      setResults(found);
      if (!found.length) setError('No places found. Try another city or move the map.');
    } catch {
      if (!controller.signal.aborted && active.current) setError('Place search is unavailable. Move the map or use your location.');
    } finally {
      if (!controller.signal.aborted && active.current) setSearching(false);
    }
  }

  function usePosition() {
    if (!navigator.geolocation) { setError('Location is unavailable on this device. Search for a city instead.'); return; }
    setLocating(true);
    setError(null);
    const requestRevision = ++revision.current;
    navigator.geolocation.getCurrentPosition(position => {
      if (!active.current) return;
      setLocating(false);
      if (requestRevision === revision.current) select({ latitude: position.coords.latitude, longitude: position.coords.longitude });
    }, () => {
      if (!active.current) return;
      setLocating(false);
      if (requestRevision === revision.current) setError('Could not access your location. You can search for a city instead.');
    }, { enableHighAccuracy: false, timeout: 10000, maximumAge: 60000 });
  }

  async function confirm() {
    if (!point || saving) return;
    setSaving(true);
    setError(null);
    try { await save({ ...point, label: point.label || null }); close(); }
    catch (caught) { if (active.current) setError(describeApiFailure(caught).message); }
    finally { if (active.current) setSaving(false); }
  }

  return <dialog ref={dialog} id={id} className="location-picker" aria-labelledby={id + '-title'} aria-describedby={id + '-description'} aria-busy={saving} onCancel={event => { event.preventDefault(); if (!saving) close(); }} onKeyDown={event => {
      if (event.key === 'Tab') {
        const items = [...event.currentTarget.querySelectorAll<HTMLElement>('button, input, a[href], [tabindex]')].filter(element => !element.matches(':disabled, [tabindex="-1"]') && element.getClientRects().length > 0);
        const first = items[0];
        const last = items[items.length - 1];
        if (first && last && ((!event.shiftKey && document.activeElement === last) || (event.shiftKey && document.activeElement === first))) {
          event.preventDefault();
          (event.shiftKey ? last : first).focus();
        }
      }
      if (event.key === 'Escape') { event.preventDefault(); if (!saving) close(); } }}>
    <header className="location-picker-heading"><div><span className="location-picker-eyebrow">A little closer</span><h2 id={id + '-title'}>Where do your paths meet?</h2></div><button type="button" className="location-picker-close" aria-label="Close location picker" disabled={saving} onClick={close}><X size={22} /></button></header>
    <p id={id + '-description'} className="location-picker-description">Choose your area to discover pets nearby.</p>
    <fieldset disabled={saving} className="location-picker-body">
      <form className="location-picker-search" role="search" onSubmit={event => { event.preventDefault(); if (query.trim().length >= 2) void findPlaces(); }}>
        <Search size={20} aria-hidden="true" /><input ref={input} aria-label="Search city or neighbourhood" placeholder="Search a city or neighbourhood" value={query} maxLength={120} onChange={event => { search.current?.abort(); setSearching(false); setResults([]); setQuery(event.target.value); }} />
        <button type="submit" disabled={searching || query.trim().length < 2}>{searching ? 'Searching…' : 'Search'}</button>
      </form>
      {results.length > 0 && <ul className="location-picker-results" aria-label="Place search results">{results.map((place, index) => <li key={`${place.latitude}:${place.longitude}:${index}`}><button type="button" onClick={() => select(place)}><MapPin size={18} aria-hidden="true" /><span>{place.label}</span><span aria-hidden="true">↗</span></button></li>)}</ul>}
      <button type="button" className="location-picker-gps" onClick={usePosition} disabled={locating}><LocateFixed size={19} aria-hidden="true" />{locating ? 'Finding your location…' : 'Use my current location'}</button>
      <Suspense fallback={<div className="location-map-loading" role="status">Loading map…</div>}><LocationMap point={point} onSelect={select} /></Suspense>
    </fieldset>
    <footer className="location-picker-footer"><div className="location-picker-selected"><span className="location-picker-selected-icon"><MapPin size={22} aria-hidden="true" /></span><div><small>Your discovery area</small><strong>{point ? point.label || 'Selected area' : 'Choose a place on the map'}</strong></div></div>
      <p className="location-picker-privacy">Your exact coordinates are not displayed on your profile. Map and place search use external services.</p>
      {error && <p className="location-picker-error" role="alert">{error}</p>}{notice && <p className="location-picker-notice" role="status">{notice}</p>}
      <button type="button" className="location-picker-confirm" disabled={!point || saving} onClick={() => void confirm()}>{saving ? 'Saving…' : 'Confirm this area'}<span aria-hidden="true">↗</span></button>
    </footer>
  </dialog>;
}
