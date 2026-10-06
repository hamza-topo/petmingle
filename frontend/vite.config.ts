import { loadEnv } from 'vite';
import { validateBuildEnvironment } from './scripts/build-environment.mjs';
import react from '@vitejs/plugin-react';
import tailwindcss from '@tailwindcss/vite';
import { defineConfig } from 'vitest/config';

export default defineConfig(({ command, mode }) => {
  if (command === 'build') validateBuildEnvironment(loadEnv(mode, process.cwd(), 'VITE_'), mode);
  return {
  plugins: [react(), tailwindcss()],
  test: {
    environment: 'jsdom',
    setupFiles: ['./src/test/setup.ts'],
    css: true,
    clearMocks: true,
  },
  };
});
