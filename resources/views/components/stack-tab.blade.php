@props(['technologies', 'isOwner' => false])

@php
/**
 * Solo se mapean los tipos de iconos que NO usan 'original' en Devicon.
 * El slug viene directamente de $tech->slug en la DB (ya es el slug correcto de Devicon).
 */
$deviconTypeOverrides = [
    'amazonwebservices' => 'plain-wordmark',
    'angularjs' => 'plain',
    'django' => 'plain',
    'tailwindcss' => 'plain',
    'kubernetes' => 'plain',
    'graphql' => 'plain',
    'firebase' => 'plain',
    'express' => 'original-wordmark',
];
@endphp

<div class="stack-grid">
    @forelse($technologies as $tech)
    @php
        $iconSlug = (string) $tech->slug;
        $iconType = $deviconTypeOverrides[$iconSlug] ?? 'original';
        $iconUrl = "https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/{$iconSlug}/{$iconSlug}-{$iconType}.svg";
        $initials = strtoupper(substr((string) $tech->name, 0, 2));
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
    @empty
        @if($isOwner)
        <div class="stack-empty">
            <p>Sin tecnologías agregadas aún.</p>
            <button class="stack-add-btn" onclick="window.openStackModal()">Agregar tecnologías</button>
        </div>
        @else
        <p class="stack-empty">Este usuario aún no ha agregado tecnologías.</p>
        @endif
    @endforelse
</div>

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