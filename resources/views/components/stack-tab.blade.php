@props([
    'technologies',
    'isOwner' => false,
    'groupedTechnologies' => [],
    'categoryLabels' => [],
    'categoryOrder' => [],
    'favoriteTechIds' => [],
])

@foreach($categoryOrder as $cat)
    @php $items = $groupedTechnologies->get($cat); @endphp
    @if($items && $items->isNotEmpty())
    <div class="stack-category">
        <h3 class="stack-category-title">{{ $categoryLabels[$cat] ?? 'Otros' }}</h3>
        <div class="stack-grid">
            @foreach($items as $tech)
            @php
            $isFav = in_array($tech->id, $favoriteTechIds);
            @endphp
            <div class="stack-card">
                @if($isOwner)
                <button type="button"
                    class="stack-card-heart {{ $isFav ? 'is-favorite' : '' }}"
                    data-tech-id="{{ $tech->id }}"
                    aria-label="{{ $isFav ? 'Quitar de favoritos' : 'Agregar a favoritos' }}"
                    title="{{ $isFav ? 'Tecnología destacada' : 'Marcar como destacada' }}">
                    <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                    </svg>
                </button>
                @elseif($isFav)
                <span class="stack-card-heart is-favorite" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                    </svg>
                </span>
                @endif
                <a href="{{ $tech->website_url ?? '#' }}"
                    target="{{ $tech->website_url ? '_blank' : '_self' }}"
                    rel="noopener noreferrer"
                    class="stack-card-link">
                    <div class="stack-icon-wrap">
                        <i class="{{ $tech->deviconClass() }} colored stack-icon-css"></i>
                    </div>
                    <span class="stack-name">{{ $tech->name }}</span>
                </a>
            </div>
            @endforeach
        </div>
    </div>
    @endif
@endforeach

@if($technologies->isEmpty())
    @if($isOwner)
    <div class="stack-empty">
        <p>Sin tecnologías agregadas aún.</p>
        <button class="stack-add-btn" onclick="window.openStackModal()">Agregar tecnologías</button>
    </div>
    @else
    <p class="stack-empty">Este usuario aún no ha agregado tecnologías.</p>
    @endif
@endif

@if($technologies->isNotEmpty() && $isOwner)
<div class="stack-footer">
    <button class="stack-add-link" onclick="window.openStackModal()">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Agregar más tecnologías
    </button>
</div>
@endif
