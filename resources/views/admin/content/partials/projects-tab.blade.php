<div class="adm-card">
    @if($projects->isEmpty())
    <div class="adm-empty">
        <div class="adm-empty-state">
            <svg width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p>No hay proyectos registrados.</p>
        </div>
    </div>
    @else
    <div class="ct-table-wrap">
        <table class="ct-table">
            <thead>
                <tr>
                    <th class="ct-th">Proyecto</th>
                    <th class="ct-th">Autor</th>
                    <th class="ct-th ct-th--center">Privacidad</th>
                    <th class="ct-th ct-th--center">Likes</th>
                    <th class="ct-th ct-th--center">Comentarios</th>
                    <th class="ct-th ct-th--center">Estado</th>
                    <th class="ct-th ct-th--center">Fecha</th>
                    <th class="ct-th ct-th--center" data-orderable="false">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($projects as $project)
                @php $isTrashed = $project->trashed(); @endphp
                <tr class="ct-tr {{ $isTrashed ? 'ct-tr--deleted' : '' }}">
                    <td class="ct-td">
                        @if($isTrashed)
                        <span class="ct-project-link ct-project-link--dead">
                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                <line x1="3" y1="9" x2="21" y2="9" />
                            </svg>
                            <span class="ct-project-title">{{ Str::limit($project->title, 50) }}</span>
                        </span>
                        @else
                        <a href="{{ route('projects.show', $project) }}" target="_blank" class="ct-project-link">
                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                <line x1="3" y1="9" x2="21" y2="9" />
                            </svg>
                            <span class="ct-project-title">{{ Str::limit($project->title, 50) }}</span>
                        </a>
                        @endif
                    </td>
                    <td class="ct-td">
                        <div class="ct-user">
                            <img src="{{ $project->user?->avatar_url }}" alt="" class="ct-avatar" loading="lazy">
                            <div class="ct-user-info">
                                <a href="{{ route('profile.show', $project->user?->username) }}" target="_blank" class="ct-user-name">{{ $project->user?->name ?? 'Usuario eliminado' }}</a>
                                <span class="ct-user-handle">{{ '@' . ($project->user?->username ?? 'desconocido') }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="ct-td ct-td--center">
                        @switch($project->privacy)
                        @case('public')
                        <span class="ct-vis ct-vis--public">Público</span>
                        @break
                        @case('followers')
                        <span class="ct-vis ct-vis--followers">Seguidores</span>
                        @break
                        @case('private')
                        <span class="ct-vis ct-vis--private">Privado</span>
                        @break
                        @endswitch
                    </td>
                    <td class="ct-td ct-td--center">
                        <span class="ct-count">{{ $project->likes_count }}</span>
                    </td>
                    <td class="ct-td ct-td--center">
                        <span class="ct-count">{{ $project->comments_count }}</span>
                    </td>
                    <td class="ct-td ct-td--center">
                        @if($isTrashed)
                        <span class="ct-status ct-status--deleted">Eliminado</span>
                        @else
                        <span class="ct-status ct-status--active">Activo</span>
                        @endif
                    </td>
                    <td class="ct-td ct-td--center">
                        <time class="ct-date" datetime="{{ $project->created_at->toDateString() }}">
                            {{ $project->created_at->diffForHumans() }}
                        </time>
                    </td>
                    <td class="ct-td ct-td--center">
                        <div class="ct-actions">
                            @if($isTrashed)
                            <form method="POST" action="{{ route('admin.content.project.restore', $project) }}" class="ct-restore-form">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="ct-btn ct-btn--ghost" title="Restaurar proyecto">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <polyline points="1 4 1 10 7 10" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                        <path d="M3.51 15a9 9 0 102.13-9.36L1 10" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                    </svg>
                                    Restaurar
                                </button>
                            </form>
                            @else
                            <form method="POST" action="{{ route('admin.content.project.delete', $project) }}" class="ct-delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="ct-btn ct-btn--ghost ct-btn--danger" title="Eliminar proyecto">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <polyline points="3 6 5 6 21 6" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                        <path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                    </svg>
                                    Eliminar
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="adm-dt-info">
        <span>{{ $projects->total() }} proyectos en total</span>
        @if($projects->hasPages())
        <div class="ct-pagination">
            {{ $projects->onEachSide(1)->links() }}
        </div>
        @endif
    </div>
    @endif
</div>
