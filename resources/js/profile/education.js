/* ═══════════════════════════════════════════════════════════
   education.js  —  Lógica del modal de educación
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
    const educationId = document.getElementById('educationId');
    const checkCurrent = document.getElementById('input-current');
    const inputYear = document.getElementById('input-graduated_year');
    const baseAction = weForm?.getAttribute('action') ?? '';

    // ── Abrir modal vacío (agregar) ──────────────────────────
    document.getElementById('btnOpenModal')?.addEventListener('click', () => {
        resetForm();
        if (modalTitle) modalTitle.textContent = 'Nueva educación';
        if (modalSubtitle) modalSubtitle.textContent = 'Completa los campos para agregar tu formación';
        if (formMethod) formMethod.value = 'POST';
        if (educationId) educationId.value = '';
        if (weForm) weForm.action = baseAction;
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

    // ── Toggle "Estudiando actualmente" ─────────────────────
    checkCurrent?.addEventListener('change', toggleGraduatedYear);

    // ── Botones Editar en tarjetas ───────────────────────────
    document.querySelectorAll('.btnEdit').forEach(btn => {
        btn.addEventListener('click', () => {
            const d = btn.dataset;

            const institutionEl = document.getElementById('input-institution');
            const degreeEl = document.getElementById('input-degree');
            const fieldEl = document.getElementById('input-field');
            const yearEl = document.getElementById('input-graduated_year');

            if (institutionEl) institutionEl.value = d.institution ?? '';
            if (degreeEl) degreeEl.value = d.degree ?? '';
            if (fieldEl) fieldEl.value = d.field ?? '';
            if (yearEl) yearEl.value = d.graduatedYear ?? '';

            if (checkCurrent) {
                checkCurrent.checked = d.current === '1';
                toggleGraduatedYear();
            }

            if (formMethod) formMethod.value = 'PUT';
            if (educationId) educationId.value = d.id;
            if (weForm) weForm.action = baseAction.replace(/\/?$/, '') + '/' + d.id;

            if (modalTitle) modalTitle.textContent = 'Editar educación';
            if (modalSubtitle) modalSubtitle.textContent = 'Modifica los campos que deseas actualizar';

            openModal();
        });
    });

    // ── Confirmar eliminación con SweetAlert2 ────────────────
    document.querySelectorAll('.formDelete').forEach(form => {
        form.addEventListener('submit', e => {
            e.preventDefault();
            const institution = form.dataset.institution ?? '';
            const degree = form.dataset.degree ?? '';

            Swal.fire({
                title: '¿Eliminar educación?',
                text: `Se eliminará "${institution} - ${degree}" de tu formación académica.`,
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
        if (inputYear) {
            inputYear.disabled = false;
            inputYear.closest('.form-group').style.opacity = '1';
        }
    }

    function toggleGraduatedYear() {
        if (!inputYear) return;
        const isCurrent = checkCurrent.checked;
        if (isCurrent) inputYear.value = '';
        inputYear.disabled = isCurrent;
        inputYear.closest('.form-group').style.opacity = isCurrent ? '0.45' : '1';
    }
});