import _ from 'lodash';
window._ = _;

import axios from 'axios';
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'http') === 'https',
    enabledTransports: ['ws', 'wss'],
    enableClientEvents: true,
    authorizer: (channel, options) => {
        return {
            authorize: (socketId, callback) => {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                fetch('/broadcasting/auth', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({
                        socket_id: socketId,
                        channel_name: channel.name,
                    }),
                })
                    .then(response => response.json())
                    .then(data => callback(null, data))
                    .catch(error => callback(error));
            },
        };
    },
});

/* window.Echo.connector.pusher.connection.bind('connected', () => {
    console.log('[Reverb] Conectado al servidor WebSocket');
});

window.Echo.connector.pusher.connection.bind('disconnected', () => {
    console.log('[Reverb] Desconectado del servidor WebSocket');
});

window.Echo.connector.pusher.connection.bind('error', (err) => {
    console.error('[Reverb] Error de conexión:', err);
}); */