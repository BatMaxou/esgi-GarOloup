const CACHE_NAME = 'garoloup-v1';

const STATIC_ASSETS = ['/offline.html', '/icon.svg', '/images/night-camp-background.png'];

const NETWORK_ONLY = [
  '/.well-known/mercure',
  '/api/',
];

const FONT_HOSTS = ['fonts.googleapis.com', 'fonts.gstatic.com'];

const isNetworkOnly = (url) => NETWORK_ONLY.some((pattern) => url.includes(pattern));

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => cache.addAll(STATIC_ASSETS)),
  );

  self.skipWaiting();
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) =>
      Promise.all(keys.filter((key) => key !== CACHE_NAME).map((key) => caches.delete(key))),
    ),
  );

  self.clients.claim();
});

self.addEventListener('fetch', (event) => {
  if (event.request.method !== 'GET') {
    return;
  }

  const url = event.request.url;

  if (isNetworkOnly(url)) {
    return;
  }

  if (FONT_HOSTS.some((h) => url.includes(h)) || url.includes('/_next/static/') || url.includes('/fonts/')) {
    event.respondWith(
      caches.match(event.request).then(
        (cached) =>
          cached ||
          fetch(event.request).then((response) => {
            const clone = response.clone();
            caches.open(CACHE_NAME).then((cache) => cache.put(event.request, clone));

            return response;
          }),
      ),
    );
    return;
  }

  event.respondWith(
    fetch(event.request)
      .then((response) => {
        if (response.ok) {
          const clone = response.clone();
          caches.open(CACHE_NAME).then((cache) => cache.put(event.request, clone));
        }

        return response;
      })
      .catch(() => caches.match(event.request).then((cached) => cached || caches.match('/offline.html'))),
  );
});
