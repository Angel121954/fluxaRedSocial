@php
$metaEstados = [
    'pending' => ['etiqueta' => 'Pendientes', 'color' => '#f59e0b'],
    'reviewing' => ['etiqueta' => 'En revisión', 'color' => '#3b82f6'],
    'approved' => ['etiqueta' => 'Aprobadas', 'color' => '#22c55e'],
    'rejected' => ['etiqueta' => 'Rechazadas', 'color' => '#ef4444'],
];

$metaReportes = [
    'usuario' => ['etiqueta' => 'Usuarios', 'color' => '#3b82f6'],
    'proyecto' => ['etiqueta' => 'Proyectos', 'color' => '#f59e0b'],
    'diario' => ['etiqueta' => 'Diario', 'color' => '#8b5cf6'],
    'problema' => ['etiqueta' => 'Problemas', 'color' => '#ef4444'],
];
@endphp

<div class="card">
    <div class="card-header">
        <span class="card-title">Estado de la comunidad</span>
    </div>

    <div class="donuts-row">
        <div class="donut-pane">
            <div class="donut-subtitle">Sugerencias por estado</div>
            <div class="donut-chart-wrap">
                <canvas id="sugChart" width="156" height="156"
                    data-labels='{{ collect(array_column($metaEstados, 'etiqueta'))->toJson() }}'
                    data-values='{{ collect(array_keys($metaEstados))->map(fn ($estado) => $sugerenciasPorEstado[$estado] ?? 0)->toJson() }}'
                    data-colors='{{ collect(array_column($metaEstados, 'color'))->toJson() }}'>
                </canvas>
                <div class="donut-center">
                    <div class="dc-num">{{ number_format($totalSugerencias) }}</div>
                    <div class="dc-sub">Total</div>
                </div>
            </div>
            <div class="donut-legend">
                @foreach ($metaEstados as $estado => $meta)
                @php
                $cantidad = $sugerenciasPorEstado[$estado] ?? 0;
                $porcentaje = $totalSugerencias > 0 ? round(($cantidad / $totalSugerencias) * 100, 1) : 0;
                @endphp
                <div class="legend-row">
                    <div class="legend-left">
                        <div class="legend-dot" style="background:{{ $meta['color'] }}"></div>
                        <span class="legend-lbl">{{ $meta['etiqueta'] }}</span>
                    </div>
                    <div class="legend-nums">
                        <span class="legend-val">{{ $cantidad }}</span>
                        <span class="legend-pct">({{ $porcentaje }}%)</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="donut-pane">
            <div class="donut-subtitle">Reportes por tipo</div>
            <div class="donut-chart-wrap">
                <canvas id="repChart" width="156" height="156"
                    data-labels='{{ collect(array_column($metaReportes, 'etiqueta'))->toJson() }}'
                    data-values='{{ collect(array_keys($metaReportes))->map(fn ($tipo) => $reportesPorTipo[$tipo] ?? 0)->toJson() }}'
                    data-colors='{{ collect(array_column($metaReportes, 'color'))->toJson() }}'>
                </canvas>
                <div class="donut-center">
                    <div class="dc-num">{{ number_format($totalReportes) }}</div>
                    <div class="dc-sub">Total</div>
                </div>
            </div>
            <div class="donut-legend">
                @foreach ($metaReportes as $tipo => $meta)
                @php
                $cantidad = $reportesPorTipo[$tipo] ?? 0;
                $porcentaje = $totalReportes > 0 ? round(($cantidad / $totalReportes) * 100, 1) : 0;
                @endphp
                <div class="legend-row">
                    <div class="legend-left">
                        <div class="legend-dot" style="background:{{ $meta['color'] }}"></div>
                        <span class="legend-lbl">{{ $meta['etiqueta'] }}</span>
                    </div>
                    <div class="legend-nums">
                        <span class="legend-val">{{ $cantidad }}</span>
                        <span class="legend-pct">({{ $porcentaje }}%)</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
