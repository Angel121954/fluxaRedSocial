@props(['type' => 'success', 'message' => null])

@if($message || session('success') || $errors->any())
<div class="notif-alert notif-alert--{{ $type }}" id="alert-{{ $type }}">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
        @switch($type)
            @case('success')
                <polyline points="20 6 9 17 4 12" />
                @break
            @case('error')
                <circle cx="12" cy="12" r="10" />
                <line x1="12" y1="8" x2="12" y2="12" />
                <line x1="12" y1="16" x2="12.01" y2="16" />
                @break
            @case('warning')
                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" />
                <line x1="12" y1="9" x2="12" y2="13" />
                <line x1="12" y1="17" x2="12.01" y2="17" />
                @break
            @case('info')
                <circle cx="12" cy="12" r="10" />
                <line x1="12" y1="16" x2="12" y2="12" />
                <line x1="12" y1="8" x2="12.01" y2="8" />
                @break
        @endswitch
    </svg>
    <span>{{ $message ?? session('success') ?? $errors->first() }}</span>
</div>
@endif