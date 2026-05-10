import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

const ddevHostname = process.env.DDEV_HOSTNAME;
const isDdev = Boolean(ddevHostname);

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    server: isDdev
        ? {
            host: '0.0.0.0',
            port: 5173,
            strictPort: true,
            origin: `https://${ddevHostname}:5173`,
            hmr: {
                host: ddevHostname,
                protocol: 'wss',
                port: 5173,
            },
        }
        : undefined,
});
