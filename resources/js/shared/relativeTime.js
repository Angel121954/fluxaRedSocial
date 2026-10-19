// relativeTime.js - Tiempo relativo compartido ("hace 2 meses", "hace 4 días")

const UNITS = [
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

export function relativeTime(timestampMs) {
    const diff = Math.floor((Date.now() - timestampMs) / 1000);
    if (diff < 0) return 'recién';

    for (const unit of UNITS) {
        const count = Math.floor(diff / unit.seconds);
        if (count >= 1) {
            return `hace ${count} ${pluralize(unit.label, count)}`;
        }
    }

    return 'recién';
}
