@extends('layouts.app')
@section('title', 'Certificaciones')

@section('content')
<x-topbar :profile="$profile" />

<div class="edit-layout">
    <x-sidebar :profile="$profile" />

    <main class="main-content">
        <h1 class="page-title">Certificaciones</h1>
        <p class="page-subtitle">Agrega certificaciones, cursos y credenciales que respalden tu perfil profesional.</p>
        <p class="form-hint career-privacy-note">
            Publica solo datos y enlaces de verificación. No subas documentos con firmas ni información confidencial.
        </p>

        <div class="we-add-wrapper">
            <button type="button" class="we-btn-add" id="btnOpenModal"
                {{ $credentials->count() >= ($limits['max_credentials'] ?? 0) ? 'disabled' : '' }}>
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                Añadir certificación
            </button>

            <span class="we-limit-badge {{ $credentials->count() >= ($limits['max_credentials'] ?? 0) ? 'we-limit-badge--full' : '' }}">
                {{ $credentials->count() }} / {{ $limits['max_credentials'] ?? 0 }}
            </span>
        </div>

        <div class="career-list" id="credentialsList">
            @forelse($credentials as $credential)
            <div class="career-card" data-id="{{ $credential->id }}">
                <div class="career-card__top">
                    <div class="career-card__info">
                        <h3 class="career-card__title">{{ $credential->title }}</h3>
                        <p class="career-card__subtitle">{{ $credential->issuer }}</p>
                        <p class="career-card__meta">
                            <span class="career-badge career-badge--{{ $credential->type }}">
                                {{ \App\Models\Credential::TYPES[$credential->type] ?? $credential->type }}
                            </span>
                            <span>Expedida {{ $credential->issued_at?->translatedFormat('M Y') ?? '' }}</span>
                            @if($credential->expires_at)
                            <span>Válida hasta {{ $credential->expires_at->translatedFormat('M Y') }}</span>
                            @endif
                            <span class="career-badge career-badge--{{ $credential->privacy }}">
                                {{ \App\Models\Credential::PRIVACY[$credential->privacy] ?? $credential->privacy }}
                            </span>
                        </p>
                    </div>

                    <div class="career-card__actions">
                        <button type="button" class="we-icon-btn btnEdit"
                            data-id="{{ $credential->id }}"
                            data-title="{{ $credential->title }}"
                            data-issuer="{{ $credential->issuer }}"
                            data-type="{{ $credential->type }}"
                            data-issued="{{ $credential->issued_at?->format('Y-m-d') ?? '' }}"
                            data-expires="{{ $credential->expires_at?->format('Y-m-d') ?? '' }}"
                            data-credential-id="{{ $credential->credential_id ?? '' }}"
                            data-credential-url="{{ $credential->credential_url ?? '' }}"
                            data-description="{{ $credential->description ?? '' }}"
                            data-privacy="{{ $credential->privacy }}"
                            aria-label="Editar certificación">
                            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </button>

                        <form method="POST"
                            action="{{ route('credentials.destroy', $credential) }}"
                            class="formDelete"
                            data-title="{{ $credential->title }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="we-icon-btn we-icon-btn--danger" aria-label="Eliminar certificación">
                                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <polyline stroke-width="2" points="3 6 5 6 21 6" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6m5 0V4a1 1 0 011-1h2a1 1 0 011 1v2" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>

                @if($credential->description)
                <div class="career-card__divider"></div>
                <p class="career-card__desc">{{ $credential->description }}</p>
                @endif

                @if($credential->credential_id || $credential->credential_url)
                <div class="career-card__footer">
                    @if($credential->credential_id)
                    <span class="career-card__detail"><strong>ID:</strong> {{ $credential->credential_id }}</span>
                    @endif
                    @if($credential->credential_url)
                    <a href="{{ $credential->credential_url }}" class="career-card__link" target="_blank" rel="noopener noreferrer">
                        Ver credencial
                    </a>
                    @endif
                </div>
                @endif
            </div>
            @empty
            <div class="we-empty">
                <svg width="40" height="40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
                <p class="we-empty__title">Sin certificaciones aún</p>
                <p class="we-empty__sub">Agrega cursos, certificaciones o reconocimientos que ya hayas obtenido.</p>
            </div>
            @endforelse
        </div>
    </main>
</div>

