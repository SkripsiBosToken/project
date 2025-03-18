import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
// import L from "leaflet";
// import "leaflet/dist/leaflet.css";

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            '@fonts': '/resources/fonts',
        },
    },
});
