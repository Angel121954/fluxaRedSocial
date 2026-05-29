/**
 * admin/users/badge-modal.js — Modal de otorgar insignias (Beta Tester, Early Adopter, etc.)
 *
 * Soporta múltiples modales en la misma página. Cada modal se inicializa
 * llamando a initBadgeModal({ ... }) con los IDs de sus elementos.
 */

function initBadgeModal(config) {
    const backdrop = document.getElementById(config.backdropId);
    const search = document.getElementById(config.searchId);
    const list = document.getElementById(config.listId);
    const submit = document.getElementById(config.submitId);
    const submitText = document.getElementById(config.submitTextId);
    const openBtn = document.getElementById(config.openBtnId);
    const closeBtn = document.getElementById(config.closeBtnId);
    const cancelBtn = document.getElementById(config.cancelBtnId);

    if (!backdrop || !list) return;

    const checkboxes = list.querySelectorAll('.adm-badge-checkbox:not(:disabled)');

    function updateCount() {
        if (!submit || !submitText) return;
        const checked = Array.from(checkboxes).filter(cb => cb.checked);
        const count = checked.length;
        submit.disabled = count === 0;
        submitText.textContent = count > 0
            ? `Otorgar insignia a ${count} usuario${count !== 1 ? 's' : ''}`
            : 'Otorgar insignia';
    }

    checkboxes.forEach(cb => cb.addEventListener('change', updateCount));

    function filterItems(query) {
        Array.from(list.children).forEach(item => {
            const name = item.dataset.name || '';
            const handle = item.dataset.handle || '';
            const match = !query || name.includes(query) || handle.includes(query);
            item.style.display = match ? '' : 'none';
        });
    }

    function openModal() {
        window.closeAllModals?.();
        checkboxes.forEach(cb => { cb.checked = false; });
        updateCount();
        if (search) {
            search.value = '';
            filterItems('');
        }
        backdrop.classList.add('is-open');
        search?.focus();
    }

    function closeModal() {
        backdrop.classList.remove('is-open');
        if (search) search.value = '';
        filterItems('');
    }

    // Exponer globalmente para que closeAllModals (ban-modal.js) pueda llamarlo
    window[config.closeGlobalFn] = closeModal;

    openBtn?.addEventListener('click', openModal);
    closeBtn?.addEventListener('click', closeModal);
    cancelBtn?.addEventListener('click', closeModal);
    backdrop?.addEventListener('click', function (e) {
        if (e.target === backdrop) closeModal();
    });

    search?.addEventListener('input', function () {
        filterItems(this.value.toLowerCase().trim());
    });
}

document.addEventListener('DOMContentLoaded', function () {
    // Modal Beta Tester
    initBadgeModal({
        backdropId: 'badgeModalBackdrop',
        searchId: 'badgeUserSearch',
        listId: 'badgeUserList',
        submitId: 'submitBadge',
        submitTextId: 'submitBadgeText',
        openBtnId: 'openBadgeModal',
        closeBtnId: 'closeBadgeModal',
        cancelBtnId: 'cancelBadgeModal',
        closeGlobalFn: 'closeBadgeModal',
    });

    // Modal Early Adopter
    initBadgeModal({
        backdropId: 'earlyBadgeModalBackdrop',
        searchId: 'earlyBadgeUserSearch',
        listId: 'earlyBadgeUserList',
        submitId: 'earlySubmitBadge',
        submitTextId: 'earlySubmitBadgeText',
        openBtnId: 'openEarlyBadgeModal',
        closeBtnId: 'closeEarlyBadgeModal',
        cancelBtnId: 'cancelEarlyBadgeModal',
        closeGlobalFn: 'closeEarlyBadgeModal',
    });
});
