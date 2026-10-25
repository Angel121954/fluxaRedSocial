import Chart from 'chart.js/auto';

(function () {
    /* ── Barra lateral móvil ───────────────────────── */
    const barraLateral = document.getElementById('sidebar');
    const capa = document.getElementById('overlay');
    const botonMenu = document.getElementById('mbToggle');
    const botonContraer = document.getElementById('sidebarClose');

    if (barraLateral && capa && botonMenu && botonContraer) {
        function abrirBarra() {
            barraLateral.classList.add('open');
            capa.classList.add('visible');
        }

        function cerrarBarra() {
            barraLateral.classList.remove('open');
            capa.classList.remove('visible');
        }

        function alternarBarra() {
            if (window.innerWidth >= 1024) {
                barraLateral.classList.toggle('collapsed');
            } else {
                cerrarBarra();
            }
        }

        botonMenu.addEventListener('click', abrirBarra);
        botonContraer.addEventListener('click', alternarBarra);
        capa.addEventListener('click', cerrarBarra);
    }

    /* ── Selector de rango de fechas ───────────────── */
    document.addEventListener('click', (evento) => {
        document.querySelectorAll('details.date-range[open]').forEach((detalle) => {
            if (!detalle.contains(evento.target)) {
                detalle.removeAttribute('open');
            }
        });
    });

    /* ── Variables de diseño ───────────────────────── */
    const estilos = getComputedStyle(document.documentElement);

    function valorVariable(nombre, respaldo) {
        return estilos.getPropertyValue(nombre).trim() || respaldo;
    }

    const acento = valorVariable('--accent', '#12b3b6');
    const textoGrafico = valorVariable('--ink-300', '#6b7789');
    const rejillaGrafico = valorVariable('--border', 'rgba(0,0,0,.075)');
    const superficie = valorVariable('--surface', '#ffffff');

    Chart.defaults.font.family = 'Figtree, -apple-system, sans-serif';

    const configuracionTooltip = {
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

    /* ── Gráfico de área: Crecimiento ──────────────── */
    const lienzoCrecimiento = document.getElementById('growthChart');

    if (lienzoCrecimiento) {
        const etiquetasOriginales = JSON.parse(lienzoCrecimiento.dataset.labels || '[]');
        const datos = JSON.parse(lienzoCrecimiento.dataset.values || '[]');
        const nombresMeses = ['ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'];

        const etiquetas = etiquetasOriginales.map((etiqueta) => {
            const [anio, mes] = etiqueta.split('-');

            return `${nombresMeses[Number(mes) - 1] ?? mes} ${anio}`;
        });

        if (etiquetas.length && datos.length) {
            const contexto = lienzoCrecimiento.getContext('2d');
            const degradado = contexto.createLinearGradient(0, 0, 0, 230);
            degradado.addColorStop(0, 'rgba(18,179,182,.16)');
            degradado.addColorStop(1, 'rgba(18,179,182,0)');

            new Chart(contexto, {
                type: 'line',
                data: {
                    labels: etiquetas,
                    datasets: [{
                        data: datos,
                        borderColor: acento,
                        borderWidth: 2,
                        fill: true,
                        backgroundColor: degradado,
                        tension: 0.42,
                        pointRadius: 0,
                        pointHoverRadius: 4,
                        pointHoverBackgroundColor: acento,
                        pointHoverBorderColor: superficie,
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
                            ...configuracionTooltip,
                            callbacks: {
                                label: (item) => `${item.raw.toLocaleString('es-CO')} usuarios`,
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
                                color: textoGrafico,
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
                                color: rejillaGrafico,
                            },
                            border: {
                                display: false,
                                dash: [3, 3],
                            },
                            ticks: {
                                color: textoGrafico,
                                font: {
                                    size: 11,
                                },
                                padding: 6,
                                callback: (valor) => valor >= 1000 ? (valor / 1000).toFixed(1) + 'k' : valor,
                            },
                        },
                    },
                },
            });
        }
    }

    /* ── Gráficos de dona ──────────────────────────── */
    const paletaPorDefecto = ['#f59e0b', '#3b82f6', '#22c55e', '#ef4444', '#8b5cf6'];

    function dibujarDona(lienzo) {
        const etiquetas = JSON.parse(lienzo.dataset.labels || '[]');
        const datos = JSON.parse(lienzo.dataset.values || '[]');
        const colores = JSON.parse(lienzo.dataset.colors || '[]');
        const paleta = colores.length ? colores : paletaPorDefecto;
        const total = datos.reduce((suma, valor) => suma + valor, 0);

        if (!etiquetas.length || !datos.length) {
            return;
        }

        new Chart(lienzo.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: etiquetas,
                datasets: [{
                    data: datos,
                    backgroundColor: etiquetas.map((_, indice) => paleta[indice % paleta.length]),
                    borderWidth: 2.5,
                    borderColor: superficie,
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
                        ...configuracionTooltip,
                        callbacks: {
                            label: (item) => {
                                const porcentaje = total > 0 ? ((item.raw / total) * 100).toFixed(1) : '0.0';

                                return `${item.label}: ${item.raw} (${porcentaje}%)`;
                            },
                        },
                    },
                },
            },
        });
    }

    document.querySelectorAll('#sugChart, #repChart').forEach(dibujarDona);
})();
