@if ($valor !== null)
<div class="stat-delta {{ $valor >= 0 ? 'delta-up' : 'delta-down' }}">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <line x1="12" y1="19" x2="12" y2="5" />
        <polyline points="5 12 12 5 19 12" />
    </svg>
    <span>{{ number_format(abs($valor), 1, ',', '.') }}%</span>
</div>
<div class="stat-vs">vs. periodo anterior</div>
@endif
