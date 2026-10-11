import { createContext, useCallback, useContext, useEffect, useRef, useState, type CSSProperties, type ReactNode } from 'react';
import { Link, Outlet, useLocation } from 'react-router';
import { X } from 'lucide-react';
import { apiRequest } from '../../api/client';
import { mediaUrl } from '../../api/config';
import { useAuth } from '../../auth/AuthProvider';
import { tokenStorage } from '../../auth/tokenStorage';
import { ReferenceImage } from '../../components/ReferenceImage';
import type { ReferenceAsset } from '../../assets/landingAssets';
import { createPrivateRealtimeClient } from '../../realtime/pusherClient';

type MatchPet = { id: number; name: string; photo: ReferenceAsset };
type Celebration = { matchId: number; target: MatchPet };
type MatchApiPet = { id: number; name: string; images: string[] };
const positiveId = (value: unknown): value is number => typeof value === 'number' && Number.isInteger(value) && value > 0;
const MatchContext = createContext({ celebrate: (_matchId: number, _target: MatchPet) => {}, receive: (_data: unknown) => {} });

export function useMatchExperience() { return useContext(MatchContext); }

function photoFor(pet: MatchApiPet): ReferenceAsset {
  const path = pet.images?.find(image => typeof image === 'string' && image.trim());
  return { src: path ? mediaUrl(path) : null, alt: `${pet.name} pet photo`, placeholder: pet.name };
}

export function MatchExperience({ children }: { children: ReactNode }) {
  const { user, pet } = useAuth();
  const { pathname } = useLocation();
  const [queue, setQueue] = useState<Celebration[]>([]);
  const [ownPhoto, setOwnPhoto] = useState<ReferenceAsset | null>(null);
  const seen = useRef(new Set<string>());
  const fetching = useRef(new Set<string>());
  const dialog = useRef<HTMLDialogElement>(null);
  const active = queue[0];
  const identity = `${user?.id}:${pet?.id}`;
  const identityRef = useRef(identity);
  identityRef.current = identity;

  useEffect(() => { setQueue([]); seen.current.clear(); fetching.current.clear(); }, [identity]);

  const celebrate = useCallback((matchId: number, target: MatchPet) => {
    if (!user || !pet || !positiveId(matchId) || !positiveId(target.id) || target.id === pet.id) return;
    const key = `petmingle.match-seen:${user.id}:${pet.id}:${matchId}`;
    if (seen.current.has(key)) return;
    try { if (sessionStorage.getItem(key)) return; sessionStorage.setItem(key, '1'); } catch { /* In-memory deduplication also works when storage is unavailable. */ }
    seen.current.add(key);
    setQueue(current => [...current, { matchId, target }]);
  }, [user, pet]);

  const receive = useCallback((data: unknown) => {
    if (!user || !pet || !data || typeof data !== 'object') return;
    const raw = data as { fromMatch?: { id?: unknown; from?: unknown; to?: unknown }; toMatch?: { id?: unknown; from?: unknown; to?: unknown } };
    const match = [raw.fromMatch, raw.toMatch].find(item => item?.from === pet.id);
    if (!match || !positiveId(match.id) || !positiveId(match.to) || match.to === pet.id) return;
    const matchId = match.id;
    const targetId = match.to;
    const token = tokenStorage.get();
    const key = `${identity}:${matchId}`;
    if (!token || fetching.current.has(key)) return;
    fetching.current.add(key);
    void apiRequest<{ data: MatchApiPet }>(`/pets/${targetId}`, { token }).then(response => {
      if (identityRef.current === identity && response.data.id === targetId && response.data.name) celebrate(matchId, { id: targetId, name: response.data.name, photo: photoFor(response.data) });
    }).catch(() => { /* A notification never replaces a successful interaction with an error. */ }).finally(() => fetching.current.delete(key));
  }, [user, pet, identity, celebrate]);

  useEffect(() => {
    const token = tokenStorage.get();
    // Messaging already subscribes for incoming messages and forwards match events.
    if (!token || !user || !pet || pathname === '/messages') return;
    const client = createPrivateRealtimeClient({ token, userId: user.id, callbacks: { onEvent: event => { if (event.name === 'new.match') receive(event.data); } } });
    client.connect();
    return () => client.disconnect();
  }, [user?.id, pet?.id, pathname, receive]);

  useEffect(() => {
    setOwnPhoto(null);
    const token = tokenStorage.get();
    if (!active || !token || !pet) return;
    let cancelled = false;
    void apiRequest<{ data: MatchApiPet }>(`/pets/${pet.id}`, { token }).then(response => {
      if (!cancelled && response.data.id === pet.id) setOwnPhoto(photoFor(response.data));
    }).catch(() => {});
    return () => { cancelled = true; };
  }, [active?.matchId, pet?.id]);

  useEffect(() => {
    if (!active) return;
    const element = dialog.current;
    const previous = document.activeElement as HTMLElement | null;
    const overflow = document.body.style.overflow;
    document.body.style.overflow = 'hidden';
    element?.showModal?.();
    if (element && !element.open) element.setAttribute('open', '');
    element?.querySelector<HTMLButtonElement>('button')?.focus();
    return () => { document.body.style.overflow = overflow; if (previous?.isConnected && !previous.matches(':disabled')) previous.focus(); else document.querySelector<HTMLElement>('main')?.focus(); };
  }, [active?.matchId]);

  function dismiss() { setQueue(current => current.slice(1)); }

  return (
    <MatchContext.Provider value={{ celebrate, receive }}>
      {children}
      {active && pet && <dialog
        key={active.matchId}
        ref={dialog}
        className="match-celebration"
        aria-labelledby="match-celebration-title"
        aria-describedby="match-celebration-description"
        onCancel={event => { event.preventDefault(); dismiss(); }}
        onKeyDown={event => {
          if (event.key !== 'Tab') return;
          const controls = [...(dialog.current?.querySelectorAll<HTMLElement>('button, a[href]') ?? [])];
          const first = controls[0], last = controls[controls.length - 1];
          if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last?.focus(); }
          else if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first?.focus(); }
        }}
      >
        <button className="match-close" type="button" aria-label="Fermer la rencontre" onClick={dismiss}><X size={22} /></button>
        <div className="match-confetti" aria-hidden="true">{Array.from({ length: 10 }, (_, index) => <i key={index} style={{ '--piece': index } as CSSProperties} />)}</div>
        <p className="match-kicker">Une nouvelle histoire</p>
        <div className="match-portraits">
          <figure><ReferenceImage asset={ownPhoto ?? { src: null, alt: `${pet.name} pet profile`, placeholder: pet.name }} /><figcaption>{pet.name}</figcaption></figure>
          <figure><ReferenceImage asset={active.target.photo} /><figcaption>{active.target.name}</figcaption></figure>
        </div>
        <h2 id="match-celebration-title">Ça sent la belle rencontre !</h2>
        <p id="match-celebration-description">{pet.name} et {active.target.name} se sont plu. Un petit bonjour pour leur prochaine balade ?</p>
        <Link className="match-hello" to={`/messages?pet=${active.target.id}`} onClick={() => setQueue([])}>Dire bonjour <span aria-hidden="true">↗</span></Link>
        <button className="match-continue" type="button" onClick={dismiss}>Continuer à découvrir</button>
      </dialog>}
    </MatchContext.Provider>
  );
}

export function MatchExperienceRoute() { return <MatchExperience><Outlet /></MatchExperience>; }
