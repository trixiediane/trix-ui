import { resolve } from 'node:path';
import { defineConfig } from 'vite';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig(({ mode }) => ({
  plugins: [tailwindcss()],
  build: mode === 'library'
    ? {
        emptyOutDir: false,
        lib: {
          entry: resolve(import.meta.dirname, 'src/library.js'),
          formats: ['es'],
          fileName: 'index',
          cssFileName: 'trix-ui',
        },
      }
    : undefined,
  server: {
    host: '0.0.0.0',
    port: 5173,
  },
  preview: {
    host: '0.0.0.0',
    port: 4173,
  },
}));
