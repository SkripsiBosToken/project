import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

/*
 * Alamat dev server Vite.
 *
 * Menjalankan `vite --host` tanpa nilai membuat Vite bind ke semua interface,
 * dan laravel-vite-plugin menulis "http://[::]:5173" ke public/hot. [::] adalah
 * alamat WILDCARD untuk binding, bukan alamat yang bisa dihubungi browser —
 * akibatnya seluruh CSS/JS gagal dimuat dan halaman tampil tanpa styling.
 *
 * `origin` di bawah memaksa URL yang ditulis ke public/hot selalu berupa
 * alamat yang benar-benar bisa diakses.
 *
 * Untuk mengakses dari HP / perangkat lain di jaringan yang sama:
 *   VITE_DEV_HOST=192.168.x.x npm run dev            (Git Bash)
 *   $env:VITE_DEV_HOST="192.168.x.x"; npm run dev    (PowerShell)
 */
const devHost = process.env.VITE_DEV_HOST || '127.0.0.1';
const devPort = Number(process.env.VITE_DEV_PORT || 5173);
const isLan = devHost !== '127.0.0.1' && devHost !== 'localhost';

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
    server: {
        // true = bind ke semua interface (dibutuhkan untuk akses LAN),
        // selain itu cukup loopback.
        host: isLan ? true : devHost,
        port: devPort,
        // Gagal terang-terangan bila port sedang dipakai, daripada diam-diam
        // pindah port sementara public/hot menyimpan port yang lama.
        strictPort: true,
        origin: `http://${devHost}:${devPort}`,
        hmr: { host: devHost },
    },
});
