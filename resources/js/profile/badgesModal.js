(function () {
    'use strict';

    const backdrop = document.getElementById('badgesModal');
    const closeBtn = document.getElementById('badgesModalClose');

    if (!backdrop) return;

    function openModal() {
        backdrop.classList.add('is-open');
        lockBodyScroll();
    }

    function closeModal() {
        backdrop.classList.remove('is-open');
        unlockBodyScroll();
    }

    if (closeBtn) closeBtn.addEventListener('click', closeModal);

    backdrop.addEventListener('click', function (e) {
        if (e.target === backdrop) closeModal();
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && backdrop.classList.contains('is-open')) {
            closeModal();
        }
    });

    window.openBadgesModal = openModal;

})();
