import { render, screen, waitFor } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { describe, expect, it, vi } from 'vitest';
import { MessageComposer } from './MessageComposer';

describe('Message composer accessibility', () => {
  it('sends from the keyboard and restores focus after completion', async () => {
    const user = userEvent.setup();
    const onSend = vi.fn().mockResolvedValue(undefined);
    render(<MessageComposer onSend={onSend} />);
    await user.tab();
    const input = screen.getByRole('textbox', { name: 'Write a message' });
    expect(input).toHaveFocus();
    await user.type(input, 'Hello{Enter}');
    expect(onSend).toHaveBeenCalledWith('Hello');
    await waitFor(() => expect(input).toHaveValue(''));
    expect(input).toHaveFocus();
  });
  it('connects send errors to the input and announces them', () => {
    render(<MessageComposer onSend={vi.fn()} error="Please retry." />);
    expect(screen.getByRole('textbox')).toHaveAccessibleDescription('Please retry.');
    expect(screen.getByRole('textbox')).toHaveAttribute('aria-invalid', 'true');
    expect(screen.getByRole('alert')).toHaveTextContent('Please retry.');
  });
});
