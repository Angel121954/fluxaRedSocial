<div class="card">
    <div class="card-header">
        <span class="card-title">Crecimiento de usuarios</span>
    </div>
    <div class="card-body">
        <div class="chart-wrap">
            <canvas id="growthChart"
                data-labels='{{ $userGrowth->pluck("month")->toJson() }}'
                data-values='{{ $userGrowth->pluck("count")->toJson() }}'>
            </canvas>
        </div>
    </div>
</div>
