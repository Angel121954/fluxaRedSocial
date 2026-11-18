@extends('admin.layouts.admin')

@section('title', 'Analítica · Fluxa')

@section('admin-content')
<div class="page-header">
    <div>
        <h1 class="page-title">Analítica del embudo</h1>
        <p class="page-sub">Del primer clic a la primera acción. Aquí ves dónde se cae la gente y qué corregir.</p>
    </div>

    <details class="date-range">
        <summary class="date-btn" aria-label="Filtrar por rango de fechas">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <rect x="3" y="4" width="18" height="18" rx="2" />
                <line x1="16" y1="2" x2="16" y2="6" />
                <line x1="8" y1="2" x2="8" y2="6" />
                <line x1="3" y1="10" x2="21" y2="10" />
            </svg>
            <span>{{ $etiquetaRango }}</span>
        </summary>

        <form class="date-popover" method="GET" action="{{ route('admin.analytics.index') }}">
            <div class="date-field">
                <label for="dateFrom">Desde</label>
                <input type="date" id="dateFrom" name="desde" value="{{ $desde?->format('Y-m-d') }}" max="{{ now()->format('Y-m-d') }}">
            </div>
            <div class="date-field">
                <label for="dateTo">Hasta</label>
                <input type="date" id="dateTo" name="hasta" value="{{ $hasta?->format('Y-m-d') }}" max="{{ now()->format('Y-m-d') }}">
            </div>
            <div class="date-actions">
                <a class="date-clear" href="{{ route('admin.analytics.index') }}">Limpiar</a>
                <button type="submit" class="date-apply">Aplicar</button>
            </div>
        </form>
    </details>
</div>

<div class="stats-row">
    <div class="stat-card">
        <div class="stat-icon si-teal">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M3 12h4l3 8 4-16 3 8h4" />
            </svg>
        </div>
        <div class="stat-body">
            <div class="stat-label">Visitas públicas</div>
            <div class="stat-value">{{ number_format($summary['page_views']) }}</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon si-blue">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" />
                <circle cx="9" cy="7" r="4" />
                <path d="M23 21v-2a4 4 0 00-3-3.87" />
                <path d="M16 3.13a4 4 0 010 7.75" />
            </svg>
        </div>
        <div class="stat-body">
            <div class="stat-label">Registros</div>
            <div class="stat-value">{{ number_format($summary['registrations']) }}</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon si-amber">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <polyline points="9 11 12 14 22 4" />
                <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11" />
            </svg>
        </div>
        <div class="stat-body">
            <div class="stat-label">Verificaron email</div>
            <div class="stat-value">{{ number_format($summary['verified']) }}</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon si-green">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M22 11.08V12a10 10 0 11-5.93-9.14" />
                <polyline points="22 4 12 14.01 9 11.01" />
            </svg>
        </div>
        <div class="stat-body">
            <div class="stat-label">Onboarding completo</div>
            <div class="stat-value">{{ number_format($summary['onboarding']) }}</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon si-teal">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <polyline points="22 7 13.5 15.5 8.5 10.5 2 17" />
                <polyline points="16 7 22 7 22 13" />
            </svg>
        </div>
        <div class="stat-body">
            <div class="stat-label">Activados (7 días)</div>
            <div class="stat-value">{{ number_format($summary['activated']) }}</div>
            <div class="an-stat-hint">{{ $summary['activation_rate'] }}% de los registros</div>
        </div>
    </div>
</div>

