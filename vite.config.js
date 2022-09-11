import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
  plugins: [
    laravel({
      input: [
        'Modules/Core/Resources/assets/js/app.js',
        'Modules/Core/Resources/assets/sass/app.sass',

        'Modules/Dashboard/Resources/assets/js/app.js',
        'Modules/Dashboard/Resources/assets/sass/app.sass',

        'Modules/Dashboard/Resources/assets/js/pages/show-orders.js',
      ],
      refresh: true,
    }),
  ],
});
