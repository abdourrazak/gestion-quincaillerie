import { wayfinder } from '@laravel/vite-plugin-wayfinder';
import tailwindcss from '@tailwindcss/vite';
import react from '@vitejs/plugin-react';
import laravel from 'laravel-vite-plugin';
import { defineConfig } from 'vite';

// Désactiver Wayfinder en production/CI (Vercel)
const isProduction = process.env.NODE_ENV === 'production';
const isCI = process.env.CI === 'true' || process.env.VERCEL === '1';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.tsx'],
            ssr: 'resources/js/ssr.tsx',
            refresh: true,
        }),
        react({
            babel: {
                plugins: ['babel-plugin-react-compiler'],
            },
        }),
        tailwindcss(),
        // Désactiver Wayfinder sur Vercel (pas de PHP disponible)
        ...(isCI ? [] : [
            wayfinder({
                formVariants: true,
            }),
        ]),
    ],
    esbuild: {
        jsx: 'automatic',
    },
});
