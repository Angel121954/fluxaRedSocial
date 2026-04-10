/**
 * profile/avatar.js — Gestión del avatar de perfil
 * Depende de: SweetAlert2 (cargado en el layout), shared/toast.js
 */

import { showToast } from '../shared/toast.js';

(() => {
    /* ── Elementos ─────────────────────────────────── */
    const avatarWrap = document.getElementById('avatarWrap');
    const avatarImg  = document.getElementById('avatarImg');
    const btnCam     = document.querySelector('.btnCam');
    const btnView    = document.querySelector('.btnView');
    const btnDelete  = document.getElementById('btnDelete');
    const fileIn     = document.getElementById('fileIn');
    const imgModal   = document.getElementById('imgModal');
    const modalImg   = document.getElementById('modalImg');
    const modalX     = document.getElementById('modalX');

    if (!avatarWrap) return; // no estamos en una vista con avatar

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
    const isMobile  = () => window.innerWidth <= 680;

    /* ── Overlay en móvil ──────────────────────────── */
    avatarWrap.addEventListener('click', (e) => {
        if (isMobile() && (e.target === avatarWrap || e.target === avatarImg)) {
            avatarWrap.classList.toggle('active');
        }
    });

    document.addEventListener('click', (e) => {
        if (isMobile() && !avatarWrap.contains(e.target)) {
            avatarWrap.classList.remove('active');
        }
    });

    /* ── Cambiar foto ──────────────────────────────── */
    if (btnCam && fileIn) {
        btnCam.addEventListener('click', (e) => {
            e.stopPropagation();
            avatarWrap.classList.remove('active');
            fileIn.click();
        });

        fileIn.addEventListener('change', async (e) => {
            const file = e.target.files[0];
            if (!file) return;
            setAllAvatars(URL.createObjectURL(file)); // optimistic UI
            await uploadAvatar(file);
            fileIn.value = '';
        });
    }

    /* ── Eliminar foto ─────────────────────────────── */
    if (btnDelete) {
        btnDelete.addEventListener('click', async () => {
            const result = await Swal.fire({
                title: '¿Eliminar foto de perfil?',
                text: 'Esta acción no se puede deshacer.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#12b3b6',
                customClass: {
                    popup: 'swal-fluxa',
                    title: 'swal-fluxa__title',
                    htmlContainer: 'swal-fluxa__text',
                    cancelButton: 'swal-fluxa__cancel',
                    icon: 'swal-fluxa__icon',
                },
                reverseButtons: true,
            });

            if (!result.isConfirmed) return;

            try {
                const username       = btnDelete.dataset.username;
                const DEFAULT_AVATAR = `https://api.dicebear.com/7.x/initials/svg?seed=${username}&backgroundColor=12b3b6`;

                const response = await fetch('/profile/avatar', {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': csrfToken, Accept: 'application/json' },
                });
                const data = await response.json();

                if (!response.ok || !data.success) throw new Error(data.message || 'Error al eliminar la foto');

                setAllAvatars(DEFAULT_AVATAR);
                showToast('Foto eliminada correctamente', 'success');
            } catch (err) {
                console.error('destroyAvatar:', err);
                showToast(err.message ?? 'No se pudo eliminar la foto', 'error');
            }
        });
    }

    /* ── Upload ────────────────────────────────────── */
    async function uploadAvatar(file) {
        setAvatarLoading(true);

        const formData = new FormData();
        formData.append('avatar', file);
        formData.append('_token', csrfToken);

        try {
            const response = await fetch('/profile/avatar', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, Accept: 'application/json' },
                body: formData,
            });
            const data = await response.json();

            if (!response.ok || !data.success) throw new Error(data.message ?? 'Error al subir la imagen');

            setAllAvatars(data.url);
            showToast('Avatar actualizado correctamente', 'success');
        } catch (err) {
            console.error('uploadAvatar:', err);
            showToast(err.message ?? 'No se pudo actualizar el avatar', 'error');
            if (avatarImg?.dataset.originalSrc) setAllAvatars(avatarImg.dataset.originalSrc);
        } finally {
            setAvatarLoading(false);
        }
    }

    /* ── Helpers UI ────────────────────────────────── */
    function setAllAvatars(url) {
        if (avatarImg) avatarImg.src = url;
        if (modalImg)  modalImg.src  = url;
        document.querySelectorAll('.nav-user-av').forEach(img => (img.src = url));
    }

    function setAvatarLoading(loading) {
        avatarWrap.classList.toggle('loading', loading);
        if (btnCam)    btnCam.disabled    = loading;
        if (btnDelete) btnDelete.disabled = loading;
    }

    /* ── Modal ver avatar ──────────────────────────── */
    function closeAvatarModal() {
        imgModal?.classList.remove('show');
        document.body.style.overflow = '';
    }

    if (btnView && imgModal) {
        btnView.addEventListener('click', (e) => {
            e.stopPropagation();
            avatarWrap.classList.remove('active');
            imgModal.classList.add('show');
            document.body.style.overflow = 'hidden';
        });

        imgModal.addEventListener('click', (e) => {
            if (e.target === imgModal) closeAvatarModal();
        });
    }

    modalX?.addEventListener('click', closeAvatarModal);

    // Escape: solo actúa si el modal del avatar está abierto
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && imgModal?.classList.contains('show')) closeAvatarModal();
    });
})();
