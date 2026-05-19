@props(['badges' => collect(), 'allBadges' => collect(), 'isOwner' => false])

@if($allBadges->count() > 0)
<div class="badges-section">
    <div class="badges-header">
        <h3 class="badges-title">Logros</h3>
        @if($badges->count() > 0 && $isOwner)
        <button class="badges-view-all" onclick="window.openBadgesModal()">
            Ver todos ({{ $badges->count() }}/{{ $allBadges->count() }})
        </button>
        @endif
    </div>

    <div class="badges-grid">
        @forelse($badges->take(6) as $badge)
        <div class="badge-card" title="{{ $badge->description }}">
            <div class="badge-icon badge-icon--tier-{{ $badge->tier }}">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
                </svg>
            </div>
            <span class="badge-name">{{ $badge->name }}</span>
        </div>
        @empty
        @if($isOwner)
        <p class="badges-empty">Completa acciones en Fluxa para ganar logros.
            <button class="badges-empty-link" onclick="window.openBadgesModal()">Ver todos los logros</button>
        </p>
        @else
        <p class="badges-empty">Este usuario aún no tiene logros.</p>
        @endif
        @endforelse
    </div>
</div>
@endif