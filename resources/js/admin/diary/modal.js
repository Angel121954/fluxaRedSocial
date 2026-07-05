document.addEventListener('DOMContentLoaded', function () {
    const form     = document.getElementById('diaryForm');
    const question = document.getElementById('diary_question');
    const emoji    = document.getElementById('diary_emoji');
    const method   = document.getElementById('diaryFormMethod');
    const title    = document.getElementById('diaryModalTitle');
    const subtitle = document.getElementById('diaryModalSubtitle');
    const submit   = document.getElementById('diaryModalSubmit');

    let editingId = null;

    document.getElementById('openDiaryModal')?.addEventListener('click', function () {
        editingId = null;
        form.action = this.dataset.url;
        method.value = 'POST';
        title.textContent = 'Nueva pregunta del diario';
        subtitle.textContent = 'Crea una pregunta para que la comunidad responda.';
        submit.textContent = 'Crear pregunta';
        question.value = '';
        emoji.value = '';
        window.openModal('diaryModalBackdrop');
        question?.focus();
    });

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
        window.openModal('diaryModalBackdrop');
        question?.focus();
    });

    function closeDiaryModal() {
        window.closeModal('diaryModalBackdrop');
        editingId = null;
    }

    window.closeDiaryModal = closeDiaryModal;

    window.closeAllModals = (window.closeAllModals || function () {});
    const origCloseAll = window.closeAllModals;
    window.closeAllModals = function () {
        origCloseAll();
        closeDiaryModal();
    };
});
