@php
/**
* Solo se mapean los slugs que NO usan el tipo 'original' en Devicon.
* El slug viene directamente de $tech->slug en la DB (ya es el slug correcto de Devicon).
* Cualquier tecnología nueva agregada a la DB funciona automáticamente.
*/
$deviconTypeOverrides = [
'amazonwebservices' => 'plain-wordmark',
'angularjs' => 'plain',
'django' => 'plain',
'tailwindcss' => 'plain',
'kubernetes' => 'plain',
'graphql' => 'plain',
'firebase' => 'plain',
'express' => 'original-wordmark', // express no tiene variante 'original' con color
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
    <p class="stack-empty">Sin tecnologías agregadas aún.
        <a href=""><span class="text-[#0d8e91]">¿Deseas agregar?</span></a>
    </p>
    @endforelse
</div>