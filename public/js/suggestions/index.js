document.addEventListener('DOMContentLoaded', function() {
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('[data-dropdown-target]');

        if (btn) {
            e.stopPropagation();
            const targetId = btn.dataset.dropdownTarget;
            const dropdown = document.getElementById(targetId);
            if (dropdown) {
                const isOpen = dropdown.classList.toggle('open');
                btn.setAttribute('aria-expanded', isOpen);
            }
        } else if (!e.target.closest('.adm-dropdown')) {
            document.querySelectorAll('.adm-dropdown.open').forEach(function(d) {
                d.classList.remove('open');
            });
            document.querySelectorAll('[data-dropdown-target]').forEach(function(b) {
                b.setAttribute('aria-expanded', 'false');
            });
        }
    });

    const uploadZone = document.getElementById('uploadZone');
    const uploadInput = document.getElementById('image');
    const uploadPreview = document.getElementById('uploadPreview');
    const previewImg = document.getElementById('previewImg');

    if (uploadZone && uploadInput) {
        uploadZone.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('drag-over');
        });

        uploadZone.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.classList.remove('drag-over');
        });

        uploadZone.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('drag-over');
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                uploadInput.files = files;
                showPreview(files[0]);
            }
        });
    }

    document.querySelectorAll('.form-delete-suggestion').forEach(form => {
        form.addEventListener('submit', e => {
            e.preventDefault();
            Swal.fire({
                title: '¿Eliminar sugerencia?',
                text: 'Esta acción no se puede deshacer.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
            }).then(result => {
                if (result.isConfirmed) form.submit();
            });
        });
    });
});

function previewSuggestionImage(input) {
    if (input.files && input.files[0]) {
        showPreview(input.files[0]);
    }
}

function showPreview(file) {
    const preview = document.getElementById('uploadPreview');
    const img = document.getElementById('previewImg');
    if (preview && img && file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = function(e) {
            img.src = e.target.result;
            preview.classList.add('show');
        };
        reader.readAsDataURL(file);
    }
}
