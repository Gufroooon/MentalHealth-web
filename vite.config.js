/**
 * Konfigurasi bundler frontend. Vite menggabungkan entry CSS dan JavaScript,
 * memuat plugin Laravel, serta memantau perubahan Blade untuk refresh.
 */
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
});
