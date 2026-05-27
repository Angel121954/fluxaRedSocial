(function () {
    'use strict';

    var backdrop = document.getElementById('diaryReportModal');
    var closeBtn = document.getElementById('diaryReportClose');
    var cancelBtn = document.getElementById('diaryReportCancel');
    var form = document.getElementById('diaryReportForm');
    var reasonInput = document.getElementById('diaryReportReason');
    var submitBtn = document.getElementById('diaryReportSubmit');

    if (!backdrop) return;

    var currentResponseId = null;

    function openModal(responseId) {
        currentResponseId = responseId;
        reasonInput.value = '';
        submitBtn.disabled = false;
        submitBtn.textContent = 'Reportar';
        backdrop.classList.add('is-open');
        lockBodyScroll();
        setTimeout(function () { reasonInput.focus(); }, 100);
    }

    function closeModal() {
        backdrop.classList.remove('is-open');
        unlockBodyScroll();
    }

    function showError(msg) {
        var existing = backdrop.querySelector('.diary-report-error');
        if (existing) existing.remove();

        var el = document.createElement('p');
        el.className = 'diary-report-error';
        el.textContent = msg;
        reasonInput.parentNode.insertBefore(el, reasonInput.nextSibling);
    }

    function clearError() {
        var el = backdrop.querySelector('.diary-report-error');
        if (el) el.remove();
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        clearError();

        var reason = reasonInput.value.trim();

        if (reason.length < 10) {
            showError('El motivo debe tener al menos 10 caracteres.');
            return;
        }

        var csrf = document.querySelector('meta[name="csrf-token"]')?.content;
        var url = '/diary/' + currentResponseId + '/report';

        submitBtn.disabled = true;
        submitBtn.textContent = 'Enviando...';

        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrf,
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ reason: reason }),
            credentials: 'same-origin',
        })
            .then(function (res) {
                if (!res.ok) throw new Error('Error al enviar reporte');
                return res.json();
            })
            .then(function (data) {
                closeModal();
                window.toast?.success(data.message || 'Reporte enviado. Gracias por ayudar a mantener la comunidad segura.');
            })
            .catch(function () {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Reportar';
                showError('No se pudo enviar el reporte. Intenta de nuevo.');
            });
    });

    if (closeBtn) closeBtn.addEventListener('click', closeModal);
    if (cancelBtn) cancelBtn.addEventListener('click', closeModal);

    backdrop.addEventListener('click', function (e) {
        if (e.target === backdrop) closeModal();
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && backdrop.classList.contains('is-open')) {
            closeModal();
        }
    });

    window.openDiaryReportModal = openModal;

})();
