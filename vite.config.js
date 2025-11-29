import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
  plugins: [
    laravel({
      input: 'resources/js/app.js',
      ssr: 'resources/js/ssr.js',
      refresh: true,
    }),
    vue({
      template: {
        transformAssetUrls: {
          base: null,
          includeAbsolute: false,
        },
      },
    }),
  ],
  // AJOUTEZ CETTE SECTION :
  server: {
    host: 'localhost',
    port: 5174, // ← Port spécifique
    strictPort: true, // ← Empêche Vite de changer de port si 5174 est occupé
  },
});
