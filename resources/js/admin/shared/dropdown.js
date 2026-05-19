/**
 * admin/shared/dropdown.js — Menús de acción por fila
 */

document.addEventListener('DOMContentLoaded', function () {
    document.addEventListener('click', function (e) {
        const toggleBtn = e.target.closest('[data-dropdown-target]');

        if (toggleBtn) {
            e.stopPropagation();
            const targetId = toggleBtn.dataset.dropdownTarget;
            const dropdown = document.getElementById(targetId);
            if (!dropdown) return;

            document.querySelectorAll('.adm-dropdown.open').forEach(d => {
                if (d !== dropdown) {
                    d.classList.remove('open');
                    document.querySelector(`[data-dropdown-target="${d.id}"]`)
                        ?.setAttribute('aria-expanded', 'false');
                }
            });

            const isOpen = dropdown.classList.toggle('open');
            toggleBtn.setAttribute('aria-expanded', String(isOpen));

            if (isOpen) {
                const rect = toggleBtn.getBoundingClientRect();
                dropdown.style.top = (rect.bottom + 6) + 'px';
                dropdown.style.right = (window.innerWidth - rect.right) + 'px';
                dropdown.style.left = 'auto';
                dropdown.style.bottom = 'auto';
            } else {
                dropdown.style.top = '';
                dropdown.style.right = '';
            }

        } else if (!e.target.closest('.adm-dropdown')) {
            closeAllDropdowns();
        }
    });

    window.closeAllDropdowns = function () {
        document.querySelectorAll('.adm-dropdown.open').forEach(d => {
            d.classList.remove('open');
            d.style.top = '';
            d.style.right = '';
            document.querySelector(`[data-dropdown-target="${d.id}"]`)
                ?.setAttribute('aria-expanded', 'false');
        });
    };

    document.addEventListener('scroll', window.closeAllDropdowns, true);

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            window.closeAllDropdowns?.();
            window.closeAllModals?.();
        }
    });
});
