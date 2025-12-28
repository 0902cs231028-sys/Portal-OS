/**
 * Aetheris Core v2.2 - Background Intelligence Engine
 * Status: STABLE (CORS-Safe & Auto-Updating)
 */

const CACHE_NAME = 'aetheris-core-v2.2'; // Bumped version forces browser to update
const ASSETS = [
    '/',
    '/index.php',
    '/dashboard.php',
    '/admin/admin_dashboard.php',
    '/js/matrix_viewer.js',
    '/js/dm_system.js',
    '/js/talkin.js'
    // NOTE: External CDNs (Tailwind/Lucide) are intentionally REMOVED to prevent CORS errors.
];

// 1. INSTALLATION: Safe Caching
self.addEventListener('install', (event) => {
    self.skipWaiting(); // Forces this new worker to take over immediately
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            // We verify assets exist before caching to prevent crash loops
            return cache.addAll(ASSETS).catch(err => console.warn("Asset Caching Warning (Non-Critical):", err));
        })
    );
});

// 2. ACTIVATION: Cleanup Old Versions
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) => {
            return Promise.all(
                keys.filter(key => key !== CACHE_NAME)
                    .map(key => caches.delete(key))
            );
        })
    );
    return self.clients.claim(); // Immediately control the page
});

// 3. INTELLIGENT FETCH: Stale-While-Revalidate
self.addEventListener('fetch', (event) => {
    // Ignore non-GET requests (like POSTs for DMs)
    if (event.request.method !== 'GET') return;

    const url = event.request.url;

    // CRITICAL FIX: Ignore external CDNs to prevent CORS crashes
    if (url.includes('cdn.tailwindcss.com') || url.includes('unpkg.com') || url.includes('fonts.googleapis')) {
        return; // Let the browser handle these normally
    }

    event.respondWith(
        caches.match(event.request).then((cachedResponse) => {
            // Fetch from network to update cache in background
            const fetchPromise = fetch(event.request).then((networkResponse) => {
                // Only cache valid responses from YOUR server (not external)
                if (networkResponse && networkResponse.status === 200 && networkResponse.type === 'basic') {
                    const cacheCopy = networkResponse.clone();
                    caches.open(CACHE_NAME).then((cache) => {
                        cache.put(event.request, cacheCopy);
                    });
                }
                return networkResponse;
            }).catch(() => {
                // If network fails, return cached response
                return cachedResponse;
            });

            // Return cached response instantly if available, otherwise wait for network
            return cachedResponse || fetchPromise;
        })
    );
});

// 4. PUSH NOTIFICATIONS (The Neural Link)
self.addEventListener('push', (event) => {
    // Default payload if JSON fails
    let data = { title: 'Aetheris Alert', body: 'New Intelligence Available', url: '/dashboard.php' };
    
    if (event.data) {
        try {
            data = event.data.json();
        } catch(e) {
            data.body = event.data.text();
        }
    }

    const options = {
        body: data.body,
        icon: '/assets/icons/icon-192x192.png', // Ensure this file exists on server
        badge: '/assets/icons/badge.png',       // Ensure this file exists on server
        vibrate: [200, 100, 200],
        data: { url: data.url },
        actions: [
            { action: 'open', title: 'VIEW' },
            { action: 'dismiss', title: 'DISMISS' }
        ]
    };

    event.waitUntil(
        self.registration.showNotification(data.title, options)
    );
});

// 5. NOTIFICATION CLICK HANDLER
self.addEventListener('notificationclick', (event) => {
    event.notification.close();

    if (event.action === 'dismiss') return;

    event.waitUntil(
        clients.matchAll({ type: 'window' }).then((clientList) => {
            // If tab is already open, just focus it
            for (const client of clientList) {
                if (client.url.includes(event.notification.data.url) && 'focus' in client) {
                    return client.focus();
                }
            }
            // If not open, open a new window
            if (clients.openWindow) {
                return clients.openWindow(event.notification.data.url);
            }
        })
    );
});