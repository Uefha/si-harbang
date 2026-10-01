/**
 * Service worker SI-HARBANG.
 *
 * Sengaja dibuat MINIMAL: hanya cukup untuk memenuhi syarat "installable"
 * (Chrome/Android mewajibkan service worker terdaftar dengan fetch handler
 * sebelum menawarkan "Tambahkan ke Layar Utama"). Data laporan berubah
 * setiap saat dan harus selalu terbaru, jadi TIDAK melakukan cache agresif
 * untuk halaman/API — hanya file statis (ikon, manifest) yang di-cache
 * sebagai app shell. Semua permintaan lain langsung ke jaringan seperti biasa.
 */

const CACHE_NAME = 'si-harbang-shell-v1';
const APP_SHELL = [
    '/manifest.json',
    '/icons/icon-192.png',
    '/icons/icon-512.png',
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => cache.addAll(APP_SHELL))
    );
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) =>
            Promise.all(keys.filter((k) => k !== CACHE_NAME).map((k) => caches.delete(k)))
        )
    );
    self.clients.claim();
});

self.addEventListener('fetch', (event) => {
    const url = new URL(event.request.url);

    // Hanya app-shell statis yang dilayani dari cache-first; sisanya
    // (halaman, DataTables ajax, form POST, dst.) tetap network seperti biasa.
    if (event.request.method === 'GET' && APP_SHELL.includes(url.pathname)) {
        event.respondWith(
            caches.match(event.request).then((cached) => cached || fetch(event.request))
        );
        return;
    }

    // Untuk navigasi halaman: coba jaringan dulu, kalau benar-benar offline
    // baru tampilkan pesan sederhana (bukan data laporan basi/salah).
    if (event.request.mode === 'navigate') {
        event.respondWith(
            fetch(event.request).catch(() =>
                new Response(
                    '<!doctype html><html lang="id"><meta charset="utf-8"><title>Offline</title>' +
                    '<body style="font-family:sans-serif;text-align:center;padding:3rem;color:#333">' +
                    '<h3>Tidak ada koneksi internet</h3>' +
                    '<p>SI-HARBANG butuh koneksi internet untuk memuat data laporan terbaru. Coba lagi setelah tersambung.</p>' +
                    '</body></html>',
                    { headers: { 'Content-Type': 'text/html; charset=utf-8' } }
                )
            )
        );
    }
});
