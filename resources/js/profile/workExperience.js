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

    // ── Referencias a labels dinámicos ──────────────────────
    const TYPE_LABELS = {
        company: {
            formal: { label: 'Empresa', placeholder: 'Ej. Google, Startup XYZ' },
            freelance: { label: 'Cliente o proyecto', placeholder: 'Ej. Cliente de Shopify, App para restaurant' },
            volunteering: { label: 'Organización', placeholder: 'Ej. Cruz Roja, Tech for Good' },
        },
        position: {
            formal: { label: 'Cargo', placeholder: 'Ej. Frontend Developer' },
            freelance: { label: 'Rol / Servicio', placeholder: 'Ej. Desarrollo de landing page' },
            volunteering: { label: 'Rol', placeholder: 'Ej. Mentor, Desarrollador voluntario' },
        },
    };

    function updateLabels(type) {
        const companyGroup = document.getElementById('input-company')?.closest('.form-group');
        const positionGroup = document.getElementById('input-position')?.closest('.form-group');
        const companyInput = document.getElementById('input-company');
        const positionInput = document.getElementById('input-position');

        const companyCfg = TYPE_LABELS.company[type] ?? TYPE_LABELS.company.formal;
        const positionCfg = TYPE_LABELS.position[type] ?? TYPE_LABELS.position.formal;

        if (companyGroup) {
            const label = companyGroup.querySelector('.form-label');
            if (label) label.textContent = companyCfg.label;
        }
        if (companyInput) companyInput.placeholder = companyCfg.placeholder;

        if (positionGroup) {
            const label = positionGroup.querySelector('.form-label');
            if (label) label.textContent = positionCfg.label;
        }
        if (positionInput) positionInput.placeholder = positionCfg.placeholder;
    }

    // ── Cambio de tipo ───────────────────────────────────────
    document.querySelectorAll('.we-type-radio').forEach(radio => {
        radio.addEventListener('change', () => {
            if (radio.checked) {
                document.querySelectorAll('.we-type-option').forEach(opt => opt.classList.remove('we-type-option--selected'));
                radio.closest('.we-type-option').classList.add('we-type-option--selected');
                updateLabels(radio.value);
            }
        });
    });

    // ── Abrir modal vacío (agregar) ──────────────────────────
    document.getElementById('btnOpenModal')?.addEventListener('click', () => {
        resetForm();
        if (modalTitle) modalTitle.textContent = 'Nueva experiencia';
        if (modalSubtitle) modalSubtitle.textContent = 'Completa los campos para agregar tu experiencia';
        if (formMethod) formMethod.value = 'POST';
        if (weForm) weForm.action = baseAction;
        if (experienceId) experienceId.value = '';
        updateLabels('formal');
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
            const type = d.type ?? 'formal';

            const getEl = (id) => document.getElementById(id);
            getEl('input-company').value = d.company ?? '';
            getEl('input-position').value = d.position ?? '';
            getEl('input-location').value = d.location ?? '';
            getEl('input-started_at').value = d.started ?? '';
            getEl('input-ended_at').value = d.ended ?? '';
            getEl('input-description').value = d.description ?? '';

            // Seleccionar el tipo correcto
            const radio = document.querySelector(`.we-type-radio[value="${type}"]`);
            if (radio) {
                radio.checked = true;
                document.querySelectorAll('.we-type-option').forEach(opt => opt.classList.remove('we-type-option--selected'));
                radio.closest('.we-type-option').classList.add('we-type-option--selected');
                updateLabels(type);
            }

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
        lockBodyScroll();
    }

    function closeModal() {
        backdrop.classList.remove('is-open');
        unlockBodyScroll();
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