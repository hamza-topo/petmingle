import { render, screen, within } from '@testing-library/react';
import { MemoryRouter } from 'react-router';
import { describe, expect, it } from 'vitest';
import { App } from '../../app/App';

function renderHome() {
  return render(<MemoryRouter initialEntries={['/']}><App /></MemoryRouter>);
}

describe('Landing page', () => {
  it('presents the brand promise and local artwork without fixture profiles', () => {
    renderHome();
    expect(screen.getByRole('heading', { level: 1, name: 'Their paths cross. Yours do too.' })).toBeVisible();
    expect(screen.getByRole('heading', { name: 'How It Works' })).toBeVisible();
    const photo = screen.getByRole('img', { name: 'Two dogs and their people meeting on a sunny park path' });
    expect(photo.getAttribute('src')).toMatch(/meeting\.webp/);
    expect(photo).toHaveAttribute('fetchpriority', 'high');
    expect(screen.queryByText(/placeholder/i)).not.toBeInTheDocument();
    expect(screen.queryByRole('list', { name: 'Featured pets' })).not.toBeInTheDocument();
  });

  it('keeps navigation and onboarding connected to existing routes', () => {
    renderHome();
    const nav = within(screen.getByRole('navigation', { name: 'Primary navigation' }));
    expect(nav.getByRole('link', { name: 'Explore' })).toHaveAttribute('href', '/discover');
    expect(nav.getByRole('link', { name: 'How It Works' })).toHaveAttribute('href', '/#how-it-works');
    expect(nav.queryByRole('button')).not.toBeInTheDocument();
    expect(screen.getByRole('link', { name: 'Create a profile' })).toHaveAttribute('href', '/pet/create');
    expect(screen.getByRole('link', { name: 'Sign In' })).toHaveAttribute('href', '/signin');
    const hero = within(screen.getByRole('region', { name: 'Their paths cross. Yours do too.' }));
    expect(hero.getByRole('link', { name: 'Find a connection' })).toHaveAttribute('href', '/discover');
  });

  it('offers a keyboard skip destination and accessible home links', () => {
    renderHome();
    expect(screen.getByRole('link', { name: 'Skip to content' })).toHaveAttribute('href', '#main-content');
    expect(screen.getByRole('main')).toHaveAttribute('id', 'main-content');
    expect(screen.getByRole('main')).toHaveAttribute('tabindex', '-1');
    for (const link of screen.getAllByRole('link', { name: 'PetMingle home' })) {
      expect(link).toHaveAttribute('href', '/');
    }
  });
});
