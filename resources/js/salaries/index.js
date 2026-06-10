(function () {
    'use strict';

    function escapeHtml(str) {
        var map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return String(str ?? '').replace(/[&<>"']/g, function(c) { return map[c]; });
    }

    /* ─── Modal ──────────────────────────────────── */
    var backdrop = document.getElementById('salaryModal');
    var closeBtn = document.getElementById('salaryModalClose');
    var cancelBtn = document.getElementById('salaryModalCancel');
    var submitBtn = document.getElementById('salaryModalSubmit');
    var form = document.getElementById('salaryForm');

    function openModal() {
        if (!backdrop) return;
        backdrop.classList.add('is-open');
        lockBodyScroll();
    }

    function closeModal() {
        if (!backdrop) return;
        backdrop.classList.remove('is-open');
        unlockBodyScroll();
    }

    if (closeBtn) closeBtn.addEventListener('click', closeModal);
    if (cancelBtn) cancelBtn.addEventListener('click', closeModal);
    if (backdrop) {
        backdrop.addEventListener('click', function (e) {
            if (e.target === backdrop) closeModal();
        });
    }
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && backdrop && backdrop.classList.contains('is-open')) {
            closeModal();
        }
    });

    window.openSalaryModal = openModal;

    /* ─── Submit ──────────────────────────────────── */
    function serializeForm(formEl) {
        var data = {};
        var fd = new FormData(formEl);
        fd.forEach(function (value, key) {
            if (key === 'technologies[]') {
                if (!data.technologies) data.technologies = [];
                data.technologies.push(value);
            } else {
                data[key] = value;
            }
        });
        return data;
    }

    if (submitBtn && form) {
        submitBtn.addEventListener('click', function () {
            var data = serializeForm(form);
            var csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            submitBtn.disabled = true;
            submitBtn.textContent = 'Enviando...';

            fetch('/salaries', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json',
                },
                body: JSON.stringify(data),
            })
            .then(function (res) {
                if (!res.ok) throw new Error('Error');
                return res.json();
            })
            .then(function () {
                closeModal();
                if (typeof window.showToast === 'function') {
                    window.showToast('Sueldo reportado anónimamente. ¡Gracias!', 'success');
                }
                setTimeout(function () { window.location.reload(); }, 500);
            })
            .catch(function () {
                if (typeof window.showToast === 'function') {
                    window.showToast('No se pudo enviar. Revisa los campos.', 'error');
                }
            })
            .finally(function () {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Enviar anónimamente';
            });
        });
    }

    /* ─── Filters ─────────────────────────────────── */
    var filterCountry = document.getElementById('filterCountry');
    var filterSeniority = document.getElementById('filterSeniority');
    var filterModality = document.getElementById('filterModality');
    var filterTechnology = document.getElementById('filterTechnology');

    function applyFilters() {
        var params = new URLSearchParams();
        if (filterCountry && filterCountry.value) params.set('country', filterCountry.value);
        if (filterSeniority && filterSeniority.value) params.set('seniority', filterSeniority.value);
        if (filterModality && filterModality.value) params.set('modality', filterModality.value);
        if (filterTechnology && filterTechnology.value) params.set('technology_id', filterTechnology.value);

        var url = '/salaries/data?' + params.toString();

        fetch(url, {
            headers: { 'Accept': 'application/json' },
        })
        .then(function (res) { return res.json(); })
        .then(function (json) {
            var container = document.getElementById('salaryReportsList');
            if (!container) return;
            container.innerHTML = '';
            if (json.data && json.data.length) {
                json.data.forEach(function (r) {
                    var techHtml = (r.technologies || []).map(function (t) {
                        return '<span class="salary-report-tag">' + escapeHtml(t) + '</span>';
                    }).join('');
                    container.innerHTML +=
                        '<div class="salary-report-item">' +
                            '<div class="salary-report-top">' +
                                '<span class="salary-report-seniority badge-' + r.seniority + '">' + escapeHtml(r.seniority.charAt(0).toUpperCase() + r.seniority.slice(1)) + '</span>' +
                                '<span class="salary-report-amount">$' + Number(r.salary_usd).toLocaleString('en-US') + '</span>' +
                            '</div>' +
                            '<div class="salary-report-meta">' +
                                '<span>' + escapeHtml(r.country) + (r.city ? ' · ' + escapeHtml(r.city) : '') + '</span>' +
                                '<span> · </span>' +
                                '<span>' + escapeHtml(r.experience_years) + ' años</span>' +
                                '<span> · </span>' +
                                '<span>' + escapeHtml(r.modality.charAt(0).toUpperCase() + r.modality.slice(1)) + '</span>' +
                            '</div>' +
                            '<div class="salary-report-techs">' + techHtml + '</div>' +
                            '<span class="salary-report-date">' + escapeHtml(r.created_at || '') + '</span>' +
                        '</div>';
                });
            } else {
                container.innerHTML = '<p class="salary-empty">No hay reportes con esos filtros.</p>';
            }
        });
    }

    [filterCountry, filterSeniority, filterModality, filterTechnology].forEach(function (el) {
        if (el) el.addEventListener('change', applyFilters);
    });

})();
