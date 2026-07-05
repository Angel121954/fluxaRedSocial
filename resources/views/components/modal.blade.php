@props([
    'maxWidth' => null,
    'hideHeader' => false,
    'hideClose' => false,
    'title' => null,
    'subtitle' => null,
])

<div class="modal-backdrop" id="{{ $id }}" role="dialog" aria-modal="true">
    <div class="modal-card @if($maxWidth) modal-card--{{ $maxWidth }} @endif">
        @unless($hideHeader)
            <div class="modal-header">
                @if(isset($headerIcon))
                    <div class="modal-header-icon">{{ $headerIcon }}</div>
                @endif

                @if(isset($header))
                    {{ $header }}
                @else
                    <div class="modal-header-text">
                        <div class="modal-title">{{ $title ?? '' }}</div>
                        @if($subtitle)
                            <div class="modal-subtitle">{{ $subtitle }}</div>
                        @endif
                    </div>
                @endif

                @unless($hideClose)
                    <button type="button" class="modal-close" data-close="{{ $id }}" aria-label="Cerrar">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                            <line x1="18" y1="6" x2="6" y2="18" />
                            <line x1="6" y1="6" x2="18" y2="18" />
                        </svg>
                    </button>
                @endunless
            </div>
        @endunless

        <div class="modal-body">
            {{ $slot }}
        </div>

        @if(isset($footer))
            <div class="modal-footer">
                {{ $footer }}
            </div>
        @endif
    </div>
</div>
