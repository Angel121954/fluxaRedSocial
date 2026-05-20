/* ═══════════════════════════════════════════════════════════
   resources/js/diary/countdown.js
   Cuenta regresiva en tiempo real hasta el cierre del Diario
══════════════════════════════════════════════════════════ */

export function initDiaryCountdown() {
    const timer = document.getElementById('diary-countdown-timer');
    if (!timer) return;

    const closesAt = parseInt(timer.dataset.closesAt, 10) * 1000;
    if (isNaN(closesAt)) return;

    let interval;

    function onClosed() {
        const label = document.getElementById('diary-countdown-label');
        const closedMsg = document.getElementById('diary-countdown-closed');
        const replyBox = document.getElementById('diary-reply-box');
        const replyClosed = document.getElementById('diary-reply-closed');

        if (label) label.style.display = 'none';
        if (timer) timer.style.display = 'none';
        if (closedMsg) closedMsg.style.display = 'inline';

        if (replyBox) replyBox.style.display = 'none';
        if (replyClosed) replyClosed.style.display = 'flex';

        if (interval) clearInterval(interval);
    }

    function update() {
        const remaining = Math.max(0, closesAt - Date.now());

        if (remaining === 0) {
            onClosed();
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
    interval = setInterval(update, 1000);
}
