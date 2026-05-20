/**
 * admin/diary/close.js — Confirmar cierre de diario con Swal
 */

document.addEventListener('click', function (e) {
    const btn = e.target.closest('.diary-admin-card__close-btn');
    if (!btn) return;

    const url = btn.dataset.url;

    Swal.fire({
        title: '¿Cerrar diario?',
        text: 'Los usuarios ya no podrán responder a esta pregunta.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, cerrar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#dc2626',
    }).then(function (result) {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = url;
            form.innerHTML = '<input type="hidden" name="_method" value="PATCH"><input type="hidden" name="_token" value="' + document.querySelector('meta[name="csrf-token"]').content + '">';
            document.body.appendChild(form);
            form.submit();
        }
    });
});
