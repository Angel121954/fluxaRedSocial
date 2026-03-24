/* ═══════════════════════════════════════════════════════════
   education.js  —  Lógica del modal de educación
   Depende de: SweetAlert2 (cargado en el layout)
═══════════════════════════════════════════════════════════ */

document.addEventListener('DOMContentLoaded', () => {

    // ── Referencias ──────────────────────────────────────────
    const backdrop = document.getElementById('weBackdrop');
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
        modalTitle.textContent = 'Nueva educación';
        modalSubtitle.textContent = 'Completa los campos para agregar tu formación';
        formMethod.value = 'POST';
        weForm.action = baseAction;
        educationId.value = '';
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

    // ── Toggle "Estudiando actualmente" ─────────────────────
    checkCurrent?.addEventListener('change', toggleGraduatedYear);

    // ── Botones Editar en tarjetas ───────────────────────────
    document.querySelectorAll('.btnEdit').forEach(btn => {
        btn.addEventListener('click', () => {
            const d = btn.dataset;

            document.getElementById('input-institution').value = d.institution ?? '';
            document.getElementById('input-degree').value = d.degree ?? '';
            document.getElementById('input-field').value = d.field ?? '';
            document.getElementById('input-graduated_year').value = d.graduatedYear ?? '';

            checkCurrent.checked = d.current === '1';
            toggleGraduatedYear();

            formMethod.value = 'PUT';
            educationId.value = d.id;
            weForm.action = baseAction.replace(/\/?$/, '') + '/' + d.id;

            modalTitle.textContent = 'Editar educación';
            modalSubtitle.textContent = 'Modifica los campos que deseas actualizar';

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
        weForm.reset();
        inputYear.disabled = false;
        inputYear.closest('.form-group').style.opacity = '1';
    }

    function toggleGraduatedYear() {
        const isCurrent = checkCurrent.checked;
        if (isCurrent) inputYear.value = '';
        inputYear.disabled = isCurrent;
        inputYear.closest('.form-group').style.opacity = isCurrent ? '0.45' : '1';
    }
});