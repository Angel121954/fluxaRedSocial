<div class="adm-card">
    @if($diaryReports->isEmpty())
    <div class="adm-empty">
        <div class="adm-empty-state">
            <svg width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p>No hay reportes del diario pendientes.</p>
        </div>
    </div>
    @else
    <div class="rp-table-wrap">
        <table class="rp-table">
            <thead>
                <tr>
                    <th class="rp-th">Denunciante</th>
                    <th class="rp-th rp-th--left">Respuesta reportada</th>
                    <th class="rp-th rp-th--left">Motivo</th>
                    <th class="rp-th rp-th--center">Fecha</th>
                    <th class="rp-th rp-th--center" data-orderable="false">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($diaryReports as $report)
                <tr class="rp-tr">
                    <td class="rp-td">
                        <div class="rp-user">
                            <img src="{{ $report->user?->avatar_url }}" alt="" class="rp-avatar" loading="lazy">
                            <div class="rp-user-info">
                                <a href="{{ route('profile.show', $report->user?->username) }}" target="_blank" class="rp-user-name">{{ $report->user?->name ?? 'Usuario eliminado' }}</a>
                                <span class="rp-user-handle">{{ '@' . ($report->user?->username ?? 'desconocido') }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="rp-td rp-td--left">
                        @if($report->diaryResponse)
                        <div class="rp-answer-preview">{{ Str::limit($report->diaryResponse->content, 120) }}</div>
                        @else
                        <span class="rp-deleted">Respuesta eliminada</span>
                        @endif
                    </td>
                    <td class="rp-td rp-td--left">
                        <div class="rp-reason">{{ $report->reason }}</div>
                    </td>
                    <td class="rp-td rp-td--center">
                        <time class="rp-date" datetime="{{ $report->created_at->toDateString() }}">
                            {{ $report->created_at->diffForHumans() }}
                        </time>
                    </td>
                    <td class="rp-td rp-td--center">
                        <div class="rp-actions">
                            <form method="POST" action="{{ route('admin.reports.diary.dismiss', $report) }}" class="rp-dismiss-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rp-btn rp-btn--ghost" title="Descartar reporte">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Descartar
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
