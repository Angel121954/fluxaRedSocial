@extends('admin.layouts.admin')

@section('title', 'Diario — Admin Fluxa')

@push('styles')
@vite('resources/css/admin/users.css')
@vite('resources/css/admin/suggestions.css')
@vite('resources/css/admin/diary.css')
@vite('resources/css/shared/modal.css')
@endpush

@section('admin-content')

<div class="page-header">
    <div>
        <h1 class="page-title">Diario</h1>
        <p class="page-sub">Gestiona las preguntas diarias para la comunidad.</p>
    </div>
    <div class="adm-header-actions">
        <button type="button" class="adm-btn adm-btn--accent" id="openDiaryModal" data-url="{{ route('admin.diary.store') }}">
            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Nueva pregunta
        </button>
    </div>
</div>

<div class="diary-admin-grid">
    @forelse($diaries as $diary)
    @php $isClosed = ($diary->status ?? 'active') === 'closed'; @endphp
    <div class="diary-admin-card {{ $isClosed ? 'diary-admin-card--closed' : '' }}">
        <div class="diary-admin-card__top">
            <span class="diary-admin-card__emoji">{{ $diary->emoji ?? '📝' }}</span>
            <div class="diary-admin-card__question">{{ $diary->question }}</div>
        </div>
        <div class="diary-admin-card__footer">
            <div class="diary-admin-card__left">
                <span class="diary-admin-card__stat">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                    </svg>
                    {{ $diary->responses_count }} respuesta{{ $diary->responses_count !== 1 ? 's' : '' }}
                </span>
                @if(!$isClosed)
                <button type="button" class="diary-admin-card__close-btn" data-diary-id="{{ $diary->id }}" data-url="{{ route('admin.diary.close', $diary) }}" title="Cerrar diario">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18" /><line x1="6" y1="6" x2="18" y2="18" />
                    </svg>
                </button>
                @endif
                <span class="diary-admin-card__status">{{ $isClosed ? 'Cerrado' : 'Activo' }}</span>
            </div>
            <div class="diary-admin-card__actions">
                @if(!$isClosed)
                <button type="button" class="diary-admin-card__edit-btn" data-id="{{ $diary->id }}" data-question="{{ $diary->question }}" data-emoji="{{ $diary->emoji ?? '' }}" title="Editar">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                        <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                </button>
                @endif
                <time class="diary-admin-card__date" datetime="{{ $diary->created_at->toDateString() }}">
                    {{ $diary->created_at->translatedFormat('d M Y') }}
                </time>
            </div>
        </div>
    </div>
    @empty
    <div class="diary-admin-empty">
        <p>No hay preguntas aún. Crea la primera.</p>
    </div>
    @endforelse
</div>

{{-- Modal: crear/editar pregunta del diario --}}
<div class="modal-backdrop" id="diaryModalBackdrop" role="dialog" aria-modal="true" aria-labelledby="diaryModalTitle">
    <div class="modal-card" id="diaryModal">
        <div class="modal-header">
            <div class="modal-header-text">
                <div class="modal-title" id="diaryModalTitle">Nueva pregunta del diario</div>
                <div class="modal-subtitle" id="diaryModalSubtitle">Crea una pregunta para que la comunidad responda.</div>
            </div>
            <button type="button" class="modal-close" id="closeDiaryModal" aria-label="Cerrar modal">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18" />
                    <line x1="6" y1="6" x2="18" y2="18" />
                </svg>
            </button>
        </div>

        <form method="POST" action="{{ route('admin.diary.store') }}" class="adm-form" id="diaryForm">
            @csrf
            <input type="hidden" name="_method" id="diaryFormMethod" value="POST">
            <div class="modal-body">
                <div class="adm-form-group">
                    <label for="diary_question" class="adm-label">Pregunta</label>
                    <textarea
                        id="diary_question"
                        name="question"
                        class="adm-textarea"
                        rows="3"
                        placeholder="Escribe la pregunta del diario..."
                        required
                        maxlength="500">{{ old('question') }}</textarea>
                    @error('question')
                    <span class="adm-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="adm-form-group">
                    <label for="diary_emoji" class="adm-label">Emoji (opcional)</label>
                    <input
                        type="text"
                        id="diary_emoji"
                        name="emoji"
                        class="adm-input"
                        placeholder="🔥"
                        maxlength="10"
                        value="{{ old('emoji') }}">
                    @error('emoji')
                    <span class="adm-error">{{ $message }}</span>
                    @enderror
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="cancelDiaryModal">Cancelar</button>
                <button type="submit" class="btn btn-primary" id="diaryModalSubmit">Crear pregunta</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
@vite('resources/js/admin/diary/modal.js')
@vite('resources/js/admin/diary/close.js')
@endpush
