/* ═══════════════════════════════════════════════════════════
   resources/js/jobs/timeTicker.js
   Actualiza .job-card__date cada 1 segundo (relativo: "Hace 5m")
════════════════════════════════════════════════════════════ */

export function startJobTimeUpdates() {
    updateJobTimes();
    setInterval(updateJobTimes, 1000);
}

function updateJobTimes() {
    const dates = document.querySelectorAll('.job-card__date[data-timestamp]');
    if (!dates.length) return;

    const now = Date.now();

    dates.forEach(el => {
        const ts = parseInt(el.dataset.timestamp);
        if (!ts) return;

        const diff = Math.floor((now - ts) / 1000);
        let text;

        if (diff < 1) {
            text = 'Publicado ahora';
        } else if (diff < 60) {
            text = 'Publicado hace ' + diff + 's';
        } else if (diff < 3600) {
            text = 'Publicado hace ' + Math.floor(diff / 60) + 'm';
        } else if (diff < 86400) {
            text = 'Publicado hace ' + Math.floor(diff / 3600) + 'h';
        } else {
            const days = Math.floor(diff / 86400);
            text = days === 1 ? 'Publicado ayer' : 'Publicado hace ' + days + 'd';
        }

        if (el.textContent !== text) {
            el.textContent = text;
        }
    });
}
