import { render, screen, waitFor } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { MemoryRouter, useLocation } from 'react-router';
import { beforeEach, expect, it, vi } from 'vitest';
import { apiRequest } from '../../api/client';
import { tokenStorage } from '../../auth/tokenStorage';
import { authenticatedAuthState } from '../../test/authFixtures';
import { MatchExperience, useMatchExperience } from './MatchExperience';

vi.mock('../../auth/AuthProvider', () => ({ useAuth: () => authenticatedAuthState() }));
vi.mock('../../api/client', () => ({ apiRequest: vi.fn() }));
vi.mock('../../realtime/pusherClient', () => ({ createPrivateRealtimeClient: () => ({ connect() {}, disconnect() {} }) }));

const target = { id: 51, name: 'Milo', photo: { src: '/storage/pets/milo.jpg', alt: 'Milo pet photo', placeholder: 'Milo' } };
function Harness() {
  const { celebrate, receive } = useMatchExperience();
  const location = useLocation();
  return <><button onClick={() => celebrate(71, target)}>Confirm match</button><button onClick={() => receive({ fromMatch: { id: 71, from: 42, to: 51 }, toMatch: { id: 72, from: 51, to: 42 } })}>Incoming match</button><button onClick={() => receive({ fromMatch: { id: 73, from: 90, to: 91 } })}>Other account match</button><p>{location.pathname}{location.search}</p></>;
}
function setup() { render(<MemoryRouter initialEntries={['/discover']}><MatchExperience><Harness /></MatchExperience></MemoryRouter>); }

beforeEach(() => {
  sessionStorage.clear();
  tokenStorage.set('test-token');
  vi.mocked(apiRequest).mockReset();
  vi.mocked(apiRequest).mockImplementation(async path => ({ data: path === '/pets/42' ? { id: 42, name: 'Nala', images: ['pets/nala.jpg'] } : { id: 51, name: 'Milo', images: ['pets/milo.jpg'] } }));
});

it('celebrates a confirmed match with real photos and opens the target conversation', async () => {
  const user = userEvent.setup(); setup();
  await user.click(screen.getByRole('button', { name: 'Confirm match' }));
  expect(screen.getByRole('dialog', { name: 'Ça sent la belle rencontre !' })).toBeVisible();
  expect(screen.getByRole('button', { name: 'Fermer la rencontre' })).toHaveFocus();
  expect(screen.getByRole('img', { name: 'Milo pet photo' })).toHaveAttribute('src', '/storage/pets/milo.jpg');
  await waitFor(() => expect(screen.getByRole('img', { name: 'Nala pet photo' })).toHaveAttribute('src', '/storage/pets/nala.jpg'));
  await user.click(screen.getByRole('link', { name: /Dire bonjour/ }));
  expect(screen.getByText('/messages?pet=51')).toBeVisible();
  expect(screen.queryByRole('dialog')).not.toBeInTheDocument();
});

it('traps focus, restores it on Escape and does not replay the same match', async () => {
  const user = userEvent.setup(); setup();
  const trigger = screen.getByRole('button', { name: 'Confirm match' });
  await user.click(trigger);
  screen.getByRole('button', { name: 'Continuer à découvrir' }).focus();
  await user.tab();
  expect(screen.getByRole('button', { name: 'Fermer la rencontre' })).toHaveFocus();
  await user.keyboard('{Escape}');
  // jsdom does not implement native dialog Escape, so dispatch the cancel event.
  screen.queryByRole('dialog')?.dispatchEvent(new Event('cancel', { bubbles: true, cancelable: true }));
  await waitFor(() => expect(screen.queryByRole('dialog')).not.toBeInTheDocument());
  expect(trigger).toHaveFocus();
  await user.click(trigger);
  expect(screen.queryByRole('dialog')).not.toBeInTheDocument();
});

it('accepts an incoming match for this pet and ignores another account', async () => {
  const user = userEvent.setup(); setup();
  await user.click(screen.getByRole('button', { name: 'Other account match' }));
  expect(apiRequest).not.toHaveBeenCalled();
  await user.click(screen.getByRole('button', { name: 'Incoming match' }));
  expect(await screen.findByRole('dialog')).toBeVisible();
  await user.click(screen.getByRole('button', { name: 'Continuer à découvrir' }));
  await user.click(screen.getByRole('button', { name: 'Confirm match' }));
  expect(screen.queryByRole('dialog')).not.toBeInTheDocument();
});
