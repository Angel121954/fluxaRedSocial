import Chart from 'chart.js/auto';

(function () {
    const canvas = document.getElementById('activityChart');

    if (!canvas) {
        return;
    }

    const labels = JSON.parse(canvas.dataset.labels || '[]');
    const registrations = JSON.parse(canvas.dataset.registrations || '[]');
    const actions = JSON.parse(canvas.dataset.actions || '[]');

    if (!labels.length) {
        return;
    }

    const styles = getComputedStyle(document.documentElement);

    const token = (name, fallback) => styles.getPropertyValue(name).trim() || fallback;

    const accent = token('--accent', '#12b3b6');
    const warning = token('--warning', '#b45309');
    const ink = token('--ink-300', '#6b7789');
    const grid = token('--border', 'rgba(0, 0, 0, .075)');
    const surface = token('--surface', '#ffffff');

    Chart.defaults.font.family = 'Figtree, -apple-system, sans-serif';

    const tooltip = {
        backgroundColor: '#0b0f1a',
        titleColor: '#a0aabe',
        bodyColor: '#ffffff',
        padding: 10,
        cornerRadius: 8,
        displayColors: true,
        boxWidth: 8,
        boxHeight: 8,
        titleFont: {
            size: 11,
            weight: '500',
        },
        bodyFont: {
            size: 13,
            weight: '700',
        },
    };

    new Chart(canvas.getContext('2d'), {
        type: 'line',
        data: {
            labels,
            datasets: [
                {
                    label: 'Registros',
                    data: registrations,
                    borderColor: accent,
                    backgroundColor: 'rgba(18, 179, 182, .10)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 0,
                    pointHoverRadius: 4,
                    pointHoverBackgroundColor: accent,
                    pointHoverBorderColor: surface,
                    pointHoverBorderWidth: 2,
                },
                {
                    label: 'Acciones',
                    data: actions,
                    borderColor: warning,
                    backgroundColor: 'transparent',
                    borderWidth: 2,
                    borderDash: [4, 4],
                    fill: false,
                    tension: 0.4,
                    pointRadius: 0,
                    pointHoverRadius: 4,
                    pointHoverBackgroundColor: warning,
                    pointHoverBorderColor: surface,
                    pointHoverBorderWidth: 2,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index',
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: {
                        color: ink,
                        boxWidth: 8,
                        boxHeight: 8,
                        usePointStyle: true,
                        pointStyle: 'circle',
                        font: {
                            size: 11,
                            weight: '600',
                        },
                    },
                },
                tooltip: {
                    ...tooltip,
                    callbacks: {
                        label: (item) => `${item.dataset.label}: ${item.raw.toLocaleString('es-CO')}`,
                    },
                },
            },
            scales: {
                x: {
                    grid: {
                        display: false,
                    },
                    border: {
                        display: false,
                    },
                    ticks: {
                        color: ink,
                        font: {
                            size: 11,
                        },
                        maxTicksLimit: 8,
                        maxRotation: 0,
                    },
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: grid,
                    },
                    border: {
                        display: false,
                    },
                    ticks: {
                        color: ink,
                        font: {
                            size: 11,
                        },
                        precision: 0,
                        padding: 6,
                    },
                },
            },
        },
    });
})();
