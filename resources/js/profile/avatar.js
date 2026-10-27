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

    if (!avatarWrap) return; // no estamos en una vista con avatar

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
    const isMobile  = () => window.innerWidth <= 680;

    /* ── En móvil, un tap sobre el avatar abre la foto completa ── */
    avatarWrap.addEventListener('click', (e) => {
        if (!isMobile()) return;
        // Los botones del overlay y los elementos con su propio manejador (btnView) no abren aquí
        if (e.target.closest('.av-btn') || e.target.closest('.btnView')) return;
        if (imgModal) {
            window.openModal('imgModal');
        }
    });

    /* ── Cambiar foto ──────────────────────────────── */
    if (btnCam && fileIn) {
        btnCam.addEventListener('click', (e) => {
            e.stopPropagation();
            fileIn.click();
        });

        fileIn.addEventListener('change', async (e) => {
            const file = e.target.files[0];
            if (!file) return;

            const previousUrl = avatarImg?.src;
            const previewUrl = URL.createObjectURL(file);

            setAllAvatars(previewUrl); // optimistic UI

            try {
                await uploadAvatar(file, previousUrl);
            } finally {
                URL.revokeObjectURL(previewUrl);
                fileIn.value = '';
            }
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
                cancelButtonColor: '#0b8386',
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
                    credentials: 'same-origin',
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
    async function compressImage(file, maxDimension = 1200, quality = 0.85) {
        if (!file.type.startsWith('image/') || file.type === 'image/gif') {
            return file;
        }

        try {
            const bitmap = await createImageBitmap(file);
            const scale = Math.min(1, maxDimension / Math.max(bitmap.width, bitmap.height));
            const width = Math.round(bitmap.width * scale);
            const height = Math.round(bitmap.height * scale);

            const canvas = document.createElement('canvas');
            canvas.width = width;
            canvas.height = height;

            const ctx = canvas.getContext('2d');
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, width, height);
            ctx.drawImage(bitmap, 0, 0, width, height);
            bitmap.close?.();

            const blob = await new Promise((resolve) => canvas.toBlob(resolve, 'image/jpeg', quality));
            if (!blob) return file;

            return new File([blob], 'avatar.jpg', { type: 'image/jpeg' });
        } catch (err) {
            console.warn('No se pudo optimizar la imagen, se sube la original:', err);
            return file;
        }
    }

    async function uploadAvatar(file, previousUrl) {
        setAvatarLoading(true);

        const optimized = await compressImage(file);
        const formData = new FormData();
        formData.append('avatar', optimized);
        formData.append('_token', csrfToken);

        try {
            const response = await fetch('/profile/avatar', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, Accept: 'application/json' },
                credentials: 'same-origin',
                body: formData,
            });
            const data = await response.json();

            if (!response.ok || !data.success) throw new Error(data.message ?? 'Error al subir la imagen');

            setAllAvatars(data.url);
            showToast('Avatar actualizado correctamente', 'success');
        } catch (err) {
            console.error('uploadAvatar:', err);
            showToast(err.message ?? 'No se pudo actualizar el avatar', 'error');
            if (previousUrl) setAllAvatars(previousUrl);
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

    if (btnView && imgModal) {
        btnView.addEventListener('click', (e) => {
            e.stopPropagation();
            window.openModal('imgModal');
        });
    }
})();
