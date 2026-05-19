<div id="toast" style="display: none;"></div>

@if(session('success') || session('error') || $errors->any())
<script>
    window.sessionToast = {
        message: '{{ session('success') ?? session('error') ?? addslashes($errors->first()) }}',
        type: '{{ session('error') ? 'error' : (session('success') ? 'success' : 'error') }}'
    };
</script>
@endif