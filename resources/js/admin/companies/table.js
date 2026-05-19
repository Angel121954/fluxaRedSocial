/**
 * admin/companies/table.js — DataTables para empresas
 * Depende de: jQuery, DataTables 1.13.x, Responsive
 */

document.addEventListener('DOMContentLoaded', function () {
    const $table = $('#companiesTable');
    if (!$table.length) return;

    const searchInput = document.getElementById('companiesSearch');
    const filterStatus = document.getElementById('filterStatus');
    const filterVerified = document.getElementById('filterVerified');

    $.fn.dataTable.ext.search.push(function (settings, _data, _dataIndex, row) {
        if (settings.nTable.id !== 'companiesTable') return true;

        const $row = $(row);
        const status = filterStatus ? filterStatus.value : '';
        const verified = filterVerified ? filterVerified.value : '';

        if (status && $row.data('status') !== status) return false;
        if (verified !== '' && String($row.data('verified')) !== verified) return false;

        return true;
    });

    $table.DataTable({
        responsive: true,
        pageLength: 15,
        lengthMenu: [10, 15, 25, 50, 100],
        language: {
            emptyTable: 'No hay empresas registradas.',
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
        order: [[5, 'desc']],
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

    if (filterVerified) {
        filterVerified.addEventListener('change', () => $table.DataTable().draw());
    }

    document.getElementById('clearFilters')?.addEventListener('click', function () {
        if (searchInput) { searchInput.value = ''; $table.DataTable().search(''); }
        if (filterStatus) filterStatus.value = '';
        if (filterVerified) filterVerified.value = '';
        $table.DataTable().draw();
    });
});
