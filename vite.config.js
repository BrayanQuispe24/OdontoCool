import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import path from 'node:path';

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '');

    return {
        base: env.VITE_ASSET_BASE || '/build/',

        resolve: {
            alias: {
                'ziggy-js': path.resolve('vendor/tightenco/ziggy'),
            },
        },

        plugins: [
            laravel({
                input: 'resources/js/app.js',
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
    };
});