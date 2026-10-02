import { render, screen, within } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { MemoryRouter } from 'react-router';
import { expect, it } from 'vitest';
import { App } from './App';

it('connects all five screens and the Plus section using existing visible controls', async () => {
  const user = userEvent.setup();
  render(<MemoryRouter><App /></MemoryRouter>);
  await user.click(within(screen.getByRole('region', { name: 'Find their people' })).getByRole('link', { name: /Get Started/ }));
  expect(screen.getByRole('form', { name: 'Create pet profile' })).toBeVisible();
  await user.click(screen.getByRole('link', { name: 'Explore' }));
  await user.click(screen.getByRole('link', { name: /^Profile/ }));
  expect(screen.getByRole('heading', { name: 'Nala', level: 1 })).toBeVisible();
  await user.click(screen.getByRole('link', { name: 'Messages' }));
  expect(screen.getByRole('heading', { name: 'Messages', level: 1 })).toBeVisible();
  await user.click(screen.getByRole('link', { name: 'Explore' }));
  await user.click(screen.getByRole('link', { name: /^PetMingle Plus/ }));
  expect(screen.getByRole('radio', { name: '12 Months' })).toBeChecked();
  expect(HTMLElement.prototype.scrollIntoView).toHaveBeenCalled();
  await user.click(screen.getByRole('link', { name: 'Complete your profile' }));
  expect(screen.getByRole('form', { name: 'Create pet profile' })).toBeVisible();
  await user.click(screen.getByRole('link', { name: 'How It Works' }));
  expect(screen.getByRole('heading', { name: 'Find their people', level: 1 })).toBeVisible();
}, 15000);
