import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    base: 'https://wniwifi.humangovrj.click/', // Definindo base com HTTPS
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: 'https://wniwifi.humangovrj.click',
                    includeAbsolute: true,
                },
            },
        }),
    ],
    server: {
        host: '98.82.236.251',
        port: 5173,
        https: true,
        watch: {
            usePolling: true,
        },
    },
});
