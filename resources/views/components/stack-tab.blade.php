@props(['technologies', 'isOwner' => false])

@php
$categoryLabels = [
    'language' => 'Lenguajes',
    'framework' => 'Frameworks',
    'library' => 'Librerías',
    'database' => 'Bases de datos',
    'tool' => 'Herramientas',
    'platform' => 'Plataformas',
];

$grouped = $technologies->groupBy(function ($tech) {
    return $tech->category ?? 'other';
});

$categoryOrder = ['language', 'framework', 'library', 'database', 'tool', 'platform', 'other'];
@endphp

@foreach($categoryOrder as $cat)
    @php $items = $grouped->get($cat); @endphp
    @if($items && $items->isNotEmpty())
    <div class="stack-category">
        <h3 class="stack-category-title">{{ $categoryLabels[$cat] ?? 'Otros' }}</h3>
        <div class="stack-grid">
            @foreach($items as $tech)
            @php
            $iconUrl = $tech->iconUrl();
            $initials = $tech->initials();
            @endphp
            <a href="{{ $tech->website_url ?? '#' }}"
                target="{{ $tech->website_url ? '_blank' : '_self' }}"
                rel="noopener noreferrer"
                class="stack-card-link">
                <div class="stack-card">
                    <div class="stack-icon-wrap">
                        <img
                            src="{{ $iconUrl }}"
                            alt="{{ $tech->name }}"
                            class="stack-icon-img"
                            onerror="this.style.display='none';this.nextElementSibling.style.display='flex';" />
                        <span class="stack-icon-fallback">{{ $initials }}</span>
                    </div>
                    <span class="stack-name">{{ $tech->name }}</span>
                </div>
            </a>
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
