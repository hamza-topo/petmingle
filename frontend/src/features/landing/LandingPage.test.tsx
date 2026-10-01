import { render, screen, within } from '@testing-library/react';
import { MemoryRouter } from 'react-router';
import { describe, expect, it } from 'vitest';
import { App } from '../../app/App';
import { featuredPets } from './landing.fixtures';

function renderHome() {
  return render(<MemoryRouter initialEntries={['/']}><App /></MemoryRouter>);
}

describe('Landing page', () => {
  it('renders the Home screen and its main sections', () => {
    renderHome();
    expect(screen.getByRole('heading', { level: 1, name: 'Find their people' })).toBeVisible();
    expect(screen.getByRole('region', { name: 'Why PetMingle' })).toBeVisible();
    expect(screen.getByRole('heading', { name: 'How It Works' })).toBeVisible();
    expect(screen.getByRole('heading', { name: 'Featured Pets' })).toBeVisible();
    expect(screen.getByRole('heading', { name: 'More friends. Brighter days.' })).toBeVisible();
  });

  it('renders primary navigation with Home active and real section destinations', () => {
    renderHome();
    const nav = within(screen.getByRole('navigation', { name: 'Primary navigation' }));
    expect(nav.getByRole('link', { name: 'Home' })).toHaveAttribute('aria-current', 'page');
    expect(nav.getByRole('link', { name: 'Explore' })).toHaveAttribute('href', '/discover');
    expect(nav.getByRole('link', { name: 'How It Works' })).toHaveAttribute('href', '#how-it-works');
    expect(nav.getByRole('button', { name: 'Stories' })).toBeDisabled();
    expect(nav.getByRole('button', { name: 'Resources' })).toBeDisabled();
  });

  it('renders the primary CTA without pretending signup is implemented', () => {
    renderHome();
    const hero = within(screen.getByRole('region', { name: 'Find their people' }));
    expect(hero.getByRole('button', { name: /Get Started/ })).toBeVisible();
    expect(hero.getByRole('button', { name: /Get Started/ })).toBeDisabled();
    expect(screen.getAllByRole('button', { name: /Get Started/ })).toHaveLength(3);
    expect(hero.getByRole('link', { name: 'Explore Pets' })).toHaveAttribute('href', '/discover');
  });

  it('renders all featured pet cards from the typed fixtures and labels missing images', () => {
    renderHome();
    const list = within(screen.getByRole('list', { name: 'Featured pets' }));
    expect(list.getAllByRole('listitem')).toHaveLength(featuredPets.length);
    for (const pet of featuredPets) {
      const card = within(list.getByRole('article', { name: pet.name }));
      expect(card.getByText(pet.breed)).toBeVisible();
      expect(card.getByRole('img')).toHaveAccessibleName(/placeholder; reference asset unavailable/);
      expect(card.getByRole('button', { name: `Save ${pet.name}` })).toBeDisabled();
    }
  });

});
