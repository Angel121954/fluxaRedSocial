// commentHandler.js - versión simplificada
// Esperar a que se carguen los módulos
document.addEventListener('DOMContentLoaded', function () {
    // Inicializar like button si está disponible
    if (typeof initLikeButton === 'function') {
        initLikeButton();
    }

    document.addEventListener('click', function (e) {
        const commentBtn = e.target.closest('.comment-btn');
        if (!commentBtn) return;

        const projectId = commentBtn.dataset.projectId;
        if (!projectId) {
            console.error('No projectId found on button');
            return;
        }


        // Obtener datos del proyecto
        const projectCard = commentBtn.closest('[data-project-id]') ||
            document.querySelector(`[data-project-id="${projectId}"]`);
        if (!projectCard) {
            console.error('No project card found');
            return;
        }

        const content = projectCard.querySelector('.card-title, .post-title, .msn-card-title')?.textContent || '';
        const avatar = projectCard.querySelector('img')?.src || document.body.dataset.userAvatar || '';
        const author = projectCard.querySelector('.card-author, .post-author')?.textContent || document.body.dataset.userName || '';

        // Usar la función global
        if (window.openCommentsModal) {
            window.openCommentsModal({
                projectId: projectId,
                avatar: avatar,
                author: author,
                handle: document.body.dataset.userHandle || '',
                time: '',
                content: content,
                isOpenSource: projectCard.dataset.openSource === '1',
            });
        } else {
            console.error('openCommentsModal not found in window');
        }
    });
});
