@props(['projects' => collect([]), 'selectedProjectIds' => []])

<div class="modal-backdrop" id="cvProjectModal">
    <div class="modal-card">
        <div class="modal-header">
            <div class="modal-header-text">
                <div class="modal-title">Seleccionar proyectos</div>
                <div class="modal-subtitle">Elige hasta 3 proyectos para incluir en tu CV</div>
            </div>
            <button type="button" class="modal-close" data-close="cvProjectModal" aria-label="Cerrar">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" viewBox="0 0 24 24">
                    <line x1="18" y1="6" x2="6" y2="18" />
                    <line x1="6" y1="6" x2="18" y2="18" />
                </svg>
            </button>
        </div>

        <div class="modal-body">
            <div class="cv-modal-counter">
                <span class="cv-modal-counter__label">Seleccionados:</span>
                <span class="cv-modal-counter__value" id="cvProjectCount">
                    {{ count($selectedProjectIds) }}/3
                </span>
            </div>

            <div class="cv-modal-grid" id="cvProjectGrid">
                @forelse($projects as $project)
                @php $isSelected = in_array($project->id, $selectedProjectIds); @endphp
                <div class="cv-project-card {{ $isSelected ? 'cv-project-card--selected' : '' }}"
                     data-project-id="{{ $project->id }}">
                    <div class="cv-project-card__check">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            <polyline points="20 6 9 17 4 12" />
                        </svg>
                    </div>
                    <div class="cv-project-card__info">
                        <span class="cv-project-card__title">{{ $project->title }}</span>
                        @if($project->technologies->isNotEmpty())
                        <span class="cv-project-card__tech">{{ $project->technologies->pluck('name')->implode(', ') }}</span>
                        @endif
                    </div>
                </div>
                @empty
                <div class="cv-modal-empty">
                    <p>No tienes proyectos públicos aún.</p>
                    <a href="{{ route('projects.create') }}" class="btn-submit" style="display:inline-flex;margin-top:0.5rem;">Crear proyecto</a>
                </div>
                @endforelse
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-close="cvProjectModal">Cancelar</button>
            <button type="button" class="btn btn-primary" id="cvProjectConfirm">Confirmar selección</button>
        </div>
    </div>
</div>
