import {
  render,
  screen,
} from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import {
  describe,
  expect,
  it,
  vi,
} from 'vitest';

import { ApiState } from './ApiState';

describe('ApiState', () => {
  it('announces loading and empty states as status', () => {
    const { rerender } = render(
      <ApiState
        kind="loading"
        message="Loading profile..."
      />,
    );

    expect(
      screen.getByRole('status'),
    ).toHaveTextContent('Loading profile...');

    rerender(
      <ApiState
        kind="empty"
        message="No results."
      />,
    );

    expect(
      screen.getByRole('status'),
    ).toHaveTextContent('No results.');
  });

  it('announces failures and invokes an explicit retry', async () => {
    const retry = vi.fn();
    const user = userEvent.setup();

    render(
      <ApiState
        kind="error"
        title="Could not load profile"
        message="Please try again."
        onRetry={retry}
      />,
    );

    expect(
      screen.getByRole('alert'),
    ).toHaveTextContent('Please try again.');

    await user.click(
      screen.getByRole('button', {
        name: 'Try again',
      }),
    );

    expect(retry).toHaveBeenCalledTimes(1);
  });

  it('does not render a retry when none is supplied', () => {
    render(
      <ApiState
        kind="error"
        message="Access denied."
      />,
    );

    expect(
      screen.queryByRole('button', {
        name: 'Try again',
      }),
    ).not.toBeInTheDocument();
  });
});
