/**
 * admin/reports/index.js — Tabs del panel de reportes
 */
(function () {
    const tabs = document.querySelectorAll('.rp-tab');
    const panels = {};

    document.querySelectorAll('.rp-panel').forEach(p => {
        panels[p.id] = p;
    });

    function activateTab(tabId) {
        tabs.forEach(t => {
            const isActive = t.dataset.tab === tabId;
            t.classList.toggle('rp-tab--active', isActive);
            t.setAttribute('aria-selected', String(isActive));
        });

        Object.entries(panels).forEach(([id, panel]) => {
            const isActive = id === 'tab-' + tabId;
            panel.classList.toggle('rp-panel--active', isActive);
        });
    }

    tabs.forEach(tab => {
        tab.addEventListener('click', function () {
            activateTab(this.dataset.tab);
        });
    });

    /* ── Confirm before dismiss ─────────────────────────── */
    document.addEventListener('click', function (e) {
        const form = e.target.closest('.rp-dismiss-form');
        if (!form) return;

        e.preventDefault();

        Swal.fire({
            title: '¿Descartar reporte?',
            text: 'El reporte se eliminará permanentemente.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, descartar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#ef4444',
        }).then(function (result) {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
})();
