/**
 * admin/diary/modal.js — Modal para crear/editar pregunta del diario
 */

document.addEventListener('DOMContentLoaded', function () {
    const backdrop = document.getElementById('diaryModalBackdrop');
    const form     = document.getElementById('diaryForm');
    const question = document.getElementById('diary_question');
    const emoji    = document.getElementById('diary_emoji');
    const method   = document.getElementById('diaryFormMethod');
    const title    = document.getElementById('diaryModalTitle');
    const subtitle = document.getElementById('diaryModalSubtitle');
    const submit   = document.getElementById('diaryModalSubmit');

    if (!backdrop) return;

    let editingId = null;

    /* Abrir modal para crear */
    document.getElementById('openDiaryModal')?.addEventListener('click', function () {
        editingId = null;
        form.action = this.dataset.url;
        method.value = 'POST';
        title.textContent = 'Nueva pregunta del diario';
        subtitle.textContent = 'Crea una pregunta para que la comunidad responda.';
        submit.textContent = 'Crear pregunta';
        question.value = '';
        emoji.value = '';
        openModal();
    });

    /* Abrir modal para editar */
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.diary-admin-card__edit-btn');
        if (!btn) return;

        editingId = btn.dataset.id;
        form.action = '/admin/diary/' + editingId;
        method.value = 'PATCH';
        title.textContent = 'Editar pregunta';
        subtitle.textContent = 'Actualiza la pregunta del diario.';
        submit.textContent = 'Guardar cambios';
        question.value = btn.dataset.question;
        emoji.value = btn.dataset.emoji;
        openModal();
    });

    /* Cerrar */
    document.getElementById('closeDiaryModal')?.addEventListener('click', closeModal);
    document.getElementById('cancelDiaryModal')?.addEventListener('click', closeModal);
    backdrop.addEventListener('click', function (e) {
        if (e.target === backdrop) closeModal();
    });

    function openModal() {
        window.closeAllModals?.();
        backdrop.classList.add('is-open');
        question?.focus();
        window.lockBodyScroll?.();
    }

    function closeModal() {
        backdrop.classList.remove('is-open');
        editingId = null;
        window.unlockBodyScroll?.();
    }

    window.closeDiaryModal = closeModal;

    window.closeAllModals = (window.closeAllModals || function () {});
    const origCloseAll = window.closeAllModals;
    window.closeAllModals = function () {
        origCloseAll();
        closeModal();
    };
});
