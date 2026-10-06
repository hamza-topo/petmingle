import { act, render, screen, waitFor } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { beforeEach, describe, expect, it, vi } from 'vitest';
import { AccountLocationControl } from './AccountLocationControl';
import { useAccountLocation } from './AccountLocationProvider';
import { reversePlace, searchPlaces } from './geocoder';

vi.mock('./AccountLocationProvider', async importOriginal => ({
  ...await importOriginal<typeof import('./AccountLocationProvider')>(), useAccountLocation: vi.fn(),
}));
vi.mock('./geocoder', () => ({ searchPlaces: vi.fn(), reversePlace: vi.fn() }));
vi.mock('./LocationMap', () => ({ default: ({ onSelect }: { onSelect: (point: { latitude: number; longitude: number }) => void }) => <button onClick={() => onSelect({ latitude: 33.57, longitude: -7.59 })}>Move map</button> }));
const place = { latitude: 33.5731, longitude: -7.5898, label: 'Casablanca, Morocco' };
const save = vi.fn();
async function open() {
  const user = userEvent.setup();
  render(<AccountLocationControl />);
  await user.click(screen.getByRole('button', { name: 'Location not set' }));
  await screen.findByRole('button', { name: 'Move map' });
  return user;
}
async function choose(user: ReturnType<typeof userEvent.setup>) {
  await user.type(screen.getByRole('textbox', { name: 'Search city or neighbourhood' }), 'Casablanca');
  await user.click(screen.getByRole('button', { name: 'Search' }));
  await user.click(await screen.findByRole('button', { name: /Casablanca, Morocco/ }));
}
describe('Location picker', () => {
  beforeEach(() => {
    vi.clearAllMocks();
    save.mockResolvedValue(place);
    vi.mocked(searchPlaces).mockResolvedValue([place]);
    vi.mocked(reversePlace).mockResolvedValue('Casablanca, Morocco');
    vi.mocked(useAccountLocation).mockReturnValue({ status: 'ready', locations: [], currentLocation: null, error: null, reload: vi.fn(), saveCoordinates: save });
  });
  it('opens by keyboard, focuses search and cancels without saving', async () => {
    const user = userEvent.setup();
    render(<AccountLocationControl />);
    await user.tab();
    const trigger = screen.getByRole('button', { name: 'Location not set' });
    await user.keyboard('{Enter}');
    expect(screen.getByRole('textbox', { name: 'Search city or neighbourhood' })).toHaveFocus();
    await user.keyboard('{Escape}');
    expect(screen.queryByRole('dialog')).not.toBeInTheDocument();
    expect(trigger).toHaveFocus();
    expect(save).not.toHaveBeenCalled();
  });
  it('searches only on submit and saves a selected place only on confirmation', async () => {
    const user = await open();
    expect(screen.getByRole('button', { name: 'Confirm this area' })).toBeDisabled();
    await choose(user);
    expect(searchPlaces).toHaveBeenCalledTimes(1);
    expect(save).not.toHaveBeenCalled();
    await user.click(screen.getByRole('button', { name: 'Confirm this area' }));
    expect(save).toHaveBeenCalledWith(place);
    expect(screen.queryByRole('dialog')).not.toBeInTheDocument();
  });
  it('allows a map choice to save even when city lookup is unavailable', async () => {
    vi.mocked(reversePlace).mockRejectedValue(new Error('offline'));
    const user = await open();
    await user.click(screen.getByRole('button', { name: 'Move map' }));
    await user.click(screen.getByRole('button', { name: 'Confirm this area' }));
    expect(save).toHaveBeenCalledWith({ latitude: 33.57, longitude: -7.59, label: null });
  });
  it('keeps the selection and offers recovery after a save failure', async () => {
    save.mockRejectedValueOnce(new Error('offline'));
    const user = await open();
    await choose(user);
    await user.click(screen.getByRole('button', { name: 'Confirm this area' }));
    expect(await screen.findByRole('alert')).toBeInTheDocument();
    expect(screen.getByRole('dialog')).toBeInTheDocument();
    await user.click(screen.getByRole('button', { name: 'Confirm this area' }));
    expect(save).toHaveBeenCalledTimes(2);
  });
  it('does not request GPS until clicked and explains a denied request', async () => {
    const gps = vi.fn((_success, failure) => failure({ code: 1 }));
    Object.defineProperty(navigator, 'geolocation', { configurable: true, value: { getCurrentPosition: gps } });
    const user = await open();
    expect(gps).not.toHaveBeenCalled();
    await user.click(screen.getByRole('button', { name: 'Use my current location' }));
    expect(await screen.findByRole('alert')).toHaveTextContent('Could not access your location');
    expect(save).not.toHaveBeenCalled();
  });
  it('does not let late GPS replace a newer city choice', async () => {
    let success: PositionCallback = () => {};
    Object.defineProperty(navigator, 'geolocation', { configurable: true, value: { getCurrentPosition: vi.fn((callback: PositionCallback) => { success = callback; }) } });
    const user = await open();
    await user.click(screen.getByRole('button', { name: 'Use my current location' }));
    await choose(user);
    act(() => success({ coords: { latitude: 48.85, longitude: 2.35 } } as GeolocationPosition));
    await user.click(screen.getByRole('button', { name: 'Confirm this area' }));
    await waitFor(() => expect(save).toHaveBeenCalledWith(place));
  });
  it('shows a useful fallback when search is unavailable', async () => {
    vi.mocked(searchPlaces).mockRejectedValue(new Error('offline'));
    const user = await open();
    await user.type(screen.getByRole('textbox'), 'Casablanca');
    await user.click(screen.getByRole('button', { name: 'Search' }));
    expect(await screen.findByRole('alert')).toHaveTextContent('Move the map or use your location');
    expect(screen.getByRole('button', { name: 'Move map' })).toBeEnabled();
  });
});
