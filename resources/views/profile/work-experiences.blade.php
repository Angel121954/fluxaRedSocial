@extends('layouts.app')
@section('title', 'Experiencia Laboral')

@section('content')
<x-topbar :profile="$profile" />

{{-- ══════════════════════════════════════════
     WORK EXPERIENCES LAYOUT
══════════════════════════════════════════ --}}
<div class="edit-layout">
    <x-sidebar :profile="$profile" />

    {{-- ──── MAIN CONTENT ──── --}}
    <main class="main-content">

        {{-- ══ CABECERA ══════════════════════════════════════════════ --}}
        <h1 class="page-title">Experiencia Laboral</h1>
        <p class="page-subtitle">Agrega y gestiona tu experiencia laboral</p>

        {{-- ══ BOTÓN AÑADIR ══════════════════════════════════════════ --}}
        <div class="we-add-wrapper">
            <button type="button" class="we-btn-add" id="btnOpenModal"
                {{ $experiences->count() >= $limits['max_work_experiences'] ? 'disabled' : '' }}>
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                Añadir experiencia laboral
            </button>

            {{-- Contador de uso --}}
            <span class="we-limit-badge {{ $experiences->count() >= $limits['max_work_experiences'] ? 'we-limit-badge--full' : '' }}">
                {{ $experiences->count() }} / {{ $limits['max_work_experiences'] }}
            </span>
        </div>

        {{-- ══ LISTA DE EXPERIENCIAS ══════════════════════════════════ --}}
        <div class="we-list" id="weList">
            @forelse($experiences as $exp)
            <div class="we-card" data-id="{{ $exp->id }}">
                <div class="we-card__top">
                    <div class="we-card__info">
                        <h3 class="we-card__company">{{ $exp->company }}</h3>
                        <p class="we-card__position">{{ $exp->position }}</p>
                        <p class="we-card__meta">
                            @if($exp->location){{ $exp->location }} &bull; @endif
                            {{ $exp->started_at->translatedFormat('M Y') }}
                            &bull;
                            {{ $exp->current ? 'Actualidad' : $exp->ended_at->translatedFormat('M Y') }}
                            @if($exp->current)
                            <span class="we-badge-inline">Actual</span>
                            @endif
                        </p>
                    </div>

                    <div class="we-card__actions">
                        <button type="button" class="we-icon-btn btnEdit"
                            data-id="{{ $exp->id }}"
                            data-company="{{ $exp->company }}"
                            data-position="{{ $exp->position }}"
                            data-location="{{ $exp->location ?? '' }}"
                            data-started="{{ $exp->started_at->format('Y-m-d') }}"
                            data-ended="{{ $exp->ended_at ? $exp->ended_at->format('Y-m-d') : '' }}"
                            data-current="{{ $exp->current ? '1' : '0' }}"
                            data-description="{{ $exp->description ?? '' }}"
                            aria-label="Editar">
                            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </button>

                        <form method="POST"
                            action="{{ route('work-experiences.destroy', $exp->id) }}"
                            class="formDelete"
                            data-company="{{ $exp->company }}"
                            data-position="{{ $exp->position }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="we-icon-btn we-icon-btn--danger" aria-label="Eliminar">
                                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <polyline stroke-width="2" points="3 6 5 6 21 6" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6m5 0V4a1 1 0 011-1h2a1 1 0 011 1v2" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>

                @if($exp->description)
                <div class="we-card__divider"></div>
                <p class="we-card__desc">{{ $exp->description }}</p>
                @endif
            </div>
            @empty
            <div class="we-empty">
                <svg width="40" height="40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <rect x="2" y="7" width="20" height="14" rx="2" stroke-width="1.5" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2" />
                    <line x1="12" y1="12" x2="12" y2="16" stroke-width="1.5" />
                    <line x1="10" y1="14" x2="14" y2="14" stroke-width="1.5" />
                </svg>
                <p class="we-empty__title">Sin experiencia laboral aún</p>
                <p class="we-empty__sub">Usa el botón de arriba para agregar tu primera experiencia.</p>
            </div>
            @endforelse
            <x-alert />
        </div>

    </main>
