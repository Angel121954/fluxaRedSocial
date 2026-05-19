/**
 * admin/suggestions/delete.js — Confirmación de eliminación de sugerencias
 */

(function () {
    document.addEventListener('submit', function (e) {
        const form = e.target.closest('.form-delete-suggestion');
        if (!form) return;

        e.preventDefault();

        Swal.fire({
            title: '¿Eliminar sugerencia?',
            text: 'Esta acción no se puede deshacer.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#dc2626',
        }).then(function (result) {
            if (result.isConfirmed) form.submit();
        });
    });
})();
