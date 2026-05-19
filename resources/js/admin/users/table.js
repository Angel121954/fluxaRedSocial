/**
 * admin/users/table.js — DataTables + filtros + export CSV
 * Depende de: jQuery, DataTables 1.13.x, Responsive
 */

document.addEventListener('DOMContentLoaded', function () {
    const $table = $('#usersTable');
    if (!$table.length) return;

    const searchInput = document.getElementById('usersSearch');
    const filterRole = document.getElementById('filterRole');
    const filterStatus = document.getElementById('filterStatus');
    const filterVerified = document.getElementById('filterVerified');

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

    const dt = $table.DataTable({
        responsive: {
            details: {
                type: 'inline',
            },
        },
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
            { orderable: false, targets: -1 },
            { responsivePriority: 1, targets: -1 },
            { orderable: true, targets: 4 },
        ],
        order: [[6, 'desc']],
        drawCallback: function () {
            updateTableInfo(this.api());
        },
        initComplete: function () {
            updateTableInfo(this.api());
        },
    });

    if (searchInput) {
        searchInput.addEventListener('input', debounce(function () {
            dt.search(this.value).draw();
        }, 280));
    }

    if (filterRole) {
        filterRole.addEventListener('change', function () {
            filterByDataAttr();
        });
    }

    if (filterStatus) {
        filterStatus.addEventListener('change', function () {
            filterByDataAttr();
        });
    }

    if (filterVerified) {
        filterVerified.addEventListener('change', function () {
            filterByDataAttr();
        });
    }

    function filterByDataAttr() {
        dt.draw();
    }

    document.getElementById('clearFilters')?.addEventListener('click', function () {
        if (searchInput) { searchInput.value = ''; dt.search(''); }
        if (filterRole) filterRole.value = '';
        if (filterStatus) filterStatus.value = '';
        if (filterVerified) filterVerified.value = '';
        dt.draw();
    });

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

    document.getElementById('exportCsv')?.addEventListener('click', function () {
        exportTableToCSV(dt, 'fluxa-usuarios.csv');
    });

    function exportTableToCSV(api, filename) {
        const headers = [];
        const rows = [];

        api.columns(':not(:last-child)').header().each(function () {
            headers.push(`"${this.textContent.trim()}"`);
        });

        api.rows({ search: 'applied' }).every(function () {
            const $row = $(this.node());
            const cells = [];

            $row.find('td:not(:last-child)').each(function () {
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

    function debounce(fn, delay) {
        let timer;
        return function (...args) {
            clearTimeout(timer);
            timer = setTimeout(() => fn.apply(this, args), delay);
        };
    }
});
