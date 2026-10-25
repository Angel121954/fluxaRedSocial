/**
 * shared/push.js - Notificaciones push (Web Push API + VAPID)
 *
 * - Registra el service worker en toda página autenticada.
 * - Expone funciones para suscribir/dessuscribir el dispositivo.
 * - En la vista de preferencias (#togglePush) conecta el toggle con
 *   permiso + suscripción, y mantiene el estado sincronizado.
 */

(function () {
    const SW_PATH = '/sw.js';
    const VAPID_KEY_META = 'meta[name="vapid-public-key"]';

    function getToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    function getVapidKey() {
        const meta = document.querySelector(VAPID_KEY_META);
        return meta ? meta.getAttribute('content') : '';
    }

    function urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - (base64String.length % 4)) % 4);
        const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);

        for (let i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }

        return outputArray;
    }

    function registerServiceWorker() {
        if (!('serviceWorker' in navigator)) {
            return Promise.resolve(null);
        }

        return navigator.serviceWorker.register(SW_PATH).then(function (registration) {
            return registration;
        });
    }

    function getSubscription(registration) {
        return registration ? registration.pushManager.getSubscription() : Promise.resolve(null);
    }

    async function persistSubscription(subscription) {
        try {
            const response = await fetch('/notifications/push/subscribe', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getToken(),
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    endpoint: subscription.endpoint,
                    public_key: subscription.getKey('p256dh') ? btoa(String.fromCharCode.apply(null, new Uint8Array(subscription.getKey('p256dh')))) : null,
                    auth_token: subscription.getKey('auth') ? btoa(String.fromCharCode.apply(null, new Uint8Array(subscription.getKey('auth')))) : null,
                    content_encoding: 'aesgcm',
                }),
            });

            return response.ok;
        } catch (err) {
            console.error('[Push] Error persistiendo suscripción:', err);
            return false;
        }
    }

    async function removeSubscriptionOnServer(subscription) {
        try {
            const response = await fetch('/notifications/push/subscribe', {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': getToken(),
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    endpoint: subscription ? subscription.endpoint : null,
                }),
            });

            return response.ok;
        } catch (err) {
            console.error('[Push] Error eliminando suscripción:', err);
            return false;
        }
    }

    async function subscribe() {
        const vapidKey = getVapidKey();
        if (!vapidKey || !('serviceWorker' in navigator)) {
            return false;
        }

        let registration;
        try {
            registration = await registerServiceWorker();
            if (!registration) {
                return false;
            }
        } catch (err) {
            console.error('[Push] No se pudo registrar el service worker:', err);
            return false;
        }

        const permission = await Notification.requestPermission();
        if (permission !== 'granted') {
            return false;
        }

        let subscription = await getSubscription(registration);
        if (!subscription) {
            subscription = await registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: urlBase64ToUint8Array(vapidKey),
            });
        }

        return persistSubscription(subscription);
    }

    async function unsubscribe() {
        let registration;
        try {
            registration = await navigator.serviceWorker.getRegistration(SW_PATH);
        } catch (err) {
            registration = null;
        }

        let subscription = null;
        if (registration) {
            subscription = await registration.pushManager.getSubscription();
            if (subscription) {
                await subscription.unsubscribe();
            }
        }

        const ok = await removeSubscriptionOnServer(subscription);

        return ok;
    }

    function initPreferenceToggle() {
        const toggle = document.getElementById('togglePush');
        if (!toggle) {
            return;
        }
        async function syncToggleState() {
            let registration;
            try {
                registration = await navigator.serviceWorker.getRegistration(SW_PATH);
            } catch (err) {
                registration = null;
            }

            let current = false;
            if (registration) {
                const sub = await registration.pushManager.getSubscription();
                current = !!sub;
            }

            if (toggle.checked !== current && 'Notification' in window) {
                const shouldBeChecked = current && Notification.permission === 'granted';
                toggle.checked = shouldBeChecked;
            }
        }

        toggle.addEventListener('change', async function () {
            const isEnabled = toggle.checked;
            toggle.disabled = true;

            let success;
            if (isEnabled) {
                success = await subscribe();
                if (success) {
                    if (window.showToast) {
                        window.showToast('Notificaciones push activadas', 'success');
                    }
                } else {
                    toggle.checked = false;
                    if (window.showToast) {
                        window.showToast('No se pudieron activar las notificaciones push', 'error');
                    }
                }
            } else {
                success = await unsubscribe();
                if (window.showToast) {
                    window.showToast(success ? 'Notificaciones push desactivadas' : 'No se pudieron desactivar las notificaciones push', success ? 'success' : 'error');
                }
            }

            toggle.disabled = false;
        });

        if (document.readyState === 'complete' || document.readyState === 'interactive') {
            syncToggleState();
        } else {
            document.addEventListener('DOMContentLoaded', syncToggleState);
        }
    }

    const deferredInstallPrompt = {
        event: null,
    };

    function getInstallButtons() {
        return Array.from(document.querySelectorAll('#installPwa, #installPwaDesktop'));
    }

    function isStandalone() {
        return window.matchMedia('(display-mode: standalone)').matches
            || window.navigator.standalone === true;
    }

    function showInstallButton() {
        getInstallButtons().forEach(function (btn) {
            btn.classList.remove('hidden');
        });
    }

    function hideInstallButton() {
        getInstallButtons().forEach(function (btn) {
            btn.classList.add('hidden');
        });
    }

    function initInstallPwa() {
        if (!getInstallButtons().length) {
            return;
        }

        if (isStandalone()) {
            hideInstallButton();
            return;
        }

        showInstallButton();

        window.addEventListener('beforeinstallprompt', function (e) {
            e.preventDefault();
            deferredInstallPrompt.event = e;
        });

        window.addEventListener('appinstalled', function () {
            hideInstallButton();
        });

        document.addEventListener('click', function (e) {
            const clicked = e.target.closest('#installPwa, #installPwaDesktop');
            if (!clicked) {
                return;
            }

            if (deferredInstallPrompt.event) {
                deferredInstallPrompt.event.prompt();

                deferredInstallPrompt.event.userChoice.then(function (choice) {
                    if (choice.outcome === 'accepted') {
                        hideInstallButton();
                    }
                    deferredInstallPrompt.event = null;
                });

                return;
            }

            if (window.showToast) {
                window.showToast('Usa el menú del navegador para instalar Fluxa', 'info');
            }
        });
    }

    if ('serviceWorker' in navigator && getVapidKey()) {
        document.addEventListener('DOMContentLoaded', function () {
            registerServiceWorker();
        });
    }

    initPreferenceToggle();
    initInstallPwa();

    window.pushNotifications = {
        subscribe: subscribe,
        unsubscribe: unsubscribe,
    };
})();