@extends('layouts.app')
@section('title', 'Educación')

@section('content')
<x-topbar :profile="$profile" />

{{-- ══════════════════════════════════════════
     EDUCATIONS LAYOUT
══════════════════════════════════════════ --}}
<div class="edit-layout">
    <x-sidebar :profile="$profile" />

    {{-- ──── MAIN CONTENT ──── --}}
    <main class="main-content">

        {{-- ══ CABECERA ══════════════════════════════════════════════ --}}
        <h1 class="page-title">Educación</h1>
        <p class="page-subtitle">Agrega y gestiona tu formación académica</p>

        {{-- ══ BOTÓN AÑADIR ══════════════════════════════════════════ --}}
        <div class="we-add-wrapper">
            <button type="button" class="we-btn-add" id="btnOpenModal"
                {{ $educations->count() >= $limits['max_educations'] ? 'disabled' : '' }}>
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                Añadir educación
            </button>

            {{-- Contador de uso --}}
            <span class="we-limit-badge {{ $educations->count() >= $limits['max_educations'] ? 'we-limit-badge--full' : '' }}">
                {{ $educations->count() }} / {{ $limits['max_educations'] }}
            </span>
        </div>

        {{-- ══ LISTA DE EDUCACIONES ══════════════════════════════════ --}}
        <div class="we-list" id="eduList">
            @forelse($educations as $edu)
            <div class="we-card" data-id="{{ $edu->id }}">
                <div class="we-card__top">
                    <div class="we-card__info">
                        <h3 class="we-card__company">{{ $edu->institution }}</h3>
                        <p class="we-card__position">{{ $edu->degree }}</p>
                        <p class="we-card__meta">
                            @if($edu->field){{ $edu->field }} &bull; @endif
                            @if($edu->current)
                            Actualidad
                            <span class="we-badge-inline">En curso</span>
                            @else
                            {{ $edu->graduated_year }}
                            @endif
                        </p>
                    </div>

                    <div class="we-card__actions">
                        <button type="button" class="we-icon-btn btnEdit"
                            data-id="{{ $edu->id }}"
                            data-institution="{{ $edu->institution }}"
                            data-degree="{{ $edu->degree }}"
                            data-field="{{ $edu->field ?? '' }}"
                            data-graduated-year="{{ $edu->graduated_year ?? '' }}"
                            data-current="{{ $edu->current ? '1' : '0' }}"
                            aria-label="Editar">
                            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </button>

                        <form method="POST"
                            action="{{ route('educations.destroy', $edu->id) }}"
                            class="formDelete"
                            data-institution="{{ $edu->institution }}"
                            data-degree="{{ $edu->degree }}">
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
            </div>
            @empty
            <div class="we-empty">
                <svg width="40" height="40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M12 14l9-5-9-5-9 5 9 5z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                </svg>
                <p class="we-empty__title">Sin formación académica aún</p>
                <p class="we-empty__sub">Usa el botón de arriba para agregar tu primera educación.</p>
            </div>
            @endforelse
            <x-alert />
        </div>

    </main>
</div>

{{-- ══════════════════════════════════════════
     MODAL: Agregar / Editar educación
══════════════════════════════════════════ --}}
<div class="we-backdrop" id="weBackdrop" role="dialog" aria-modal="true" aria-labelledby="weModalTitle">
    <div class="we-modal" id="weModal">

        {{-- Header --}}
        <div class="we-modal__header">
            <div class="we-modal__header-icon">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 14l9-5-9-5-9 5 9 5z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                </svg>
            </div>
            <div>
                <h2 class="we-modal__title" id="weModalTitle">Nueva educación</h2>
                <p class="we-modal__subtitle">Completa los campos para agregar tu formación</p>
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
            <form id="weForm" method="POST" action="{{ route('educations.store') }}">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" name="education_id" id="educationId" value="">

                <div class="we-form-grid">
                    <x-form-group name="institution" label="Institución">
                        <input name="institution" type="text" class="form-input" id="input-institution"
                            value="{{ old('institution') }}"
                            placeholder="Ej. Universidad Nacional, SENA, Platzi"
                            maxlength="150" required />
                    </x-form-group>

                    <x-form-group name="degree" label="Título / Programa">
                        <input name="degree" type="text" class="form-input" id="input-degree"
                            value="{{ old('degree') }}"
                            placeholder="Ej. Ingeniería de Sistemas"
                            maxlength="150" required />
                    </x-form-group>

                    <x-form-group name="field" label="Área de estudio">
                        <input name="field" type="text" class="form-input" id="input-field"
                            value="{{ old('field') }}"
                            placeholder="Ej. Desarrollo de Software"
                            maxlength="150" />
                    </x-form-group>

                    <x-form-group name="graduated_year" label="Año de graduación">
                        <input name="graduated_year" type="number" class="form-input" id="input-graduated_year"
                            value="{{ old('graduated_year') }}"
                            placeholder="Ej. 2023"
                            min="1950" max="{{ date('Y') + 10 }}" />
                    </x-form-group>

                    <x-form-group name="current" label="¿Estudiando actualmente?">
                        <label class="we-toggle">
                            <input type="checkbox" name="current" id="input-current" value="1"
                                {{ old('current') ? 'checked' : '' }}>
                            <span class="we-toggle__track"></span>
                            <span class="we-toggle__label">Actualmente estudio aquí</span>
                        </label>
                    </x-form-group>
                </div>

                {{-- Footer del modal --}}
                <div class="we-modal__footer">
                    <x-btn-cancel id="btnCancelModal" />
                    <x-btn-submit id="btnSubmitForm">Guardar educación</x-btn-submit>
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
<script src="{{ asset('js/profile/education.js') }}"></script>
@endpush