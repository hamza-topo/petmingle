import { render, screen, waitFor } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { MemoryRouter } from 'react-router';
import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';
import { App } from '../../app/App';
import { PetCreatePage } from './PetCreatePage';

import { useAuth } from '../../auth/AuthProvider';
import { authenticatedAuthState } from '../../test/authFixtures';

vi.mock('../../auth/AuthProvider', () => ({
  useAuth: vi.fn(),
}));

const mockedUseAuth = vi.mocked(useAuth);

beforeEach(() => {
  mockedUseAuth.mockReturnValue(authenticatedAuthState());

  vi.stubGlobal(
    'URL',
    Object.assign(URL, {
      createObjectURL: vi.fn(() => 'blob:pet-preview'),
      revokeObjectURL: vi.fn(),
    }),
  );
});
afterEach(() => vi.unstubAllGlobals());
function renderForm() { return render(<MemoryRouter initialEntries={['/pet/create']}><App /></MemoryRouter>); }

describe('Pet profile creation', () => {
  it('renders its route, sections and first progress step', () => {
    renderForm();
    expect(screen.getByRole('heading', { level: 1, name: /Let’s find/ })).toBeVisible();
    for (const name of ['Add a photo', 'Basic information', 'Personality', 'Playdate preferences']) expect(screen.getByRole('region', { name })).toBeVisible();
    expect(screen.getByText('Pet Info').closest('li')).toHaveAttribute('aria-current', 'step');
    expect(screen.getByRole('link', { name: 'Home' })).not.toHaveAttribute('aria-current');
    expect(screen.getByRole('textbox', { name: 'Pet name' })).toHaveValue('Nala');
  });
  it('requires a nonblank pet name before local submission', async () => {
    const user = userEvent.setup(); renderForm();
    await user.clear(screen.getByRole('textbox', { name: 'Pet name' }));
    await user.type(screen.getByRole('textbox', { name: 'Pet name' }), '   ');
    await user.click(screen.getByRole('button', { name: 'Continue' }));
    expect(await screen.findByRole('alert')).toHaveTextContent('Enter your pet’s name.');
    expect(screen.queryByRole('status')).not.toBeInTheDocument();
  });
  it('submits selected traits and preferences through the local handler', async () => {
    const user = userEvent.setup(); const submit = vi.fn();
    render(<MemoryRouter><PetCreatePage onLocalSubmit={submit} /></MemoryRouter>);
    expect(screen.getByRole('checkbox', { name: 'Playful' })).toBeChecked();
    await user.click(screen.getByRole('checkbox', { name: 'Friendly' }));
    await user.click(screen.getByRole('checkbox', { name: 'Playful' }));
    await user.selectOptions(screen.getByLabelText('Energy level'), 'Low energy');
    await user.selectOptions(screen.getByLabelText('Ideal playdate type'), 'Gentle play');
    await user.click(screen.getByRole('button', { name: 'Continue' }));
    await waitFor(() => expect(submit).toHaveBeenCalledWith(expect.objectContaining({ name: 'Nala', traits: ['Friendly'], energy: 'Low energy', playdate: 'Gentle play', photo: null })));
    expect(screen.getByRole('status')).toHaveTextContent('Pet details are valid.');
  });
  it('previews, replaces and removes a local photo, releasing object URLs', async () => {
    const user = userEvent.setup(); const { unmount } = renderForm();
    const file = new File(['photo'], 'pet.png', { type: 'image/png' });
    await user.upload(screen.getByLabelText('Pet photo'), file);
    expect(await screen.findByRole('img', { name: 'Selected pet photo' })).toHaveAttribute('src', 'blob:pet-preview');
    await user.upload(screen.getByLabelText('Pet photo'), new File(['another'], 'pet2.jpg', { type: 'image/jpeg' }));
    expect(URL.revokeObjectURL).toHaveBeenCalledTimes(1);
    await user.click(screen.getByRole('button', { name: 'Remove photo' }));
    expect(screen.queryByRole('img', { name: 'Selected pet photo' })).not.toBeInTheDocument();
    expect(URL.revokeObjectURL).toHaveBeenCalledTimes(2);
    await user.upload(screen.getByLabelText('Pet photo'), file);
    unmount();
    expect(URL.revokeObjectURL).toHaveBeenCalledTimes(3);
  });
  it('rejects unsupported and oversized photos without submitting', async () => {
    const user = userEvent.setup({ applyAccept: false }); renderForm();
    await user.upload(screen.getByLabelText('Pet photo'), new File(['gif'], 'pet.gif', { type: 'image/gif' }));
    await user.click(screen.getByRole('button', { name: 'Continue' }));
    expect(await screen.findByRole('alert')).toHaveTextContent('Choose a JPG or PNG image.');
    const large = new File(['png'], 'large.png', { type: 'image/png' });
    Object.defineProperty(large, 'size', { value: 10 * 1024 * 1024 + 1 });
    await user.upload(screen.getByLabelText('Pet photo'), large);
    expect(await screen.findByRole('alert')).toHaveTextContent('no larger than 10MB');
    expect(screen.queryByRole('status')).not.toBeInTheDocument();
  });
});
