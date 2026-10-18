self.addEventListener('install', (event) => {
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(self.clients.claim());
});

self.addEventListener('push', (event) => {
    let data = {};
    try {
        data = event.data ? event.data.json() : {};
    } catch (e) {
        data = {};
    }

    const title = data.title || 'Fluxa';
    const options = {
        body: data.body || 'Tienes una nueva notificación en Fluxa',
        icon: data.icon || '/img/logo.png',
        badge: data.badge || '/img/logo.png',
        data: {
            url: data.link || '/',
        },
        tag: data.tag || undefined,
    };

    event.waitUntil(self.registration.showNotification(title, options));
});

self.addEventListener('notificationclick', (event) => {
    event.notification.close();

    const targetUrl = event.notification.data?.url || '/';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then((windowClients) => {
            for (const client of windowClients) {
                if (client.url.includes(window.location.origin) && 'focus' in client) {
                    return client.navigate(targetUrl).then((c) => c?.focus());
                }
            }

            return clients.openWindow(targetUrl);
        })
    );
});
