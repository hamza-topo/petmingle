import { render, screen } from '@testing-library/react';
import { MemoryRouter, Link, Route, Routes } from 'react-router';
import userEvent from '@testing-library/user-event';
import { expect, it } from 'vitest';
import { RouteScroll } from './RouteScroll';

it('moves keyboard focus to the new page landmark after navigation', async () => {
  const user = userEvent.setup();
  render(<MemoryRouter><RouteScroll /><Routes>
    <Route path="/" element={<main tabIndex={-1}><Link to="/next">Next page</Link></main>} />
    <Route path="/next" element={<main tabIndex={-1} aria-label="Next content">Next content</main>} />
  </Routes></MemoryRouter>);
  await user.click(screen.getByRole('link', { name: 'Next page' }));
  expect(screen.getByRole('main', { name: 'Next content' })).toHaveFocus();
});

it('moves focus to an in-page destination as well as scrolling', async () => {
  const user = userEvent.setup();
  render(<MemoryRouter><RouteScroll /><main tabIndex={-1}>
    <Link to="/#details">Details</Link><section id="details" aria-label="Details content">Details content</section>
  </main></MemoryRouter>);
  await user.click(screen.getByRole('link'));
  expect(screen.getByRole('region', { name: 'Details content' })).toHaveFocus();
});
