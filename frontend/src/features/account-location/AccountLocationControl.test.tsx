import { render, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { beforeEach, describe, expect, it, vi } from 'vitest';
import { AccountLocationControl } from './AccountLocationControl';
import { useAccountLocation } from './AccountLocationProvider';

vi.mock('./AccountLocationProvider', async importOriginal => ({
  ...await importOriginal<typeof import('./AccountLocationProvider')>(),
  useAccountLocation: vi.fn(),
}));

describe('Location editor keyboard access', () => {
  beforeEach(() => {
    vi.mocked(useAccountLocation).mockReturnValue({
      status: 'ready', locations: [], currentLocation: null, error: null,
      reload: vi.fn(), saveCoordinates: vi.fn(),
    });
  });
  it('opens with Enter, focuses latitude, and returns focus on Escape', async () => {
    const user = userEvent.setup();
    render(<AccountLocationControl />);
    await user.tab();
    const trigger = screen.getByRole('button', { name: 'Location not set' });
    expect(trigger).toHaveFocus();
    await user.keyboard('{Enter}');
    expect(screen.getByRole('textbox', { name: 'Latitude' })).toHaveFocus();
    expect(trigger).toHaveAttribute('aria-expanded', 'true');
    await user.keyboard('{Escape}');
    expect(screen.queryByRole('form', { name: 'Account location' })).not.toBeInTheDocument();
    expect(trigger).toHaveFocus();
  });
  it('associates validation errors and focuses the first invalid coordinate', async () => {
    const user = userEvent.setup();
    render(<AccountLocationControl />);
    await user.click(screen.getByRole('button', { name: 'Location not set' }));
    await user.click(screen.getByRole('button', { name: 'Save coordinates' }));
    const latitude = screen.getByRole('textbox', { name: /Latitude/ });
    expect(latitude).toHaveFocus();
    expect(latitude).toHaveAttribute('aria-invalid', 'true');
    expect(latitude).toHaveAccessibleDescription('Latitude must be between -90 and 90.');
    expect(screen.getByRole('textbox', { name: /Longitude/ })).toHaveAccessibleDescription('Longitude must be between -180 and 180.');
  });
});
