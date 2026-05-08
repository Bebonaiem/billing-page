import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**', '**/vendor/**'],
        },
        hmr: {
            overlay: false,
        },
    },
    build: {
        cssCodeSplit: true,
        sourcemap: process.env.NODE_ENV === 'development',
        minify: 'esbuild',
        esbuild: {
            drop: ['console', 'debugger'],
        },
    },
});
