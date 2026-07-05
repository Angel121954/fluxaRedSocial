(function () {
    'use strict';

    const backdrop = document.getElementById('diaryReportModal');
    const form = document.getElementById('diaryReportForm');
    const reasonInput = document.getElementById('diaryReportReason');
    const submitBtn = document.getElementById('diaryReportSubmit');

    if (!backdrop) return;

    let currentResponseId = null;

    function openReportModal(responseId) {
        currentResponseId = responseId;
        reasonInput.value = '';
        submitBtn.disabled = false;
        submitBtn.textContent = 'Reportar';
        window.openModal('diaryReportModal');
        setTimeout(function () { reasonInput.focus(); }, 100);
    }

    function closeReportModal() {
        window.closeModal('diaryReportModal');
    }

    function showError(msg) {
        const existing = backdrop.querySelector('.diary-report-error');
        if (existing) existing.remove();

        const el = document.createElement('p');
        el.className = 'diary-report-error';
        el.textContent = msg;
        reasonInput.parentNode.insertBefore(el, reasonInput.nextSibling);
    }

    function clearError() {
        const el = backdrop.querySelector('.diary-report-error');
        if (el) el.remove();
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        clearError();

        const reason = reasonInput.value.trim();

        if (reason.length < 10) {
            showError('El motivo debe tener al menos 10 caracteres.');
            return;
        }

        const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
        const url = '/diary/' + currentResponseId + '/report';

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
                closeReportModal();
                window.toast?.success(data.message || 'Reporte enviado. Gracias por ayudar a mantener la comunidad segura.');
            })
            .catch(function () {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Reportar';
                showError('No se pudo enviar el reporte. Intenta de nuevo.');
            });
    });

    window.openDiaryReportModal = openReportModal;
})();
