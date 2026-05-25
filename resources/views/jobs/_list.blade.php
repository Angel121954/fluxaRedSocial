{{-- Partial para AJAX pagination (loadMore.js) --}}
@forelse ($jobs as $job)
@include('jobs._card', ['job' => $job])
@empty
@if(!isset($hideEmpty) || !$hideEmpty)
<div class="jobs-empty">
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="jobs-empty-icon" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
            d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
    </svg>
    <h3 class="jobs-empty-title">No encontramos ofertas</h3>
    <p class="jobs-empty-text">Intenta con otros filtros o amplía tu búsqueda.</p>
</div>
@endif
@endforelse

@if ($jobs->hasMorePages())
<div class="jobs-load-more-wrap" id="loadMoreWrap">
    <button type="button" class="btn-load-more" id="btnLoadMore" data-url="{{ $jobs->nextPageUrl() }}">
        Ver más ofertas
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"
            style="width:15px;height:15px">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>
</div>
@endif