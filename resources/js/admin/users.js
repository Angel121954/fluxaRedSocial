/**
 * admin/users.js — Vista de Usuarios (Admin)
 * Depende de: jQuery, DataTables 1.13.x, Responsive
 * @vite('resources/js/admin/users.js')
 */

document.addEventListener('DOMContentLoaded', function () {

    /* ══════════════════════════════════════════════════════════
       1. DataTables
    ══════════════════════════════════════════════════════════ */
    const $table = $('#usersTable');

    if (!$table.length) return;

    const dt = $table.DataTable({
        responsive: true,
        pageLength: 15,
        lengthMenu: [10, 15, 25, 50, 100],
        language: {
            emptyTable: 'No hay usuarios registrados.',
            zeroRecords: 'No se encontraron resultados.',
            loadingRecords: 'Cargando...',
            processing: 'Procesando...',
            paginate: {
                first: '«',
                last: '»',
                next: '›',
                previous: '‹',
            },
        },
        columnDefs: [
            // Columna "Acciones" no ordenable
            { orderable: false, targets: -1 },
            // Columna "Verificado" — ordenar por data-verified del <tr>
            { orderable: true, targets: 4 },
        ],
        order: [[6, 'desc']], // Ordenar por fecha de registro desc por defecto
        drawCallback: function () {
            updateTableInfo(this.api());
        },
        initComplete: function () {
            updateTableInfo(this.api());
        },
    });

    /* ── Buscador custom (reemplaza el nativo de DataTables) ─── */
    const searchInput = document.getElementById('usersSearch');
    if (searchInput) {
        searchInput.addEventListener('input', debounce(function () {
            dt.search(this.value).draw();
        }, 280));
    }

    /* ── Filtro: Rol ─────────────────────────────────────────── */
    const filterRole = document.getElementById('filterRole');
    if (filterRole) {
        filterRole.addEventListener('change', function () {
            filterByDataAttr();
        });
    }

    /* ── Filtro: Estado ──────────────────────────────────────── */
    const filterStatus = document.getElementById('filterStatus');
    if (filterStatus) {
        filterStatus.addEventListener('change', function () {
            filterByDataAttr();
        });
    }

    /* ── Filtro: Verificado ──────────────────────────────────── */
    const filterVerified = document.getElementById('filterVerified');
    if (filterVerified) {
        filterVerified.addEventListener('change', function () {
            filterByDataAttr();
        });
    }

    /**
     * Filtra las filas según los data-atributos del <tr>.
     * DataTables no sabe de estos atributos, así que usamos
     * $.fn.dataTable.ext.search para un filtro personalizado.
     */
    $.fn.dataTable.ext.search.push(function (settings, _data, _dataIndex, row) {
        if (settings.nTable.id !== 'usersTable') return true;

        const $row = $(row);
        const role = filterRole ? filterRole.value : '';
        const status = filterStatus ? filterStatus.value : '';
        const verified = filterVerified ? filterVerified.value : '';

        if (role && $row.data('role') !== role) return false;
        if (status && $row.data('status') !== status) return false;
        if (verified !== '' && String($row.data('verified')) !== verified) return false;

        return true;
    });

    /* ── Limpiar filtros ─────────────────────────────────────── */
    const clearBtn = document.getElementById('clearFilters');
    if (clearBtn) {
        clearBtn.addEventListener('click', function () {
            if (searchInput) { searchInput.value = ''; dt.search(''); }
            if (filterRole) filterRole.value = '';
            if (filterStatus) filterStatus.value = '';
            if (filterVerified) filterVerified.value = '';
            dt.draw();
        });
    }

    /* ── Info de registros custom ────────────────────────────── */
    function updateTableInfo(api) {
        const info = api.page.info();
        let infoEl = document.getElementById('dtInfo');

        if (!infoEl) {
            infoEl = document.createElement('div');
            infoEl.id = 'dtInfo';
            infoEl.className = 'adm-dt-info';
            const wrapper = document.querySelector('.dataTables_wrapper');
            if (wrapper) wrapper.appendChild(infoEl);
        }

        const from = info.recordsDisplay > 0 ? info.start + 1 : 0;
        const to = info.end;
        const total = info.recordsDisplay;
        const all = info.recordsTotal;

        infoEl.innerHTML = `
            <span>Mostrando <strong>${from}–${to}</strong> de <strong>${total}</strong> usuario${total !== 1 ? 's' : ''}
            ${total !== all ? ` (filtrado de ${all} total)` : ''}</span>
            <div style="display:flex;align-items:center;gap:8px;">
                <label for="dtPageLength" style="font-size:12px;color:var(--ink-400);">Filas:</label>
                <select id="dtPageLength" style="height:28px;padding:0 24px 0 8px;border:1px solid var(--border-strong);border-radius:var(--r-md);font-size:12px;font-family:inherit;background:var(--surface);color:var(--ink-600);outline:none;appearance:none;cursor:pointer;">
                    <option value="10"  ${info.length === 10 ? 'selected' : ''}>10</option>
                    <option value="15"  ${info.length === 15 ? 'selected' : ''}>15</option>
                    <option value="25"  ${info.length === 25 ? 'selected' : ''}>25</option>
                    <option value="50"  ${info.length === 50 ? 'selected' : ''}>50</option>
                    <option value="100" ${info.length === 100 ? 'selected' : ''}>100</option>
                </select>
            </div>
        `;

        document.getElementById('dtPageLength')?.addEventListener('change', function () {
            dt.page.len(parseInt(this.value)).draw();
        });
    }

    /* ── Exportar CSV ────────────────────────────────────────── */
    document.getElementById('exportCsv')?.addEventListener('click', function () {
        exportTableToCSV(dt, 'fluxa-usuarios.csv');
    });

    /* ══════════════════════════════════════════════════════════
       2. Dropdown de acciones (delegado al document)
    ══════════════════════════════════════════════════════════ */
    document.addEventListener('click', function (e) {
        const toggleBtn = e.target.closest('[data-dropdown-target]');

        if (toggleBtn) {
            e.stopPropagation();
            const targetId = toggleBtn.dataset.dropdownTarget;
            const dropdown = document.getElementById(targetId);
            if (!dropdown) return;

            // Cerrar todos los demás
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
            // Click fuera → cerrar todos
            closeAllDropdowns();
        }
    });

    function closeAllDropdowns() {
        document.querySelectorAll('.adm-dropdown.open').forEach(d => {
            d.classList.remove('open');
            d.style.top = '';
            d.style.right = '';
            document.querySelector(`[data-dropdown-target="${d.id}"]`)
                ?.setAttribute('aria-expanded', 'false');
        });
    }

    // Cerrar dropdowns al hacer scroll para evitar posiciones desactualizadas
    document.addEventListener('scroll', closeAllDropdowns, true);

    // Cerrar dropdowns con Escape
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeAllDropdowns();
            closeAllModals();
        }
    });

    /* ══════════════════════════════════════════════════════════
       3. Modal — Otorgar insignia Beta Tester
    ══════════════════════════════════════════════════════════ */
    const badgeBackdrop = document.getElementById('badgeModalBackdrop');
    const badgeSearch = document.getElementById('badgeUserSearch');
    const badgeList = document.getElementById('badgeUserList');
    const submitBadge = document.getElementById('submitBadge');
    const submitBadgeText = document.getElementById('submitBadgeText');
    const badgeCheckboxes = badgeList?.querySelectorAll('.adm-badge-checkbox:not(:disabled)');

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

    /* Abrir modal (botón principal del header) */
    document.getElementById('openBadgeModal')?.addEventListener('click', function () {
        openBadgeModal();
    });

    /* Abrir modal desde dropdown de usuario específico */
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-grant-badge');
        if (!btn) return;

        const userId = btn.dataset.userId;
        openBadgeModal(userId);
    });

    function openBadgeModal(preselectedUserId) {
        closeAllModals();

        // Resetear todos los checkboxes
        badgeCheckboxes?.forEach(cb => { cb.checked = false; });
        updateBadgeCount();

        // Si viene con usuario preseleccionado, marcarlo
        if (preselectedUserId) {
            const cb = badgeList?.querySelector(`.adm-badge-checkbox:not(:disabled)[value="${preselectedUserId}"]`);
            if (cb) cb.checked = true;
            updateBadgeCount();
        }

        if (badgeSearch) {
            badgeSearch.value = '';
            filterBadgeItems('');
        }
        badgeBackdrop?.classList.add('is-open');
        badgeSearch?.focus();
    }

    /* Filtrado de la lista por búsqueda */
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

    /* Cerrar modal badge */
    document.getElementById('closeBadgeModal')?.addEventListener('click', closeBadgeModal);
    document.getElementById('cancelBadgeModal')?.addEventListener('click', closeBadgeModal);
    badgeBackdrop?.addEventListener('click', function (e) {
        if (e.target === badgeBackdrop) closeBadgeModal();
    });

    function closeBadgeModal() {
        badgeBackdrop?.classList.remove('is-open');
        if (badgeSearch) badgeSearch.value = '';
        filterBadgeItems('');
    }

    /* ══════════════════════════════════════════════════════════
       4. Modal — Confirmar baneo
    ══════════════════════════════════════════════════════════ */
    const banBackdrop = document.getElementById('banModalBackdrop');
    const banUserName = document.getElementById('banUserName');
    const banReason = document.getElementById('banReason');
    const confirmBanBtn = document.getElementById('confirmBanBtn');

    let activeBanUserId = null;

    /* Abrir modal de baneo desde dropdown */
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-ban');
        if (!btn) return;

        activeBanUserId = btn.dataset.userId;
        const name = btn.dataset.userName || 'este usuario';

        if (banUserName) banUserName.textContent = name;
        if (banReason) banReason.value = '';

        closeAllModals();
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

    /* Confirmar desbaneo desde dropdown (sin modal — acción directa) */
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-unban');
        if (!btn) return;

        const userId = btn.dataset.userId;
        const name = btn.dataset.userName || 'este usuario';
        const form = document.getElementById(`unban-form-${userId}`);

        if (!form) return;

        // Confirmación simple inline (sin SweetAlert2)
        const confirmed = window.confirm(`¿Deseas desbanear a ${name}? Recuperará acceso completo a la plataforma.`);
        if (confirmed) form.submit();
    });

    /* Cerrar modal baneo */
    document.getElementById('closeBanModal')?.addEventListener('click', closeBanModal);
    document.getElementById('cancelBanModal')?.addEventListener('click', closeBanModal);
    banBackdrop?.addEventListener('click', function (e) {
        if (e.target === banBackdrop) closeBanModal();
    });

    function closeBanModal() {
        banBackdrop?.classList.remove('is-open');
        activeBanUserId = null;
    }

    /* ── Helper: cerrar todos los modales ────────────────────── */
    function closeAllModals() {
        closeBadgeModal();
        closeBanModal();
    }

    /* ══════════════════════════════════════════════════════════
       5. Utilidades
    ══════════════════════════════════════════════════════════ */

    /**
     * Debounce: evita disparar la búsqueda en cada pulsación.
     */
    function debounce(fn, delay) {
        let timer;
        return function (...args) {
            clearTimeout(timer);
            timer = setTimeout(() => fn.apply(this, args), delay);
        };
    }

    /**
     * Exporta la tabla actual (con los filtros activos) a CSV.
     */
    function exportTableToCSV(api, filename) {
        const headers = [];
        const rows = [];

        // Cabeceras (excluir última columna "Acciones")
        api.columns(':not(:last-child)').header().each(function () {
            headers.push(`"${this.textContent.trim()}"`);
        });

        // Filas visibles
        api.rows({ search: 'applied' }).every(function () {
            const $row = $(this.node());
            const cells = [];

            $row.find('td:not(:last-child)').each(function () {
                // Limpiar texto (quitar HTML extra)
                let text = this.innerText.trim().replace(/\s+/g, ' ');
                cells.push(`"${text.replace(/"/g, '""')}"`);
            });

            rows.push(cells.join(','));
        });

        const csvContent = [headers.join(','), ...rows].join('\n');
        const blob = new Blob(['\uFEFF' + csvContent], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');

        link.href = url;
        link.download = filename;
        link.style.display = 'none';

        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
    }
});