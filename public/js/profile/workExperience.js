/* ═══════════════════════════════════════════════════════════
   workExperiences.js  —  Lógica del modal de experiencia laboral
   Depende de: SweetAlert2 (cargado en el layout)
═══════════════════════════════════════════════════════════ */

document.addEventListener('DOMContentLoaded', () => {

    // ── Referencias ──────────────────────────────────────────
    const backdrop = document.getElementById('weBackdrop');
    const modalTitle = document.getElementById('weModalTitle');
    const modalSubtitle = backdrop.querySelector('.we-modal__subtitle');
    const weForm = document.getElementById('weForm');
    const formMethod = document.getElementById('formMethod');
    const experienceId = document.getElementById('experienceId');
    const textarea = document.getElementById('input-description');
    const charCount = document.getElementById('charCountDesc');
    const checkCurrent = document.getElementById('input-current');
    const inputEnded = document.getElementById('input-ended_at');
    const baseAction = weForm?.getAttribute('action') ?? '';

    // ── Abrir modal vacío (agregar) ──────────────────────────
    document.getElementById('btnOpenModal')?.addEventListener('click', () => {
        resetForm();
        modalTitle.textContent = 'Nueva experiencia';
        modalSubtitle.textContent = 'Completa los campos para agregar tu experiencia';
        formMethod.value = 'POST';
        weForm.action = baseAction;
        experienceId.value = '';
        openModal();
    });

    // ── Cerrar modal ─────────────────────────────────────────
    document.getElementById('btnCloseModal')?.addEventListener('click', closeModal);
    document.getElementById('btnCancelModal')?.addEventListener('click', closeModal);

    // Cerrar al hacer click en el backdrop (fuera del modal)
    backdrop?.addEventListener('click', e => {
        if (e.target === backdrop) closeModal();
    });

    // Cerrar con Escape
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape' && backdrop.classList.contains('is-open')) closeModal();
    });

    // ── Toggle "Trabajo actual" ──────────────────────────────
    checkCurrent?.addEventListener('change', toggleEndedAt);

    // ── Contador de caracteres ───────────────────────────────
    textarea?.addEventListener('input', updateCharCount);

    // ── Botones Editar en tarjetas ───────────────────────────
    document.querySelectorAll('.btnEdit').forEach(btn => {
        btn.addEventListener('click', () => {
            const d = btn.dataset;

            document.getElementById('input-company').value = d.company ?? '';
            document.getElementById('input-position').value = d.position ?? '';
            document.getElementById('input-location').value = d.location ?? '';
            document.getElementById('input-started_at').value = d.started ?? '';
            document.getElementById('input-ended_at').value = d.ended ?? '';
            document.getElementById('input-description').value = d.description ?? '';

            checkCurrent.checked = d.current === '1';
            toggleEndedAt();

            formMethod.value = 'PUT';
            experienceId.value = d.id;
            weForm.action = baseAction.replace(/\/?$/, '') + '/' + d.id;

            modalTitle.textContent = 'Editar experiencia';
            modalSubtitle.textContent = 'Modifica los campos que deseas actualizar';

            updateCharCount();
            openModal();
        });
    });

    // ── Confirmar eliminación con SweetAlert2 ────────────────
    document.querySelectorAll('.formDelete').forEach(form => {
        form.addEventListener('submit', e => {
            e.preventDefault();
            const company = form.dataset.company ?? '';
            const position = form.dataset.position ?? '';

            Swal.fire({
                title: '¿Eliminar experiencia?',
                text: `Se eliminará "${company} - ${position}" como tu experiencia laboral.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
            }).then(result => {
                if (result.isConfirmed) form.submit();
            });
        });
    });

    // ── Helpers ───────────────────────────────────────────────
    function openModal() {
        backdrop.classList.add('is-open');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        backdrop.classList.remove('is-open');
        document.body.style.overflow = '';
    }

    function resetForm() {
        weForm.reset();
        inputEnded.disabled = false;
        inputEnded.closest('.form-group').style.opacity = '1';
        updateCharCount();
    }

    function toggleEndedAt() {
        const isCurrent = checkCurrent.checked;
        if (isCurrent) inputEnded.value = '';
        inputEnded.disabled = isCurrent;
        inputEnded.closest('.form-group').style.opacity = isCurrent ? '0.45' : '1';
    }

    function updateCharCount() {
        if (!textarea || !charCount) return;
        const len = textarea.value.length;
        charCount.textContent = `${len}/1000`;
        charCount.style.color = len > 900 ? '#dc2626' : 'var(--ink-300)';
    }
});