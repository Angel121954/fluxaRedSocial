<div id="toast" style="display: none;"></div>

@if(session()->has('success') || session()->has('error') || $errors->any())
@php
    $toastMessage = session('success') ?? session('error') ?? $errors->first();
    $toastType = session()->has('error') ? 'error' : (session()->has('success') ? 'success' : 'error');
@endphp
<script>
    window.sessionToast = {!! json_encode(['message' => $toastMessage, 'type' => $toastType]) !!};
</script>
@endif