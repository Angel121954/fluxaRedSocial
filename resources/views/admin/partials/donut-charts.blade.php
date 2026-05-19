<div class="card">
    <div class="card-header">
        <span class="card-title">Sugerencias por estado</span>
    </div>
    <div class="donuts-row">
        <div class="donut-pane" style="border-right:none">
            <div class="donut-chart-wrap">
                <canvas id="sugChart" width="148" height="148"
                    data-labels='{{ $suggestionsByStatus->keys()->toJson() }}'
                    data-values='{{ $suggestionsByStatus->values()->toJson() }}'>
                </canvas>
                <div class="donut-center">
                    <div class="dc-num">{{ $suggestionsCount }}</div>
                    <div class="dc-sub">Total</div>
                </div>
            </div>
            <div class="donut-legend">
                @foreach ($suggestionsByStatus as $status => $count)
                    @php
                        $pct = $suggestionsCount > 0 ? round(($count / $suggestionsCount) * 100, 1) : 0;
                        $dotColors = [
                            'pending' => '#f59e0b',
                            'reviewing' => '#3b82f6',
                            'approved' => '#22c55e',
                            'rejected' => '#ef4444',
                        ];
                        $labels = [
                            'pending' => 'Pendientes',
                            'reviewing' => 'En revisión',
                            'approved' => 'Aprobadas',
                            'rejected' => 'Rechazadas',
                        ];
                    @endphp
                    <div class="legend-row">
                        <div class="legend-left">
                            <div class="legend-dot" style="background:{{ $dotColors[$status] ?? '#12b3b6' }}"></div>
                            <span class="legend-lbl">{{ $labels[$status] ?? $status }}</span>
                        </div>
                        <div class="legend-nums">
                            <span class="legend-val">{{ $count }}</span>
                            <span class="legend-pct">({{ $pct }}%)</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
