/**
 * admin/shared/ban-modal.js — Modal de confirmar baneo + desbaneo directo
 */

document.addEventListener('DOMContentLoaded', function () {
    const banBackdrop = document.getElementById('banModalBackdrop');
    const banUserName = document.getElementById('banUserName');
    const banReason = document.getElementById('banReason');
    const confirmBanBtn = document.getElementById('confirmBanBtn');

    let activeBanUserId = null;

    if (!banBackdrop) return;

    /* Abrir modal de baneo */
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-ban');
        if (!btn) return;

        const userId = btn.dataset.userId;
        const name = btn.dataset.userName || 'este usuario';

        if (banUserName) banUserName.textContent = name;
        if (banReason) banReason.value = '';

        window.closeAllModals?.();

        activeBanUserId = userId;
        banBackdrop?.classList.add('is-open');
        banReason?.focus();
    });

    /* Confirmar baneo */
    confirmBanBtn?.addEventListener('click', function () {
        if (!activeBanUserId) return;

        const reasonInput = document.getElementById(`ban-reason-${activeBanUserId}`);
        const banForm = document.getElementById(`ban-form-${activeBanUserId}`);

        if (reasonInput) reasonInput.value = banReason?.value?.trim() || '';

        closeBanModal();

        if (banForm) {
            banForm.submit();
        }
    });

    /* Desbaneo directo */
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-unban');
        if (!btn) return;

        const userId = btn.dataset.userId;
        const name = btn.dataset.userName || 'este usuario';
        const form = document.getElementById(`unban-form-${userId}`);

        if (!form) return;

        Swal.fire({
            title: '¿Desbanear a ' + name + '?',
            text: 'Recuperará acceso completo a la plataforma.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, desbanear',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#16a34a',
        }).then(function (result) {
            if (result.isConfirmed) form.submit();
        });
    });

    document.getElementById('closeBanModal')?.addEventListener('click', closeBanModal);
    document.getElementById('cancelBanModal')?.addEventListener('click', closeBanModal);
    banBackdrop?.addEventListener('click', function (e) {
        if (e.target === banBackdrop) closeBanModal();
    });

    function closeBanModal() {
        banBackdrop?.classList.remove('is-open');
        activeBanUserId = null;
    }

    window.closeBanModal = closeBanModal;

    window.closeAllModals = function () {
        window.closeBadgeModal?.();
        window.closeEarlyBadgeModal?.();
        window.closeBanModal?.();
    };
});
