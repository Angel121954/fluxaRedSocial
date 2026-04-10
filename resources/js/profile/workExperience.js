/* ═══════════════════════════════════════════════════════════
   workExperiences.js  —  Lógica del modal de experiencia laboral
   Depende de: SweetAlert2 (cargado en el layout)
═══════════════════════════════════════════════════════════ */

document.addEventListener('DOMContentLoaded', () => {

    // ── Referencias ──────────────────────────────────────────
    const backdrop = document.getElementById('weBackdrop');
    if (!backdrop) return;

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
        if (modalTitle) modalTitle.textContent = 'Nueva experiencia';
        if (modalSubtitle) modalSubtitle.textContent = 'Completa los campos para agregar tu experiencia';
        if (formMethod) formMethod.value = 'POST';
        if (weForm) weForm.action = baseAction;
        if (experienceId) experienceId.value = '';
        openModal();
    });

    // ── Cerrar modal ─────────────────────────────────────────
    document.getElementById('btnCloseModal')?.addEventListener('click', closeModal);
    document.getElementById('btnCancelModal')?.addEventListener('click', closeModal);

    // Cerrar al hacer click en el backdrop (fuera del modal)
    backdrop.addEventListener('click', e => {
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

            const getEl = (id) => document.getElementById(id);
            getEl('input-company').value = d.company ?? '';
            getEl('input-position').value = d.position ?? '';
            getEl('input-location').value = d.location ?? '';
            getEl('input-started_at').value = d.started ?? '';
            getEl('input-ended_at').value = d.ended ?? '';
            getEl('input-description').value = d.description ?? '';

            if (checkCurrent) {
                checkCurrent.checked = d.current === '1';
                toggleEndedAt();
            }

            if (formMethod) formMethod.value = 'PUT';
            if (experienceId) experienceId.value = d.id;
            if (weForm) weForm.action = baseAction.replace(/\/?$/, '') + '/' + d.id;

            if (modalTitle) modalTitle.textContent = 'Editar experiencia';
            if (modalSubtitle) modalSubtitle.textContent = 'Modifica los campos que deseas actualizar';

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
        if (weForm) weForm.reset();
        if (inputEnded) {
            inputEnded.disabled = false;
            inputEnded.closest('.form-group').style.opacity = '1';
        }
        updateCharCount();
    }

    function toggleEndedAt() {
        if (!inputEnded) return;
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