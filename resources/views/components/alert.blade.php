{{-- Mensajes de sesión --}}
@if(session('success'))
<div class="notif-alert notif-alert--success">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
        <polyline points="20 6 9 17 4 12" />
    </svg>
    {{ session('success') }}
</div>
@endif

@if($errors->any())
<div class="notif-alert notif-alert--error">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
        <circle cx="12" cy="12" r="10" />
        <line x1="12" y1="8" x2="12" y2="12" />
        <line x1="12" y1="16" x2="12.01" y2="16" />
    </svg>
    {{ $errors->first() }}
</div>
@endif

@push('styles')
<link rel="stylesheet" href="{{ asset('css/profile/notificationsProfile.css') }}">
@endpush