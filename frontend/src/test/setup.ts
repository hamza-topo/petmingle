import '@testing-library/jest-dom/vitest';
import { cleanup } from '@testing-library/react';
import { afterEach, vi } from 'vitest';

afterEach(cleanup);

// jsdom has no layout or scrolling implementation.
window.scrollTo = vi.fn();
HTMLElement.prototype.scrollIntoView = vi.fn();
