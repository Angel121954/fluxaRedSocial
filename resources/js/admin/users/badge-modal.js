import '../../shared/modal.js';

function initBadgeModal(config) {
    const backdrop = document.getElementById(config.backdropId);
    const search = document.getElementById(config.searchId);
    const list = document.getElementById(config.listId);
    const submit = document.getElementById(config.submitId);
    const submitText = document.getElementById(config.submitTextId);
    const openBtn = document.getElementById(config.openBtnId);

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

    function openBadgeModal() {
        window.closeAllModals?.();
        checkboxes.forEach(cb => { cb.checked = false; });
        updateCount();
        if (search) {
            search.value = '';
            filterItems('');
        }
        window.openModal(config.backdropId);
        search?.focus();
    }

    function closeBadgeModal() {
        window.closeModal(config.backdropId);
        if (search) search.value = '';
        filterItems('');
    }

    window[config.closeGlobalFn] = closeBadgeModal;

    openBtn?.addEventListener('click', openBadgeModal);

    search?.addEventListener('input', function () {
        filterItems(this.value.toLowerCase().trim());
    });
}

document.addEventListener('DOMContentLoaded', function () {
    initBadgeModal({
        backdropId: 'badgeModalBackdrop',
        searchId: 'badgeUserSearch',
        listId: 'badgeUserList',
        submitId: 'submitBadge',
        submitTextId: 'submitBadgeText',
        openBtnId: 'openBadgeModal',
        closeGlobalFn: 'closeBadgeModal',
    });

    initBadgeModal({
        backdropId: 'earlyBadgeModalBackdrop',
        searchId: 'earlyBadgeUserSearch',
        listId: 'earlyBadgeUserList',
        submitId: 'earlySubmitBadge',
        submitTextId: 'earlySubmitBadgeText',
        openBtnId: 'openEarlyBadgeModal',
        closeGlobalFn: 'closeEarlyBadgeModal',
    });
});
