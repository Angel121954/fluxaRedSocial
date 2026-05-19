<div class="stats-row">
    <div class="stat-card">
        <div class="stat-icon si-teal">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" />
                <circle cx="9" cy="7" r="4" />
                <path d="M23 21v-2a4 4 0 00-3-3.87" />
                <path d="M16 3.13a4 4 0 010 7.75" />
            </svg>
        </div>
        <div class="stat-body">
            <div class="stat-label">Usuarios totales</div>
            <div class="stat-value">{{ number_format($totalUsers) }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon si-green">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="22 7 13.5 15.5 8.5 10.5 2 17" />
                <polyline points="16 7 22 7 22 13" />
            </svg>
        </div>
        <div class="stat-body">
            <div class="stat-label">Usuarios activos</div>
            <div class="stat-value">{{ number_format($activeUsers) }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon si-blue">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="9 11 12 14 22 4" />
                <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11" />
            </svg>
        </div>
        <div class="stat-body">
            <div class="stat-label">Verificados</div>
            <div class="stat-value">{{ number_format($verifiedUsers) }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon si-amber">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z" />
            </svg>
        </div>
        <div class="stat-body">
            <div class="stat-label">Sugerencias</div>
            <div class="stat-value">{{ number_format($suggestionsCount) }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon si-red">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z" />
                <line x1="4" y1="22" x2="4" y2="15" />
            </svg>
        </div>
        <div class="stat-body">
            <div class="stat-label">Reportes</div>
            <div class="stat-value">{{ number_format($totalReports) }}</div>
        </div>
    </div>
</div>
