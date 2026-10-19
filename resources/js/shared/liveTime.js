import { relativeTime } from './relativeTime.js';

(() => {
    function updateAll() {
        document.querySelectorAll('[data-live-time]').forEach(el => {
            const ts = parseInt(el.getAttribute('data-live-time'), 10);
            if (!isNaN(ts)) {
                el.textContent = relativeTime(ts * 1000);
            }
        });
    }

    updateAll();
    setInterval(updateAll, 1000);
})();
