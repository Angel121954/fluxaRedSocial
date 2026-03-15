{{--
    resources/views/components/project-media.blade.php
    Uso: @include('components.project-media', ['media' => $project->media])
--}}

@php
$items = $media->sortBy('position')->values();
$total = $items->count();
$extra = $total > 4 ? $total - 4 : 0;
$layout = match(true) {
$total === 1 => 'single',
$total === 2 => 'duo',
$total === 3 => 'trio',
default => 'quad',
};

$cloud = config('cloudinary.cloud_name', env('CLOUDINARY_CLOUD_NAME'));

$cdn = function(string $publicId, string $preset = 'fill') use ($cloud): string {
$t = match($preset) {
'wide' => 'c_limit,q_auto:best,f_auto,w_900', // ← c_limit, sin h forzado
'tall' => 'c_fill,g_auto,q_auto:good,f_auto,w_600,h_700',
default => 'c_fill,g_auto,q_auto:good,f_auto,w_600,h_600',
};
return "https://res.cloudinary.com/{$cloud}/image/upload/{$t}/{$publicId}";
};
@endphp

@if($total > 0)
<div
    class="pm-grid pm-layout--{{ $layout }}"
    data-total="{{ $total }}"
    aria-label="Imágenes del proyecto ({{ $total }})">
    @foreach($items->take(4) as $idx => $item)
    @php
    $preset = ($layout === 'single') ? 'wide' : (($layout === 'trio' && $idx === 0) ? 'tall' : 'fill');
    $isLast = ($idx === 3 && $extra > 0);
    $srcUrl = $item->public_id ? $cdn($item->public_id, $preset) : $item->media_url;
    $fullUrl = $item->public_id
    ? "https://res.cloudinary.com/{$cloud}/image/upload/q_auto:best,f_auto/{$item->public_id}"
    : $item->media_url;
    @endphp

    <button
        class="pm-item pm-item--{{ $idx + 1 }}{{ $isLast ? ' pm-item--more' : '' }}"
        data-lightbox="{{ $fullUrl }}"
        data-index="{{ $idx }}"
        aria-label="Ver imagen {{ $idx + 1 }}"
        type="button">
        @if($item->type === 'video')
        <video class="pm-media" src="{{ $item->media_url }}"
            muted loop playsinline preload="metadata"></video>
        <span class="pm-play-badge" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="currentColor">
                <path d="M8 5v14l11-7z" />
            </svg>
        </span>
        @else
        <img
            class="pm-media"
            src="{{ $srcUrl }}"
            alt="Media {{ $idx + 1 }}"
            loading="{{ $idx === 0 ? 'eager' : 'lazy' }}"
            decoding="async" />
        @endif

        @if($isLast)
        <span class="pm-more-overlay" aria-hidden="true">
            <span class="pm-more-n">+{{ $extra }}</span>
        </span>
        @endif
    </button>
    @endforeach
</div>
@endif

{{--
    @once garantiza que el lightbox, el CSS y el JS
    se incluyan UNA SOLA VEZ aunque este partial
    se llame N veces dentro de un @foreach
--}}
@once
<div class="pm-lightbox" id="pmLightbox"
    role="dialog" aria-modal="true"
    aria-label="Visor de imagen" hidden>

    <button class="pm-lb-close" id="pmLbClose" aria-label="Cerrar">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>

    <button class="pm-lb-nav pm-lb-prev" id="pmLbPrev" aria-label="Anterior">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
        </svg>
    </button>

    <div class="pm-lb-stage">
        <img class="pm-lb-img" id="pmLbImg" src="" alt="Imagen ampliada" />
        <div class="pm-lb-spinner" id="pmLbSpinner" aria-hidden="true"></div>
    </div>

    <button class="pm-lb-nav pm-lb-next" id="pmLbNext" aria-label="Siguiente">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
        </svg>
    </button>

    <div class="pm-lb-counter" id="pmLbCounter" aria-live="polite"></div>
</div>

@push('styles')
<link rel="stylesheet" href="{{ asset('css/projectMedia.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('js/projectMedia.js') }}"></script>
@endpush
@endonce