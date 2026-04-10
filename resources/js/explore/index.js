/**
 * explore/index.js — Entrada para la vista Explore
 * @vite('resources/js/explore/index.js')
 */

import '../shared/modalScrollFix.js';
import './topics.js';
import './projectMenu.js';
import { openCommentsModal } from '../comments/index.js';
import { initTabs }       from './tabs.js';
import { initLoadMore }   from './loadMore.js';
import { initLikeButton } from './like.js';

document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('publications-container');
    if (!container) return;

    const loadMore = initLoadMore(container);
    loadMore();
    initTabs(() => loadMore());
    initLikeButton();

    // Delegar apertura del modal de comentarios desde cualquier post-card
    document.addEventListener('click', (e) => {
        const commentBtn = e.target.closest('.comment-btn');
        if (!commentBtn) return;

        const postCard = commentBtn.closest('.post-card');
        if (!postCard) return;

        openCommentsModal({
            avatar:      postCard.querySelector('.post-avatar')?.src        || '',
            author:      postCard.querySelector('.post-author')?.textContent || '',
            handle:      postCard.querySelector('.post-handle')?.textContent || '',
            time:        postCard.querySelector('.post-time')?.textContent   || '',
            content:     postCard.querySelector('.post-content')?.textContent || '',
            commentsKey: commentBtn.dataset.projectId
                ? `project_${commentBtn.dataset.projectId}`
                : 'post3',
        });
    });
});
