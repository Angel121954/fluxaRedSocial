/* ═══════════════════════════════════════════════════════════
   resources/js/diary/countdown.js
   Cuenta regresiva en tiempo real hasta el cierre del Diario
══════════════════════════════════════════════════════════ */

export function initDiaryCountdown() {
    const timer = document.getElementById('diary-countdown-timer');
    if (!timer) return;

    const closesAt = parseInt(timer.dataset.closesAt, 10) * 1000;
    if (isNaN(closesAt)) return;

    function update() {
        const remaining = Math.max(0, closesAt - Date.now());

        if (remaining === 0) {
            timer.textContent = 'Cerrado';
            timer.closest('.diary-countdown')?.classList.add('diary-countdown--closed');
            return;
        }

        const totalSeconds = Math.floor(remaining / 1000);
        const hours        = Math.floor(totalSeconds / 3600);
        const minutes      = Math.floor((totalSeconds % 3600) / 60);
        const seconds      = totalSeconds % 60;

        timer.textContent = [hours, minutes, seconds]
            .map(n => String(n).padStart(2, '0'))
            .join(':');
    }

    update();
    setInterval(update, 1000);
}
