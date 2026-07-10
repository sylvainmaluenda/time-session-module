import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import tailwindcss from '@tailwindcss/vite';
import path, { resolve } from 'path';

export default defineConfig({
    plugins: [react(), tailwindcss()],

    resolve: {
        alias: {
            '@': path.resolve(__dirname, './src')
        }
    },

    server: {
        host: true,
        port: 5173,
        cors: {
            origin: 'http://demo.psconfigurator-812.myh'
        }
    },

    build: {
        manifest: true,
        cssCodeSplit: false,
        outDir: '../build/dist',
        emptyOutDir: true,

        rollupOptions: {
            input: {
                sessionWidget: resolve(__dirname, 'src/sessionWidget/main.tsx'),
                sessionExpired: resolve(__dirname, 'src/sessionExpired/main.tsx')
            },

            output: {
                entryFileNames: '[name]-[hash].js',
                chunkFileNames: 'chunks/[name]-[hash].js',
                assetFileNames: 'assets/[name]-[hash][extname]'
            }
        }
    }
});
