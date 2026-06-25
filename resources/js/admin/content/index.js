/**
 * admin/content/index.js — Tabs + confirmaciones
 */
(function () {
    const tabs = document.querySelectorAll('.ct-tab');
    const panels = {};

    document.querySelectorAll('.ct-panel').forEach(function (p) {
        panels[p.id] = p;
    });

    function activateTab(tabId) {
        tabs.forEach(function (t) {
            var isActive = t.dataset.tab === tabId;
            t.classList.toggle('ct-tab--active', isActive);
            t.setAttribute('aria-selected', String(isActive));
        });

        Object.entries(panels).forEach(function (_ref) {
            var id = _ref[0];
            var panel = _ref[1];
            var isActive = id === 'tab-' + tabId;
            panel.classList.toggle('ct-panel--active', isActive);
        });
    }

    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            activateTab(this.dataset.tab);
        });
    });

    /* ── Delete project ────────────────────────────────────── */
    document.addEventListener('click', function (e) {
        var form = e.target.closest('.ct-delete-form');
        if (!form) return;

        e.preventDefault();

        Swal.fire({
            title: '¿Eliminar contenido?',
            text: 'Esta acción no se puede deshacer.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#ef4444',
        }).then(function (result) {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });

    /* ── Restore project ───────────────────────────────────── */
    document.addEventListener('click', function (e) {
        var form = e.target.closest('.ct-restore-form');
        if (!form) return;

        e.preventDefault();

        Swal.fire({
            title: '¿Restaurar proyecto?',
            text: 'El proyecto volverá a ser visible.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, restaurar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#12b3b6',
        }).then(function (result) {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
})();
