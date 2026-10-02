import { render, screen, within } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { MemoryRouter } from 'react-router';
import { describe, expect, it } from 'vitest';
import { App } from '../../app/App';
import { ownPet, plusPlans, profileStats } from './profile.fixtures';
function renderProfile() { return render(<MemoryRouter initialEntries={['/profile']}><App /></MemoryRouter>); }
describe('Own pet profile', () => {
  it('renders the pet identity, biography and details from fixtures', () => {
    renderProfile();
    expect(screen.getByRole('heading', { level: 1, name: ownPet.name })).toBeVisible();
    expect(screen.getByRole('region', { name: `About ${ownPet.name}` })).toHaveTextContent(ownPet.biography);
    const details = within(screen.getByRole('region', { name: 'Details' }));
    expect(details.getByText(ownPet.breed)).toBeVisible();
    expect(details.getByText(ownPet.location)).toBeVisible();
    expect(details.getByText('62 lbs')).toBeVisible();
    for (const trait of ownPet.traits) expect(screen.getByText(trait.label)).toBeVisible();
  });
  it('renders exactly the three fixture statistics', () => {
    renderProfile();
    const stats = within(screen.getByLabelText('Pet profile statistics'));
    expect(stats.getAllByRole('term')).toHaveLength(profileStats.length);
    for (const stat of profileStats) { expect(stats.getByText(stat.label)).toBeVisible(); expect(stats.getByText(String(stat.value))).toBeVisible(); }
  });
  it('renders four thumbnails and changes the active main image locally', async () => {
    const user = userEvent.setup(); renderProfile();
    const gallery = within(screen.getByRole('group', { name: 'Pet photo gallery' }));
    expect(gallery.getAllByRole('button', { name: /View photo/ })).toHaveLength(ownPet.gallery.length);
    expect(gallery.getByRole('button', { name: 'View photo 1' })).toHaveAttribute('aria-pressed', 'true');
    await user.click(gallery.getByRole('button', { name: 'View photo 3' }));
    expect(gallery.getByRole('button', { name: 'View photo 3' })).toHaveAttribute('aria-pressed', 'true');
    expect(gallery.getByRole('button', { name: 'View photo 1' })).toHaveAttribute('aria-pressed', 'false');
    const overview = screen.getByRole('region', { name: 'Pet profile overview' });
    expect(overview.querySelector('.own-main-photo [role="img"]')).toHaveAccessibleName(/Nala — Running outdoors/);
  });
  it('renders Plus prices and changes the selected plan without enabling purchase', async () => {
    const user = userEvent.setup(); renderProfile();
    for (const plan of plusPlans) { expect(screen.getByRole('radio', { name: plan.label })).toBeVisible(); expect(screen.getByText(plan.monthlyPrice)).toBeVisible(); }
    expect(screen.getByRole('radio', { name: '12 Months' })).toBeChecked();
    await user.click(screen.getByRole('radio', { name: '3 Months' }));
    expect(screen.getByRole('radio', { name: '3 Months' })).toBeChecked();
    expect(screen.getByRole('radio', { name: '12 Months' })).not.toBeChecked();
    expect(screen.getByText('Most Popular')).toBeVisible();
    expect(screen.getByRole('button', { name: /Try PetMingle Plus/ })).toBeDisabled();
  });
  it('opens Messaging while Home remains a destination rather than the current route', async () => {
    const user = userEvent.setup(); renderProfile();
    expect(screen.getByRole('link', { name: 'Home' })).not.toHaveAttribute('aria-current');
    expect(screen.getByRole('button', { name: 'Nala pet menu — unavailable' })).toBeVisible();
    await user.click(screen.getByRole('link', { name: 'Messages' }));
    expect(screen.getByRole('heading', { name: 'Messages', level: 1 })).toBeVisible();
  });
});
