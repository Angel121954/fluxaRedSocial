(() => {
    const units = [
        { label: 'año', seconds: 31536000 },
        { label: 'mes', seconds: 2592000 },
        { label: 'semana', seconds: 604800 },
        { label: 'día', seconds: 86400 },
        { label: 'hora', seconds: 3600 },
        { label: 'minuto', seconds: 60 },
        { label: 'segundo', seconds: 1 },
    ];

    function pluralize(word, count) {
        if (count === 1) return word;
        if (word === 'mes') return 'meses';
        return word + 's';
    }

    function relativeTime(timestamp) {
        const diff = Math.floor((Date.now() / 1000) - timestamp);
        if (diff < 0) return 'recién';

        for (const unit of units) {
            const count = Math.floor(diff / unit.seconds);
            if (count >= 1) {
                return `hace ${count} ${pluralize(unit.label, count)}`;
            }
        }
        return 'recién';
    }

    function updateAll() {
        document.querySelectorAll('[data-live-time]').forEach(el => {
            const ts = parseInt(el.getAttribute('data-live-time'), 10);
            if (!isNaN(ts)) {
                el.textContent = relativeTime(ts);
            }
        });
    }

    if (document.querySelector('[data-live-time]')) {
        updateAll();
        setInterval(updateAll, 1000);
    }
})();
