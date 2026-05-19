import Chart from 'chart.js/auto';

(function () {
    /* ── Mobile sidebar ────────────────────────────── */
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');
    const mbToggle = document.getElementById('mbToggle');
    const sbClose = document.getElementById('sidebarClose');

    if (sidebar && overlay && mbToggle && sbClose) {
        function openSidebar() {
            sidebar.classList.add('open');
            overlay.classList.add('visible');
        }

        function closeSidebar() {
            sidebar.classList.remove('open');
            overlay.classList.remove('visible');
        }

        function toggleSidebar() {
            if (window.innerWidth > 900) {
                sidebar.classList.toggle('collapsed');
            } else {
                closeSidebar();
            }
        }

        mbToggle.addEventListener('click', openSidebar);
        sbClose.addEventListener('click', toggleSidebar);
        overlay.addEventListener('click', closeSidebar);
    }

    /* ── Chart helpers ─────────────────────────────── */
    Chart.defaults.font.family = 'Inter, -apple-system, sans-serif';

    const tooltipDefaults = {
        backgroundColor: '#0b0f1a',
        titleColor: '#a0aabe',
        bodyColor: '#ffffff',
        padding: 10,
        cornerRadius: 8,
        displayColors: false,
        titleFont: {
            size: 11,
            weight: '500',
        },
        bodyFont: {
            size: 13,
            weight: '700',
        },
    };

    /* ── Area chart: Crecimiento ───────────────────── */
    const growthCanvas = document.getElementById('growthChart');
    if (growthCanvas) {
        const labels = JSON.parse(growthCanvas.dataset.labels || '[]');
        const data = JSON.parse(growthCanvas.dataset.values || '[]');

        if (labels.length && data.length) {
            const ctx = growthCanvas.getContext('2d');
            const grad = ctx.createLinearGradient(0, 0, 0, 200);
            grad.addColorStop(0, 'rgba(18,179,182,.14)');
            grad.addColorStop(1, 'rgba(18,179,182,0)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels,
                    datasets: [{
                        data,
                        borderColor: '#12b3b6',
                        borderWidth: 2,
                        fill: true,
                        backgroundColor: grad,
                        tension: 0.42,
                        pointRadius: 0,
                        pointHoverRadius: 4,
                        pointHoverBackgroundColor: '#12b3b6',
                        pointHoverBorderColor: '#fff',
                        pointHoverBorderWidth: 2,
                    }],
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
                            display: false,
                        },
                        tooltip: {
                            ...tooltipDefaults,
                            callbacks: {
                                title: (i) => i[0].label,
                                label: (i) => `${i.raw.toLocaleString('es-CO')} usuarios`,
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
                                color: '#a0aabe',
                                font: {
                                    size: 11,
                                },
                                maxTicksLimit: 8,
                                maxRotation: 0,
                            },
                        },
                        y: {
                            min: 0,
                            grid: {
                                color: 'rgba(0,0,0,.04)',
                            },
                            border: {
                                display: false,
                                dash: [3, 3],
                            },
                            ticks: {
                                color: '#a0aabe',
                                font: {
                                    size: 11,
                                },
                                padding: 6,
                                callback: (v) => v >= 1000 ? (v / 1000).toFixed(1) + 'k' : v,
                            },
                        },
                    },
                },
            });
        }
    }

    /* ── Donut chart: Sugerencias ──────────────────── */
    const sugCanvas = document.getElementById('sugChart');
    if (sugCanvas) {
        const labels = JSON.parse(sugCanvas.dataset.labels || '[]');
        const data = JSON.parse(sugCanvas.dataset.values || '[]');
        const total = data.reduce((a, b) => a + b, 0);

        if (labels.length && data.length) {
            const palette = ['#f59e0b', '#3b82f6', '#22c55e', '#ef4444', '#8b5cf6'];

            new Chart(sugCanvas.getContext('2d'), {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data,
                        backgroundColor: labels.map((_, i) => palette[i % palette.length]),
                        borderWidth: 2.5,
                        borderColor: '#fff',
                        hoverOffset: 3,
                    }],
                },
                options: {
                    responsive: false,
                    cutout: '74%',
                    plugins: {
                        legend: {
                            display: false,
                        },
                        tooltip: {
                            ...tooltipDefaults,
                            callbacks: {
                                label: (i) => `${i.raw} (${((i.raw / total) * 100).toFixed(1)}%)`,
                            },
                        },
                    },
                },
            });
        }
    }
})();
