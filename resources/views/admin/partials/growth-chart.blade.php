<div class="card">
    <div class="card-header">
        <span class="card-title">Crecimiento de usuarios</span>
    </div>
    <div class="card-body">
        <div class="chart-wrap">
            <canvas id="growthChart"
                data-labels='{{ $crecimientoUsuarios->pluck("month")->toJson() }}'
                data-values='{{ $crecimientoUsuarios->pluck("count")->toJson() }}'>
            </canvas>
        </div>
    </div>
</div>
