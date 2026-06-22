@extends('layouts.app')
@section('title', 'CV / Perfil profesional')
@section('content')
<x-topbar :profile="$profile" />

{{-- ══════════════════════════════════════════
     CV / PERFIL PROFESIONAL LAYOUT
══════════════════════════════════════════ --}}
<div class="edit-layout">
    <x-sidebar :profile="$profile" />

    {{-- ──── MAIN CONTENT ──── --}}
    <main class="main-content">
        <h1 class="page-title">CV / Perfil profesional</h1>
        <p class="page-subtitle">Personaliza la configuración de la generación de tu CV y cómo aparecerá tu perfil profesional.</p>

        <form id="cvForm" method="POST" action="{{ route('cv.update') }}">
            @csrf
            @method('PUT')

            <div class="cv-layout">

                {{-- ──── PANEL IZQUIERDO: Configuración ──── --}}
                <div class="cv-config">

                    {{-- ── SECCIÓN: Formato de descarga ──────────────── --}}
                    <section class="cv-section">
                        <h2 class="cv-section__title">Formato de descarga</h2>
                        <div class="cv-format-grid">

                            <label class="cv-format-card {{ old('format', $cvSettings['format'] ?? 'pdf') === 'pdf' ? 'cv-format-card--selected' : '' }}" for="fmt-pdf">
                                <input type="radio" id="fmt-pdf" name="format" value="pdf"
                                    {{ old('format', $cvSettings['format'] ?? 'pdf') === 'pdf' ? 'checked' : '' }}
                                    class="cv-format-radio">
                                <div class="cv-format-card__check">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                        <polyline points="20 6 9 17 4 12" />
                                    </svg>
                                </div>
                                <div class="cv-format-card__icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                        <polyline points="14 2 14 8 20 8"/>
                                        <line x1="16" y1="13" x2="8" y2="13"/>
                                        <line x1="16" y1="17" x2="8" y2="17"/>
                                        <polyline points="10 9 9 9 8 9"/>
                                    </svg>
                                </div>
                                <span class="cv-format-card__name">PDF Visual</span>
                                <span class="cv-format-card__sub">Diseño clásico con foto y colores</span>
                            </label>

                            <label class="cv-format-card {{ old('format', $cvSettings['format'] ?? 'pdf') === 'ats' ? 'cv-format-card--selected' : '' }}" for="fmt-ats">
                                <input type="radio" id="fmt-ats" name="format" value="ats"
                                    {{ old('format', $cvSettings['format'] ?? 'pdf') === 'ats' ? 'checked' : '' }}
                                    class="cv-format-radio">
                                <div class="cv-format-card__check">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                        <polyline points="20 6 9 17 4 12" />
                                    </svg>
                                </div>
                                <div class="cv-format-card__icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                        <polyline points="14 2 14 8 20 8"/>
                                        <line x1="16" y1="13" x2="8" y2="13"/>
                                        <line x1="16" y1="17" x2="8" y2="17"/>
                                        <polyline points="10 9 9 9 8 9"/>
                                    </svg>
                                </div>
                                <span class="cv-format-card__name">PDF ATS</span>
                                <span class="cv-format-card__sub">Optimizado para sistemas ATS</span>
                            </label>

                            <label class="cv-format-card {{ old('format', $cvSettings['format'] ?? 'pdf') === 'json' ? 'cv-format-card--selected' : '' }}" for="fmt-json">
                                <input type="radio" id="fmt-json" name="format" value="json"
                                    {{ old('format', $cvSettings['format'] ?? 'pdf') === 'json' ? 'checked' : '' }}
                                    class="cv-format-radio">
                                <div class="cv-format-card__check">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                        <polyline points="20 6 9 17 4 12" />
                                    </svg>
                                </div>
                                <div class="cv-format-card__icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                        <polyline points="14 2 14 8 20 8"/>
                                        <line x1="16" y1="13" x2="8" y2="13"/>
                                        <line x1="16" y1="17" x2="8" y2="17"/>
                                        <polyline points="10 9 9 9 8 9"/>
                                    </svg>
                                </div>
                                <span class="cv-format-card__name">JSON</span>
                                <span class="cv-format-card__sub">Exportación de datos estructurados</span>
                            </label>

                        </div>
                    </section>

                    <div class="section-divider"></div>

                    {{-- ── SECCIÓN: Contenido del CV ────────────────────── --}}
                    <section class="cv-section">
                        <h2 class="cv-section__title">Contenido del CV</h2>
                        <div class="cv-content-grid">

                            @php
                            $fields = [
                            'show_photo' => 'Foto de perfil',
                            'show_location' => 'Ubicación',
                            'show_email' => 'Email',
                            'show_projects' => 'Proyectos',
                            'show_experience' => 'Experiencia',
                            'show_education' => 'Educación',
                            ];
                            @endphp

                            @foreach($fields as $name => $label)
                            <label class="cv-checkbox-item" for="{{ $name }}">
                                <input type="checkbox" id="{{ $name }}" name="{{ $name }}"
                                    value="1"
                                    {{ old($name, $cvSettings[$name] ?? true) ? 'checked' : '' }}
                                    class="cv-checkbox">
                                <span class="cv-checkbox-custom">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                        <polyline points="20 6 9 17 4 12" />
                                    </svg>
                                </span>
                                <span class="cv-checkbox-label">{{ $label }}</span>
                            </label>
                            @endforeach

                        </div>
                    </section>

                    <div class="section-divider"></div>

                    {{-- ── SECCIÓN: Orden de secciones ─────────────────── --}}
                    <section class="cv-section">
                        <h2 class="cv-section__title">Orden de secciones</h2>
                        <p class="cv-section__hint">Arrastra las secciones para cambiar el orden en que aparecerán en tu CV.</p>

                        {{-- Contenedor donde el JS inyecta los inputs section_order[] --}}
                        <div id="sectionsOrderInputs">
                            @foreach($cvSettings['section_order'] as $key)
                            <input type="hidden" name="section_order[]" value="{{ $key }}">
                            @endforeach
                        </div>

                        <ul class="cv-sortable" id="cvSortable">
                            @php
                            $sectionLabels = [
                            'experience' => 'Experiencia',
                            'projects' => 'Proyectos',
                            'education' => 'Educación',
                            'skills' => 'Habilidades',
                            ];
                            @endphp

                            @foreach($cvSettings['section_order'] as $sectionKey)
                            <li class="cv-sortable__item" data-section="{{ $sectionKey }}">
                                <span class="cv-sortable__handle" aria-hidden="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" viewBox="0 0 24 24">
                                        <line x1="3" y1="7" x2="21" y2="7" />
                                        <line x1="3" y1="12" x2="21" y2="12" />
                                        <line x1="3" y1="17" x2="21" y2="17" />
                                    </svg>
                                </span>
                                <span class="cv-sortable__label">{{ $sectionLabels[$sectionKey] }}</span>
                                <span class="cv-sortable__arrow" aria-hidden="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                        <polyline points="9 18 15 12 9 6" />
                                    </svg>
                                </span>
                            </li>
                            @endforeach
                        </ul>
                    </section>

                </div>

                {{-- ──── PANEL DERECHO: Vista previa ──── --}}
                <aside class="cv-preview-panel">
                    <h2 class="cv-preview-panel__title">Vista previa del CV</h2>

                    {{-- ── PDF Visual (diseño clásico: dos columnas) ── --}}
                    <div class="cv-preview-card cv-preview-format cvp-visual" id="previewPdf" data-format="pdf">
                        <div class="cvp-visual__header">
                            <div class="cvp-visual__avatar" id="cvpAvatar" style="{{ ($cvSettings['show_photo'] ?? true) ? '' : 'display:none' }}"></div>
                            <div class="cvp-visual__info">
                                <div class="cvp-visual__name">{{ $profile->user->name ?? 'Tu nombre' }}</div>
                                <div class="cvp-visual__handle">&#64;{{ $profile->user->username ?? 'username' }}</div>
                                <div class="cvp-visual__role">{{ $profile->user->role ?? 'Software' }} Developer</div>
                            </div>
                            <div class="cvp-visual__qr"></div>
                        </div>
                        <div class="cvp-visual__body">
                            <div class="cvp-visual__sidebar">
                                <div class="cvp-visual__sb-title">Contacto</div>
                                <div class="cvp-visual__sb-line"></div>
                                <div class="cvp-visual__sb-line cvp-visual__sb-line--short"></div>
                                <div class="cvp-visual__sb-divider"></div>
                                <div class="cvp-visual__sb-title">Tecnologías</div>
                                <div class="cvp-visual__sb-grid">
                                    <div class="cvp-visual__sb-dot"></div>
                                    <div class="cvp-visual__sb-dot"></div>
                                    <div class="cvp-visual__sb-dot"></div>
                                    <div class="cvp-visual__sb-dot"></div>
                                </div>
                            </div>
                            <div class="cvp-visual__content">
                                <div class="cvp-visual__bio-line"></div>
                                <div class="cvp-visual__bio-line cvp-visual__bio-line--short"></div>
                                <div class="cvp-sections" id="cvpSections">
                                    @foreach($cvSettings['section_order'] as $sec)
                                    <div class="cvp-section" data-section="{{ $sec }}" id="cvp{{ ucfirst($sec) }}">
                                        <div class="cvp-section__title">{{ ['experience' => 'Experiencia Laboral', 'projects' => 'Proyectos', 'education' => 'Educación', 'skills' => 'Habilidades'][$sec] }}</div>
                                        <div class="cvp-skeleton-group">
                                            <div class="cvp-skeleton cvp-skeleton--sm"></div>
                                            <div class="cvp-skeleton"></div>
                                            <div class="cvp-skeleton cvp-skeleton--md"></div>
                                            <div class="cvp-skeleton cvp-skeleton--sm"></div>
                                            <div class="cvp-skeleton"></div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ── PDF ATS ── --}}
                    <div class="cv-preview-card cv-preview-format" id="previewAts" data-format="ats" style="display:none">
                        <div class="cvp-ats">
                            <div class="cvp-ats__header">
                                <div class="cvp-ats__name">{{ $profile->user->name ?? 'Tu nombre' }}</div>
                                <div class="cvp-ats__meta">
                                    {{ $profile->user->email ?? 'email@ejemplo.com' }}
                                    @if(!empty($profile->city)) | {{ $profile->city }} @endif
                                </div>
                            </div>
                            <div class="cvp-ats__sections" id="cvpSectionsAts">
                                @foreach($cvSettings['section_order'] as $sec)
                                <div class="cvp-ats__section" data-section="{{ $sec }}">
                                    <div class="cvp-ats__section-title">{{ ['experience' => 'EXPERIENCIA LABORAL', 'projects' => 'PROYECTOS', 'education' => 'EDUCACIÓN', 'skills' => 'HABILIDADES TÉCNICAS'][$sec] ?? $sec }}</div>
                                    <div class="cvp-ats__line"></div>
                                    <div class="cvp-ats__line cvp-ats__line--short"></div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- ── JSON ── --}}
                    <div class="cv-preview-card cv-preview-format" id="previewJson" data-format="json" style="display:none">
                        <pre class="cvp-json"><span class="cvp-json__key">"meta"</span>: {<span class="cvp-json__key">"generator"</span>: <span class="cvp-json__str">"Fluxa"</span>},
