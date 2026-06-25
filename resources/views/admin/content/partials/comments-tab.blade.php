<div class="adm-card">
    @if($comments->isEmpty())
    <div class="adm-empty">
        <div class="adm-empty-state">
            <svg width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p>No hay comentarios registrados.</p>
        </div>
    </div>
    @else
    <div class="ct-table-wrap">
        <table class="ct-table">
            <thead>
                <tr>
                    <th class="ct-th">Comentario</th>
                    <th class="ct-th">Autor</th>
                    <th class="ct-th">Proyecto</th>
                    <th class="ct-th ct-th--center">Fecha</th>
                    <th class="ct-th ct-th--center" data-orderable="false">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($comments as $comment)
                <tr class="ct-tr">
                    <td class="ct-td">
                        <div class="ct-comment-content">{{ Str::limit($comment->content, 80) }}</div>
                    </td>
                    <td class="ct-td">
                        <div class="ct-user">
                            <img src="{{ $comment->user?->avatar_url }}" alt="" class="ct-avatar" loading="lazy">
                            <div class="ct-user-info">
                                <a href="{{ route('profile.show', $comment->user?->username) }}" target="_blank" class="ct-user-name">{{ $comment->user?->name ?? 'Usuario eliminado' }}</a>
                                <span class="ct-user-handle">{{ '@' . ($comment->user?->username ?? 'desconocido') }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="ct-td">
                        @if($comment->project)
                        <a href="{{ route('projects.show', $comment->project) }}" target="_blank" class="ct-project-link">
                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                <line x1="3" y1="9" x2="21" y2="9" />
                            </svg>
                            {{ Str::limit($comment->project->title, 40) }}
                        </a>
                        @else
                        <span class="ct-deleted">Proyecto eliminado</span>
                        @endif
                    </td>
                    <td class="ct-td ct-td--center">
                        <time class="ct-date" datetime="{{ $comment->created_at->toDateString() }}">
                            {{ $comment->created_at->diffForHumans() }}
                        </time>
                    </td>
                    <td class="ct-td ct-td--center">
                        <div class="ct-actions">
                            <form method="POST" action="{{ route('admin.content.comment.delete', $comment) }}" class="ct-delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="ct-btn ct-btn--ghost ct-btn--danger" title="Eliminar comentario">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <polyline points="3 6 5 6 21 6" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                        <path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                    </svg>
                                    Eliminar
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="adm-dt-info">
        <span>{{ $comments->total() }} comentarios en total</span>
        @if($comments->hasPages())
        <div class="ct-pagination">
            {{ $comments->onEachSide(1)->links() }}
        </div>
        @endif
    </div>
    @endif
</div>
