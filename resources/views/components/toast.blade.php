<div id="toast" style="display: none;"></div>

@if(session('success') || $errors->any())
<script>
    window.sessionToast = {
        message: '{{ session('success') ?? addslashes($errors->first()) }}',
        type: '{{ session('success') ? 'success' : 'error' }}'
    };
</script>
@endif