<span class="cvp-json__key">"personal"</span>: {<span class="cvp-json__key">"name"</span>: <span class="cvp-json__str">"{{ $profile->user->name ?? 'Tu nombre' }}"</span>},
<span class="cvp-json__key">"sections"</span>: [
  { <span class="cvp-json__key">"type"</span>: <span class="cvp-json__str">"{{ $cvSettings['section_order'][0] ?? 'experience' }}"</span> }
]</pre>
                    </div>

                    {{-- ── Botones de descarga directa ── --}}
                    <div class="cv-download-group">
                        <a href="{{ route('cv.download.format', 'pdf') }}" class="cv-download-btn cv-download-btn--pdf" id="downloadPdfBtn">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                <polyline points="7 10 12 15 17 10"/>
                                <line x1="12" y1="15" x2="12" y2="3"/>
                            </svg>
                            Descargar PDF Visual
                        </a>
                        <a href="{{ route('cv.download.format', 'ats') }}" class="cv-download-btn cv-download-btn--ats" id="downloadAtsBtn">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                <polyline points="7 10 12 15 17 10"/>
                                <line x1="12" y1="15" x2="12" y2="3"/>
                            </svg>
                            Descargar PDF ATS
                        </a>
                        <a href="{{ route('cv.download.format', 'json') }}" class="cv-download-btn cv-download-btn--json" id="downloadJsonBtn">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                <polyline points="7 10 12 15 17 10"/>
                                <line x1="12" y1="15" x2="12" y2="3"/>
                            </svg>
                            Descargar JSON
                        </a>
                    </div>
                </aside>

            </div>{{-- /.cv-layout --}}

            {{-- ──── FOOTER: Acciones ──── --}}
            <div class="cv-footer">
                <button type="submit" class="btn-submit" id="btnSaveCV">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" />
                        <polyline points="17 21 17 13 7 13 7 21" />
                        <polyline points="7 3 7 8 15 8" />
                    </svg>
                    Guardar configuración
                </button>

                <a href="{{ route('cv.restore') }}" class="btn-restore">
                    Restaurar
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                        <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8" />
                        <path d="M3 3v5h5" />
                    </svg>
                </a>
            </div>

        </form>
    </main>
</div>

@endsection

@push('styles')
@vite('resources/css/profile/shared.css')
@vite('resources/css/profile/sidebar.css')
@vite('resources/css/profile/cv.css')
@endpush

@push('scripts')
{{-- SortableJS desde CDN --}}
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
@vite('resources/js/profile/cv.js')
@vite('resources/js/profile/avatar.js')
@endpush
