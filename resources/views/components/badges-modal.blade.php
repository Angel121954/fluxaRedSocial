@props([
    'badges' => collect(),
    'allBadges' => collect(),
    'badgeCategories' => [],
    'tierLabels' => [],
])

<div class="badges-modal-backdrop" id="badgesModal">
    <div class="badges-modal">
        <div class="badges-modal-header">
            <div class="badges-modal-header-text">
                <h3 class="badges-modal-title">Todos los logros</h3>
                <p class="badges-modal-subtitle">Has obtenido {{ $badges->count() }} de {{ $allBadges->count() }} logros disponibles</p>
            </div>
            <button class="badges-modal-close" id="badgesModalClose" aria-label="Cerrar">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="badges-modal-body">
            @foreach($badgeCategories as $catKey => $catLabel)
            @php
            $catBadges = $allBadges->where('category', $catKey);
            if ($catBadges->isEmpty()) continue;
            @endphp
            <div class="badges-modal-category">
                <h4 class="badges-modal-category-title">{{ $catLabel }}</h4>
                <div class="badges-modal-grid">
                    @foreach($catBadges as $badge)
                    @php $earned = $badges->firstWhere('id', $badge->id); @endphp
                    <div class="badges-modal-item {{ $earned ? '' : 'badges-modal-item--locked' }}">
                        <div class="badge-icon badge-icon--tier-{{ $badge->tier }} {{ $earned ? '' : 'badge-icon--locked' }}">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
                            </svg>
                        </div>
                        <div class="badges-modal-item-info">
                            <span class="badges-modal-item-name">{{ $badge->name }}</span>
                            <span class="badges-modal-item-desc">{{ $badge->description }}</span>
                            @if(!$earned)
                            <span class="badges-modal-item-locked">Por obtener</span>
                            @else
                            <span class="badges-modal-item-earned">{{ $tierLabels[$badge->tier] ?? '' }}</span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>