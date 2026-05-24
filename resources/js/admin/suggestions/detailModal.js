(function () {
    const backdrop = document.getElementById('suggestionDetailModal');
    if (!backdrop) return;

    const statusMap = {
        pending: 'Pendiente',
        approved: 'Aprobado',
        reviewing: 'En revisión',
        rejected: 'Rechazado',
    };

    const statusBadge = document.getElementById('sdmStatus');
    const statusText = document.getElementById('sdmStatusText');
    const avatar = document.getElementById('sdmAvatar');
    const userName = document.getElementById('sdmUserName');
    const userHandle = document.getElementById('sdmUserHandle');
    const date = document.getElementById('sdmDate');
    const description = document.getElementById('sdmDescription');
    const imageField = document.getElementById('sdmImageField');
    const image = document.getElementById('sdmImage');

    function populateModal(btn) {
        const status = btn.dataset.status;
        const label = statusMap[status] || status;

        statusBadge.className = 'sdm-badge adm-badge adm-badge--' + status;
        statusText.textContent = label;

        userName.textContent = btn.dataset.userName;
        userHandle.textContent = btn.dataset.userHandle;
        avatar.src = btn.dataset.userAvatar || '';
        avatar.alt = btn.dataset.userName;

        date.textContent = btn.dataset.date;

        description.textContent = btn.dataset.description;

        const imgUrl = btn.dataset.image;
        if (imgUrl) {
            image.src = imgUrl;
            image.alt = 'Imagen adjunta';
            imageField.style.display = '';
        } else {
            image.src = '';
            image.alt = '';
            imageField.style.display = 'none';
        }
    }

    function openModal(btn) {
        populateModal(btn);
        backdrop.classList.add('is-open');
        window.lockBodyScroll?.();
    }

    function closeModal() {
        backdrop.classList.remove('is-open');
        window.unlockBodyScroll?.();
    }

    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-view-suggestion');
        if (btn) {
            e.preventDefault();
            openModal(btn);
        }
    });

    backdrop.addEventListener('click', function (e) {
        if (e.target === backdrop) closeModal();
    });

    document.querySelectorAll('[data-close="suggestionDetailModal"]').forEach(function (el) {
        el.addEventListener('click', closeModal);
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && backdrop.classList.contains('is-open')) {
            closeModal();
        }
    });
})();