</div>

{{-- ══════════════════════════════════════════
     MODAL: Agregar / Editar experiencia
══════════════════════════════════════════ --}}
<div class="we-backdrop" id="weBackdrop" role="dialog" aria-modal="true" aria-labelledby="weModalTitle">
    <div class="we-modal" id="weModal">

        {{-- Header --}}
        <div class="we-modal__header">
            <div class="we-modal__header-icon">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <rect x="2" y="7" width="20" height="14" rx="2" stroke-width="2" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2" />
                </svg>
            </div>
            <div>
                <h2 class="we-modal__title" id="weModalTitle">Nueva experiencia</h2>
                <p class="we-modal__subtitle">Completa los campos para agregar tu experiencia</p>
            </div>
            <button type="button" class="we-modal__close" id="btnCloseModal" aria-label="Cerrar">
                <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <line x1="18" y1="6" x2="6" y2="18" stroke-width="2.5" stroke-linecap="round" />
                    <line x1="6" y1="6" x2="18" y2="18" stroke-width="2.5" stroke-linecap="round" />
                </svg>
            </button>
        </div>

        {{-- Body --}}
        <div class="we-modal__body">
            <form id="weForm" method="POST" action="{{ route('work-experiences.store') }}">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" name="experience_id" id="experienceId" value="">

                <div class="we-form-grid">
                    <x-form-group name="company" label="Empresa">
                        <input name="company" type="text" class="form-input" id="input-company"
                            value="{{ old('company') }}"
                            placeholder="Ej. Google, Startup XYZ" maxlength="100" required />
                    </x-form-group>

                    <x-form-group name="position" label="Cargo">
                        <input name="position" type="text" class="form-input" id="input-position"
                            value="{{ old('position') }}"
                            placeholder="Ej. Frontend Developer" maxlength="100" required />
                    </x-form-group>

                    <x-form-group name="location" label="Ubicación">
                        <input name="location" type="text" class="form-input" id="input-location"
                            value="{{ old('location') }}"
                            placeholder="Ej. Bogotá, Colombia (Remoto)" maxlength="100" />
                    </x-form-group>

                    <x-form-group name="started_at" label="Fecha de inicio">
                        <input name="started_at" type="date" class="form-input" id="input-started_at"
                            value="{{ old('started_at') }}" required />
                    </x-form-group>

                    <x-form-group name="ended_at" label="Fecha de fin">
                        <input name="ended_at" type="date" class="form-input" id="input-ended_at"
                            value="{{ old('ended_at') }}" />
                    </x-form-group>

                    <x-form-group name="current" label="¿Trabajo actual?">
                        <label class="we-toggle">
                            <input type="checkbox" name="current" id="input-current" value="1"
                                {{ old('current') ? 'checked' : '' }}>
                            <span class="we-toggle__track"></span>
                            <span class="we-toggle__label">Actualmente trabajo aquí</span>
                        </label>
                    </x-form-group>
                </div>

                <x-form-group name="description" label="Descripción" hint="Describe brevemente tus responsabilidades y logros.">
                    <textarea name="description" class="form-input we-textarea" id="input-description"
                        maxlength="1000"
                        placeholder="Desarrollé features en React, lideré migraciones de base de datos...">{{ old('description') }}</textarea>
                    <div class="char-count" id="charCountDesc">0/1000</div>
                </x-form-group>

                {{-- Footer del modal --}}
                <div class="we-modal__footer">
                    <x-btn-cancel id="btnCancelModal" />
                    <x-btn-submit id="btnSubmitForm">Guardar experiencia</x-btn-submit>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/profile/workExperience.css') }}" />
@endpush

@push('scripts')
<script src="{{ asset('js/profile/workExperience.js') }}"></script>
@endpush