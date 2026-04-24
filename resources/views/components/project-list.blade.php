@forelse($projects as $project)
<x-project-card :project="$project" />
@empty
<div class="feed-empty">
    <div class="feed-empty-icon">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
        </svg>
    </div>
    <h3 class="feed-empty-title">No hay proyectos disponibles</h3>
    <p class="feed-empty-text">¡Explora las tendencias o recientes para descubrir proyectos interesantes!</p>
</div>
@endforelse

@if(method_exists($projects, 'hasMorePages') && $projects->hasMorePages())
<div class="load-more-wrapper">
    <button
        class="btn-load-more"
        data-url="{{ $projects->nextPageUrl() }}">
        Cargar más proyectos
    </button>

    <p class="load-more-status"
        data-total="{{ $projects->total() }}">
        Has visto {{ $projects->count() }} de {{ $projects->total() }} proyectos
    </p>
</div>
@endif