<div class="content-grid">
    <div class="col-left">
        <div class="card">
            <div class="card-header">
                <span class="card-title">Embudo de activación</span>
                <span class="an-card-note">Conversión paso a paso</span>
            </div>
            <div class="card-body">
                @if ($funnel[0]['count'] === 0 && $summary['registrations'] === 0)
                <div class="an-empty">
                    <strong>Aún no hay eventos en este rango.</strong>
                    <span>Cuando alguien visite la landing, se registre o publique, aparecerá aquí.</span>
                </div>
                @else
                <div class="an-funnel">
                    @foreach ($funnel as $step)
                    <div class="an-funnel-step">
                        <div class="an-funnel-top">
                            <span class="an-funnel-label">{{ $step['label'] }}</span>
                            <span class="an-funnel-value">{{ number_format($step['count']) }}</span>
                        </div>
                        <div class="an-funnel-track">
                            <span class="an-funnel-fill" style="width: {{ $step['overall'] }}%"></span>
                        </div>
                        <div class="an-funnel-meta">
                            <span>{{ $step['rate'] }}% del paso anterior</span>
                            <span>{{ $step['overall'] }}% del total</span>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        <div class="card" id="actividad">
            <div class="card-header">
                <span class="card-title">Actividad en el tiempo</span>
                <span class="an-card-note">Registros vs. publicaciones y comentarios</span>
            </div>
            <div class="card-body">
                <div class="an-chart-wrap">
                    <canvas id="activityChart"
                        data-labels='{{ json_encode($activity['labels']) }}'
                        data-registrations='{{ json_encode($activity['registrations']) }}'
                        data-actions='{{ json_encode($activity['actions']) }}'>
                    </canvas>
                </div>
            </div>
        </div>

        <div class="card" id="trafico">
            <div class="card-header">
                <span class="card-title">Fuentes de adquisición</span>
                <span class="an-card-note">Primer toque: UTM o referrer</span>
            </div>
            <div class="card-body">
                @if ($sources->isEmpty())
                <div class="an-empty">
                    <strong>Todavía no hay registros con fuente.</strong>
                    <span>Comparte enlaces con <code>?utm_source=linkedin</code> o <code>?utm_source=sena</code> para medir qué canal activa usuarios.</span>
                </div>
                @else
                <div class="an-table-wrap">
                    <table class="an-table">
                        <thead>
                            <tr>
                                <th>Fuente</th>
                                <th>Visitas</th>
                                <th>Registros</th>
                                <th>Activados</th>
                                <th>Tasa</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sources as $source)
                            <tr>
                                <td><strong>{{ $source['source'] }}</strong></td>
                                <td>{{ number_format($source['visits']) }}</td>
                                <td>{{ number_format($source['registrations']) }}</td>
                                <td>{{ number_format($source['activated']) }}</td>
                                <td>{{ $source['activation_rate'] }}%</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-right">
        <div class="card" id="retencion">
            <div class="card-header">
                <span class="card-title">Retención</span>
            </div>
            <div class="card-body">
                <div class="an-retention">
                    <div class="an-retention-item">
                        <span class="an-retention-label">Día 1</span>
                        <strong>{{ $retention['d1_rate'] }}%</strong>
                        <span>{{ $retention['d1'] }} de {{ $retention['total'] }}</span>
                    </div>
                    <div class="an-retention-item">
                        <span class="an-retention-label">Día 7</span>
                        <strong>{{ $retention['d7_rate'] }}%</strong>
                        <span>{{ $retention['d7'] }} de {{ $retention['total'] }}</span>
                    </div>
                    <div class="an-retention-item">
                        <span class="an-retention-label">Día 30</span>
                        <strong>{{ $retention['d30_rate'] }}%</strong>
                        <span>{{ $retention['d30'] }} de {{ $retention['total'] }}</span>
                    </div>
                </div>
                <p class="an-note">D30 solo es confiable para cohortes con al menos 30 días de antigüedad.</p>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <span class="card-title">North Star</span>
            </div>
            <div class="card-body">
                <div class="an-north-star">
                    <span class="an-north-star-value">{{ number_format($summary['weekly_active_builders']) }}</span>
                    <span class="an-north-star-label">builders activos en los últimos 7 días</span>
                </div>
                <p class="an-note">Publicaron, comentaron, siguieron o compartieron un proyecto.</p>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <span class="card-title">Cómo leer esto</span>
            </div>
            <div class="card-body">
                <p class="an-note an-note--block">
                    Si cae la verificación, revisa el correo y el muro de verificación.
                    Si cae el onboarding, sobran pasos. Si caen los activados, el primer proyecto no está recibiendo feedback.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
@vite('resources/css/admin/analytics.css')
@endpush

@push('scripts')
@vite('resources/js/admin/analytics.js')
@endpush
