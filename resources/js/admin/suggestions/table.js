/**
 * admin/suggestions/table.js — DataTables para sugerencias
 * Depende de: jQuery, DataTables 1.13.x, Responsive
 */

document.addEventListener('DOMContentLoaded', function () {
    const $table = $('#suggestionsTable');
    if (!$table.length) return;

    const searchInput = document.getElementById('suggestionsSearch');
    const filterStatus = document.getElementById('filterStatus');

    $.fn.dataTable.ext.search.push(function (settings, _data, _dataIndex, row) {
        if (settings.nTable.id !== 'suggestionsTable') return true;

        const $row = $(row);
        const status = filterStatus ? filterStatus.value : '';

        if (status && $row.data('status') !== status) return false;

        return true;
    });

    $table.DataTable({
        responsive: {
            details: {
                type: 'inline',
            },
        },
        pageLength: 15,
        lengthMenu: [10, 15, 25, 50, 100],
        language: {
            emptyTable: 'No hay sugerencias registradas.',
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
        ],
        order: [[4, 'desc']],
    });

    if (searchInput) {
        let timer;
        searchInput.addEventListener('input', function () {
            clearTimeout(timer);
            timer = setTimeout(() => $table.DataTable().search(this.value).draw(), 280);
        });
    }

    if (filterStatus) {
        filterStatus.addEventListener('change', () => $table.DataTable().draw());
    }

    document.getElementById('clearFilters')?.addEventListener('click', function () {
        if (searchInput) { searchInput.value = ''; $table.DataTable().search(''); }
        if (filterStatus) filterStatus.value = '';
        $table.DataTable().draw();
    });
});
