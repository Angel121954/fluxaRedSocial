document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('reportProblemModal');
    if (!modal) return;

    const form = document.getElementById('reportProblemForm');
    const submitBtn = document.getElementById('reportProblemSubmit');
    const errorBanner = document.getElementById('reportProblemError');
    const errorText = document.getElementById('reportProblemErrorText');
    const textarea = document.getElementById('reportProblemMessage');
    const typeSelect = document.getElementById('reportProblemType');

    function closeModal() {
        modal.classList.remove('show', 'is-open');
        unlockBodyScroll();
        textarea.value = '';
        typeSelect.value = '';
        errorBanner.style.display = 'none';
    }

    function openModal() {
        const helpMenu = document.getElementById('helpDropdownMenu');
        if (helpMenu?.classList.contains('active')) {
            helpMenu.classList.remove('active');
            document.getElementById('helpDropdownBtn')?.setAttribute('aria-expanded', 'false');
        }

        const mobileMenu = document.getElementById('mobileMenu');
        if (mobileMenu?.classList.contains('active')) {
            mobileMenu.classList.remove('active');
            document.getElementById('mobileMenuBtn')?.setAttribute('aria-expanded', 'false');
        }

        modal.classList.add('show');
        lockBodyScroll();
        textarea.value = '';
        typeSelect.value = '';
        errorBanner.style.display = 'none';
        setTimeout(() => typeSelect.focus(), 150);
    }

    window.abrirReportProblemModal = openModal;

    modal.addEventListener('click', function (e) {
        if (e.target === modal) closeModal();
    });

    document.querySelectorAll('[data-close="reportProblemModal"]').forEach(el => {
        el.addEventListener('click', closeModal);
    });

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const type = typeSelect.value;
        const message = textarea.value.trim();

        if (!type) {
            errorText.textContent = 'Selecciona el tipo de problema.';
            errorBanner.style.display = 'flex';
            typeSelect.focus();
            return;
        }

        if (message.length < 10) {
            errorText.textContent = 'El mensaje debe tener al menos 10 caracteres.';
            errorBanner.style.display = 'flex';
            textarea.focus();
            return;
        }

        submitBtn.disabled = true;
        submitBtn.textContent = 'Enviando...';
        errorBanner.style.display = 'none';

        fetch('/report-problem', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ type, message }),
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    closeModal();
                    window.toast?.success('Reporte enviado correctamente. ¡Gracias!');
                } else {
                    throw new Error('Error al enviar');
                }
            })
            .catch(() => {
                errorText.textContent = 'Ocurrió un error al enviar tu reporte. Intenta de nuevo.';
                errorBanner.style.display = 'flex';
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Enviar reporte';
            });
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && modal.classList.contains('show')) {
            closeModal();
        }
    });
});
