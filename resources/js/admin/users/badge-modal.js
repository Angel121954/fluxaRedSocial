/**
 * admin/users/badge-modal.js — Modal de otorgar insignia Beta Tester
 */

document.addEventListener('DOMContentLoaded', function () {
    const badgeBackdrop = document.getElementById('badgeModalBackdrop');
    const badgeSearch = document.getElementById('badgeUserSearch');
    const badgeList = document.getElementById('badgeUserList');
    const submitBadge = document.getElementById('submitBadge');
    const submitBadgeText = document.getElementById('submitBadgeText');
    const badgeCheckboxes = badgeList?.querySelectorAll('.adm-badge-checkbox:not(:disabled)');

    if (!badgeBackdrop) return;

    function updateBadgeCount() {
        if (!badgeCheckboxes || !submitBadge || !submitBadgeText) return;
        const checked = Array.from(badgeCheckboxes).filter(cb => cb.checked);
        const count = checked.length;
        submitBadge.disabled = count === 0;
        submitBadgeText.textContent = count > 0
            ? `Otorgar insignia a ${count} usuario${count !== 1 ? 's' : ''}`
            : 'Otorgar insignia';
    }

    badgeCheckboxes?.forEach(cb => cb.addEventListener('change', updateBadgeCount));

    document.getElementById('openBadgeModal')?.addEventListener('click', function () {
        window.closeAllModals?.();

        badgeCheckboxes?.forEach(cb => { cb.checked = false; });
        updateBadgeCount();

        if (badgeSearch) {
            badgeSearch.value = '';
            filterBadgeItems('');
        }
        badgeBackdrop?.classList.add('is-open');
        badgeSearch?.focus();
    });

    badgeSearch?.addEventListener('input', function () {
        filterBadgeItems(this.value.toLowerCase().trim());
    });

    function filterBadgeItems(query) {
        if (!badgeList) return;
        Array.from(badgeList.children).forEach(item => {
            const name = item.dataset.name || '';
            const handle = item.dataset.handle || '';
            const match = !query || name.includes(query) || handle.includes(query);
            item.style.display = match ? '' : 'none';
        });
    }

    window.closeBadgeModal = function () {
        badgeBackdrop?.classList.remove('is-open');
        if (badgeSearch) badgeSearch.value = '';
        filterBadgeItems('');
    };

    document.getElementById('closeBadgeModal')?.addEventListener('click', closeBadgeModal);
    document.getElementById('cancelBadgeModal')?.addEventListener('click', closeBadgeModal);
    badgeBackdrop?.addEventListener('click', function (e) {
        if (e.target === badgeBackdrop) closeBadgeModal();
    });
});
