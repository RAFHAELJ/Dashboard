import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

// Define a URL base dos assets dependendo do ambiente
const assetUrl = process.env.NODE_ENV === 'production'
    ? 'https://d3j80ooyz4simi.cloudfront.net/build' // URL do CloudFront para produção
    : '/build/'; // URL base para ambiente de desenvolvimento

export default defineConfig({
    base: assetUrl, // Define o caminho base até 'build' sem duplicar 'assets'
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: assetUrl, // Define base até o build apenas
                    includeAbsolute: true,
                },
            },
        }),
    ],
});