<div class="we-backdrop" id="credBackdrop" role="dialog" aria-modal="true" aria-labelledby="credModalTitle">
    <div class="we-modal" id="credModal">
        <div class="we-modal__header">
            <div class="we-modal__header-icon">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </div>
            <div>
                <h2 class="we-modal__title" id="credModalTitle">Nueva certificación</h2>
                <p class="we-modal__subtitle">Completa los datos de tu certificación</p>
            </div>
            <button type="button" class="we-modal__close" id="btnCloseModal" aria-label="Cerrar">
                <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <line x1="18" y1="6" x2="6" y2="18" stroke-width="2.5" stroke-linecap="round" />
                    <line x1="6" y1="6" x2="18" y2="18" stroke-width="2.5" stroke-linecap="round" />
                </svg>
            </button>
        </div>

        <div class="we-modal__body">
            <form id="credForm" method="POST" action="{{ route('credentials.store') }}">
                @csrf
                <input type="hidden" name="_method" id="credFormMethod" value="POST">

                <div class="we-form-grid">
                    <x-form-group name="title" label="Título">
                        <input name="title" type="text" class="form-input" id="input-title"
                            value="{{ old('title') }}" placeholder="Ej. AWS Certified Cloud Practitioner" maxlength="150" required />
                    </x-form-group>

                    <x-form-group name="issuer" label="Entidad emisora">
                        <input name="issuer" type="text" class="form-input" id="input-issuer"
                            value="{{ old('issuer') }}" placeholder="Ej. Amazon Web Services" maxlength="150" required />
                    </x-form-group>

                    <x-form-group name="type" label="Tipo">
                        <select name="type" class="form-input" id="input-type" required>
                            @foreach(\App\Models\Credential::TYPES as $value => $label)
                            <option value="{{ $value }}" {{ old('type', 'certification') === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </x-form-group>

                    <x-form-group name="privacy" label="Visibilidad" hint="Público: aparece en tu perfil. Privado: solo tú lo ves.">
                        <select name="privacy" class="form-input" id="input-privacy" required>
                            @foreach(\App\Models\Credential::PRIVACY as $value => $label)
                            <option value="{{ $value }}" {{ old('privacy', 'public') === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </x-form-group>

                    <x-form-group name="issued_at" label="Fecha de expedición">
                        <input name="issued_at" type="date" class="form-input" id="input-issued_at"
                            value="{{ old('issued_at') }}" required />
                    </x-form-group>

                    <x-form-group name="expires_at" label="Válida hasta" hint="Opcional.">
                        <input name="expires_at" type="date" class="form-input" id="input-expires_at"
                            value="{{ old('expires_at') }}" />
                    </x-form-group>

                    <x-form-group name="credential_id" label="ID de la credencial" hint="Opcional.">
                        <input name="credential_id" type="text" class="form-input" id="input-credential_id"
                            value="{{ old('credential_id') }}" placeholder="Ej. ABC-123-XYZ" maxlength="100" />
                    </x-form-group>

                    <x-form-group name="credential_url" label="Enlace de verificación" hint="Opcional. Solo enlaces http/https.">
                        <input name="credential_url" type="url" class="form-input" id="input-credential_url"
                            value="{{ old('credential_url') }}" placeholder="https://..." maxlength="255" />
                    </x-form-group>
                </div>

                <x-form-group name="description" label="Descripción" hint="Opcional. Describe brevemente qué acredita.">
                    <textarea name="description" class="form-input we-textarea" id="input-description"
                        maxlength="500" placeholder="Ej. Certificación en fundamentos de cloud, seguridad y facturación...">{{ old('description') }}</textarea>
                    <div class="char-count" id="charCountDesc">0/500</div>
                </x-form-group>

                <div class="we-modal__footer">
                    <x-btn-cancel id="btnCancelModal" />
                    <x-btn-submit id="btnSubmitForm">Guardar certificación</x-btn-submit>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
@vite('resources/css/shared/modal.css')
@vite('resources/css/profile/shared.css')
@vite('resources/css/profile/sidebar.css')
@vite('resources/css/profile/workExperience.css')
@vite('resources/css/profile/career.css')
@endpush

@push('scripts')
@vite('resources/js/profile/credentials.js')
@vite('resources/js/profile/avatar.js')
@endpush
