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

                    {{-- ── SECCIÓN: Apariencia del CV ──────────────────── --}}
                    <section class="cv-section">
                        <h2 class="cv-section__title">Apariencia del CV</h2>
                        <div class="cv-templates">

                            {{-- Clásico --}}
                            <label class="cv-template-card {{ old('template', $cvSettings['template'] ?? 'classic') === 'classic' ? 'cv-template-card--selected' : '' }}" for="tpl-classic">
                                <input type="radio" id="tpl-classic" name="template" value="classic"
                                    {{ old('template', $cvSettings['template'] ?? 'classic') === 'classic' ? 'checked' : '' }}
                                    class="cv-template-radio">
                                <div class="cv-template-card__check">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                        <polyline points="20 6 9 17 4 12" />
                                    </svg>
                                </div>
                                <div class="cv-template-card__preview cv-template-card__preview--classic">
                                    <div class="tpl-line tpl-line--title"></div>
                                    <div class="tpl-line tpl-line--sub"></div>
                                    <div class="tpl-divider"></div>
                                    <div class="tpl-line"></div>
                                    <div class="tpl-line tpl-line--short"></div>
                                    <div class="tpl-line"></div>
                                    <div class="tpl-line tpl-line--short"></div>
                                </div>
                                <span class="cv-template-card__name">Clásico</span>
                                <span class="cv-template-card__sub">Sin foto</span>
                            </label>

                            {{-- Moderno --}}
                            <label class="cv-template-card {{ old('template', $cvSettings['template'] ?? 'classic') === 'modern' ? 'cv-template-card--selected' : '' }}" for="tpl-modern">
                                <input type="radio" id="tpl-modern" name="template" value="modern"
                                    {{ old('template', $cvSettings['template'] ?? 'classic') === 'modern' ? 'checked' : '' }}
                                    class="cv-template-radio">
                                <div class="cv-template-card__check">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                        <polyline points="20 6 9 17 4 12" />
                                    </svg>
                                </div>
                                <div class="cv-template-card__preview cv-template-card__preview--modern">
                                    <div class="tpl-modern-header">
                                        <div class="tpl-avatar-circle"></div>
                                        <div class="tpl-modern-info">
                                            <div class="tpl-line tpl-line--title"></div>
                                            <div class="tpl-line tpl-line--sub"></div>
                                        </div>
                                    </div>
                                    <div class="tpl-divider"></div>
                                    <div class="tpl-line"></div>
                                    <div class="tpl-line tpl-line--short"></div>
                                    <div class="tpl-line"></div>
                                </div>
                                <span class="cv-template-card__name">Moderno</span>
                                <span class="cv-template-card__sub">Con foto</span>
                            </label>

                            {{-- Creativo --}}
                            <label class="cv-template-card {{ old('template', $cvSettings['template'] ?? 'classic') === 'creative' ? 'cv-template-card--selected' : '' }}" for="tpl-creative">
                                <input type="radio" id="tpl-creative" name="template" value="creative"
                                    {{ old('template', $cvSettings['template'] ?? 'classic') === 'creative' ? 'checked' : '' }}
                                    class="cv-template-radio">
                                <div class="cv-template-card__check">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                        <polyline points="20 6 9 17 4 12" />
                                    </svg>
                                </div>
                                <div class="cv-template-card__preview cv-template-card__preview--creative">
                                    <div class="tpl-creative-sidebar">
                                        <div class="tpl-avatar-circle tpl-avatar-circle--sm"></div>
                                        <div class="tpl-line tpl-line--white tpl-line--short"></div>
                                        <div class="tpl-line tpl-line--white tpl-line--xs"></div>
                                    </div>
                                    <div class="tpl-creative-body">
                                        <div class="tpl-line tpl-line--title"></div>
                                        <div class="tpl-line tpl-line--short"></div>
                                        <div class="tpl-line"></div>
                                    </div>
                                </div>
                                <span class="cv-template-card__name">Creativo</span>
                                <span class="cv-template-card__sub">Visual</span>
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
                            {{-- Fallback server-side: garantiza que siempre lleguen valores al submit --}}
                            @php
                            $defaultOrder = ['experience', 'projects', 'education', 'skills'];
                            $initialOrder = $cvSettings['section_order'] ?? $defaultOrder;
                            @endphp

                            @foreach($initialOrder as $key)
                            <input type="hidden" name="section_order[]" value="{{ $key }}">
                            @endforeach
                        </div>

                        <ul class="cv-sortable" id="cvSortable">
                            @php
                            $defaultOrder = ['experience', 'projects', 'education', 'skills'];
                            $savedOrder = $cvSettings['section_order'] ?? $defaultOrder;
                            $sectionLabels = [
                            'experience' => 'Experiencia',
                            'projects' => 'Proyectos',
                            'education' => 'Educación',
                            'skills' => 'Habilidades',
                            ];
                            @endphp

                            @foreach($savedOrder as $sectionKey)
                            @if(isset($sectionLabels[$sectionKey]))
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
                            @endif
                            @endforeach
                        </ul>
                    </section>

                </div>

                {{-- ──── PANEL DERECHO: Vista previa ──── --}}
                <aside class="cv-preview-panel">
                    <h2 class="cv-preview-panel__title">Vista previa del CV</h2>
                    <div class="cv-preview-card" id="cvPreviewCard">

                        {{-- Header del CV --}}
                        <div class="cvp-header">
                            <div class="cvp-avatar" id="cvpAvatar" style="{{ ($cvSettings['show_photo'] ?? true) ? '' : 'display:none' }}">
                                <img src="{{ $profile->avatar ?? '' }}" alt="Avatar" class="cvp-avatar__img">
                            </div>
                            <div class="cvp-info">
                                <h3 class="cvp-name">{{ $profile->user->name ?? 'Tu nombre' }}</h3>
                                <p class="cvp-username">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                        <circle cx="12" cy="7" r="4" />
                                    </svg>
                                    {{ $profile->user->username ?? 'username' }}
                                </p>
                                <p class="cvp-bio">{{ Str::limit($profile->bio ?? 'Tu biografía profesional aparecerá aquí.', 120) }}</p>
                            </div>
                        </div>

                        {{-- Secciones del CV (orden dinámico) --}}
                        <div class="cvp-sections" id="cvpSections">

                            <div class="cvp-section" data-section="experience" id="cvpExperience">
                                <h4 class="cvp-section__title">Experiencia Laboral</h4>
                                <div class="cvp-skeleton-group">
                                    <div class="cvp-skeleton cvp-skeleton--sm"></div>
                                    <div class="cvp-skeleton"></div>
                                    <div class="cvp-skeleton cvp-skeleton--md"></div>
                                    <div class="cvp-skeleton cvp-skeleton--sm"></div>
                                    <div class="cvp-skeleton"></div>
                                </div>
                            </div>

                            <div class="cvp-section" data-section="projects" id="cvpProjects">
                                <h4 class="cvp-section__title">Proyectos</h4>
                                <div class="cvp-skeleton-group">
                                    <div class="cvp-skeleton cvp-skeleton--sm"></div>
                                    <div class="cvp-skeleton cvp-skeleton--lg"></div>
                                </div>
                            </div>

                            <div class="cvp-section" data-section="education" id="cvpEducation">
                                <h4 class="cvp-section__title">Educación</h4>
                                <div class="cvp-skeleton-group">
                                    <div class="cvp-skeleton cvp-skeleton--sm"></div>
                                    <div class="cvp-skeleton cvp-skeleton--md"></div>
                                    <div class="cvp-skeleton"></div>
                                </div>
                            </div>

                            <div class="cvp-section" data-section="skills" id="cvpSkills" style="display:none">
                                <h4 class="cvp-section__title">Habilidades</h4>
                                <div class="cvp-skeleton-group">
                                    <div class="cvp-skeleton cvp-skeleton--sm"></div>
                                    <div class="cvp-skeleton cvp-skeleton--md"></div>
                                </div>
                            </div>

                        </div>
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