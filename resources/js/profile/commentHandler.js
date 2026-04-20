import { initLikeButton } from '../core/explore/like.js';
import { openCommentsModal } from '../projects/modalComment.js';

document.addEventListener('DOMContentLoaded', function() {
    initLikeButton();

    document.addEventListener('click', function(e) {
        const commentBtn = e.target.closest('.comment-btn');
        if (!commentBtn) return;

        const projectId = commentBtn.dataset.projectId;
        const projectCard = document.querySelector(`[data-project-id="${projectId}"]`);
        if (!projectCard) return;

        const content = projectCard.querySelector('.card-title')?.textContent || '';
        
        openCommentsModal({
            avatar: document.body.dataset.userAvatar || '',
            author: document.body.dataset.userName || '',
            handle: document.body.dataset.userHandle || '',
            time: '',
            content: content,
            commentsKey: `project_${projectId}`
        });
    });
});
