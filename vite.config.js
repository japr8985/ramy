import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import vuetify from 'vite-plugin-vuetify';
import { VitePWA } from 'vite-plugin-pwa';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.ts'],
            refresh: true,
        }),
        vue(),
        vuetify({ autoImport: true }),
        VitePWA({
            registerType: 'autoUpdate',
            injectRegister: 'auto',
            outDir: 'public', // Fuerza la salida de sw.js a la raíz de public/
            scope: '/',
            base: '/',
            devOptions: {
                enabled: false,
            },
            filename: 'sw.js',
            manifest: {
                name: 'Ramy Store',
                short_name: 'RamyStore',
                description: 'App de Ramy',
                theme_color: '#0A0C13',
                background_color: '#0A0C13',
                display: 'standalone',
                start_url: '/',
                icons: [
                    {
                        src: '/favicon/icon-192x192.png',
                        sizes: '192x192',
                        type: 'image/png',
                    },
                    {
                        src: '/favicon/icon-512x512.png',
                        sizes: '512x512',
                        type: 'image/png',
                    },
                    {
                        src: '/favicon/maskable-icon-512x512.png',
                        sizes: '512x512',
                        type: 'image/png',
                        purpose: 'any maskable',
                    },
                ],
            },
            workbox: {
                globPatterns: ['**/*.{js,css,ico,png,svg,woff2,ttf,eot}'],
                globIgnores: ['**/manifest.webmanifest', '**/site.webmanifest'],
                navigateFallback: null,
            },
        }),
    ],
    resolve: {
        alias: {
            vue: 'vue/dist/vue.esm-bundler.js',
            '@': '/resources/js/src',
        },
    },
});