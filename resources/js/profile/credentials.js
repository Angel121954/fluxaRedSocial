/* ═══════════════════════════════════════════════════════════
   credentials.js  —  Modal de certificaciones
   Depende de: SweetAlert2 (cargado en el layout)
═══════════════════════════════════════════════════════════ */

document.addEventListener('DOMContentLoaded', () => {

    const backdrop = document.getElementById('credBackdrop');
    if (!backdrop) return;

    const form = document.getElementById('credForm');
    const formMethod = document.getElementById('credFormMethod');
    const modalTitle = document.getElementById('credModalTitle');
    const modalSubtitle = backdrop.querySelector('.we-modal__subtitle');
    const textarea = document.getElementById('input-description');
    const charCount = document.getElementById('charCountDesc');
    const baseAction = form?.getAttribute('action') ?? '';

    document.getElementById('btnOpenModal')?.addEventListener('click', () => {
        resetForm();
        if (modalTitle) modalTitle.textContent = 'Nueva certificación';
        if (modalSubtitle) modalSubtitle.textContent = 'Completa los datos de tu certificación';
        if (formMethod) formMethod.value = 'POST';
        if (form) form.action = baseAction;
        openModal();
    });

    document.getElementById('btnCloseModal')?.addEventListener('click', closeModal);
    document.getElementById('btnCancelModal')?.addEventListener('click', closeModal);

    backdrop.addEventListener('click', event => {
        if (event.target === backdrop) closeModal();
    });

    document.addEventListener('keydown', event => {
        if (event.key === 'Escape' && backdrop.classList.contains('is-open')) closeModal();
    });

    textarea?.addEventListener('input', updateCharCount);

    document.querySelectorAll('.btnEdit').forEach(button => {
        button.addEventListener('click', () => {
            const data = button.dataset;
            setValue('input-title', data.title);
            setValue('input-issuer', data.issuer);
            setValue('input-type', data.type || 'certification');
            setValue('input-privacy', data.privacy || 'public');
            setValue('input-issued_at', data.issued);
            setValue('input-expires_at', data.expires);
            setValue('input-credential_id', data.credentialId);
            setValue('input-credential_url', data.credentialUrl);
            setValue('input-description', data.description);

            if (formMethod) formMethod.value = 'PUT';
            if (form) form.action = baseAction.replace(/\/?$/, '') + '/' + data.id;

            if (modalTitle) modalTitle.textContent = 'Editar certificación';
            if (modalSubtitle) modalSubtitle.textContent = 'Modifica los datos que deseas actualizar';

            updateCharCount();
            openModal();
        });
    });

    document.querySelectorAll('.formDelete').forEach(deleteForm => {
        deleteForm.addEventListener('submit', event => {
            event.preventDefault();
            const title = deleteForm.dataset.title ?? '';

            Swal.fire({
                title: '¿Eliminar certificación?',
                text: `Se eliminará "${title}" de tu perfil profesional.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
            }).then(result => {
                if (result.isConfirmed) deleteForm.submit();
            });
        });
    });

    function setValue(id, value) {
        const element = document.getElementById(id);
        if (element) element.value = value ?? '';
    }

    function openModal() {
        backdrop.classList.add('is-open');
        lockBodyScroll();
    }

    function closeModal() {
        backdrop.classList.remove('is-open');
        unlockBodyScroll();
    }

    function resetForm() {
        if (!form) return;
        form.reset();
        setValue('input-type', 'certification');
        setValue('input-privacy', 'public');
        updateCharCount();
    }

    function updateCharCount() {
        if (!textarea || !charCount) return;
        const length = textarea.value.length;
        charCount.textContent = `${length}/500`;
        charCount.style.color = length > 450 ? '#dc2626' : 'var(--ink-300)';
    }
});
