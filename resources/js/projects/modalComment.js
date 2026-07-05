import { renderComments, addComment, addReply, startTimeUpdates } from './commentRenderer.js';
import { initCommentForm, setReplyingTo as setReply, resetCommentForm as resetForm } from './commentForm.js';

let commentsModal, modalCommentsList;
let currentProjectId = null;

function getElements() {
    commentsModal = document.getElementById("commentsModal");
    modalCommentsList = document.getElementById("modalCommentsList");
}

function setCurrentProjectId(id) {
    currentProjectId = id;
}

function getCurrentProjectId() {
    return currentProjectId;
}

function initCommentModal() {
    getElements();
    if (!commentsModal) return;

    initCommentForm({
        onSubmit: (comment, isReply) => {
            if (isReply) {
                addReply(isReply, comment, modalCommentsList);
            } else {
                addComment(comment, modalCommentsList);
            }
        },
        getCurrentProjectId: getCurrentProjectId
    });

    if (modalCommentsList) {
        modalCommentsList.addEventListener("click", (e) => {
            const replyBtn = e.target.closest('.reply-btn');
            if (replyBtn) {
                setReply(replyBtn.dataset.commentId);
                return;
            }

            const likeBtn = e.target.closest('.like-comment-btn');
            if (likeBtn) {
                toggleCommentLike(likeBtn.dataset.commentId, likeBtn);
            }
        });
    }
}

export function openCommentsModal(postData) {
    getElements();
    if (!commentsModal) return;

    const projectId = postData.projectId;
    if (!projectId) {
        console.error('projectId is undefined in postData');
        return;
    }

    setCurrentProjectId(projectId);

    document.getElementById("modalPostAvatar").src = postData.avatar;
    document.getElementById("modalPostAuthor").textContent = postData.author;
    document.getElementById("modalPostHandleTime").textContent =
        `${postData.handle} · ${postData.time}`;
    document.getElementById("modalPostContent").textContent = postData.content;

    const badge = document.getElementById("modalPostBadge");
    if (badge) {
        badge.style.display = postData.isOpenSource ? "flex" : "none";
    }

    loadComments();

    window.openModal('commentsModal');
    startTimeUpdates(modalCommentsList);
    setTimeout(() => {
        const commentTextarea = document.getElementById("commentTextarea");
        commentTextarea?.focus();
    }, 100);
}

window.openCommentsModal = openCommentsModal;

function closeCommentsModal() {
    window.closeModal('commentsModal');
    resetForm();
    setCurrentProjectId(null);
}

async function loadComments() {
    const projectId = getCurrentProjectId();
    if (!projectId) {
        console.error('loadComments: currentProjectId is undefined');
        return;
    }
    try {
        const response = await fetch(`/projects/${projectId}/comments`, {
            credentials: 'same-origin'
        });
        if (!response.ok) throw new Error('Error cargando comentarios');

        const data = await response.json();
        renderComments(data.comments, modalCommentsList);
    } catch (error) {
        console.error('Error:', error);
        if (modalCommentsList) {
            modalCommentsList.innerHTML = `
                <div class="comments-empty">
                    <div class="comments-empty-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                    </div>
                    <div class="comments-empty-title">Error al cargar comentarios</div>
                </div>
            `;
        }
    }
}

async function toggleCommentLike(commentId, button) {
    if (!commentId || commentId === 'undefined') {
        console.error('commentId is undefined or invalid:', commentId);
        return;
    }

    const svg = button.querySelector('svg');
    const svgPath = svg?.querySelector('path');
    const wasLiked = button.classList.contains('liked');
    const wasFilled = svgPath?.getAttribute('fill') === 'currentColor';
    const countEl = button.querySelector('.like-count') || button.querySelector('span:not(.sr-only)');
    const prevCount = countEl ? parseInt(countEl.textContent, 10) : null;

    button.classList.toggle('liked');
    if (svgPath) {
        svgPath.setAttribute('fill', wasLiked ? 'none' : 'currentColor');
    }

    if (countEl) {
        countEl.textContent = wasLiked ? Math.max(0, prevCount - 1) : prevCount + 1;
    }

    try {
        const response = await fetch(`/comments/${commentId}/like`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        });

        if (!response.ok) {
            const error = await response.json().catch(() => ({}));
            throw new Error(error.message || `Error ${response.status} al dar like`);
        }

        const data = await response.json();

        button.classList.toggle('liked', data.liked);
        if (svgPath) {
            svgPath.setAttribute('fill', data.liked ? 'currentColor' : 'none');
        }
        if (countEl && data.likes_count !== undefined) {
            countEl.textContent = data.likes_count;
        }
    } catch (error) {
        button.classList.toggle('liked', wasLiked);
        if (svgPath) {
            svgPath.setAttribute('fill', wasFilled ? 'currentColor' : 'none');
        }
        if (countEl && prevCount !== null) {
            countEl.textContent = prevCount;
        }
        console.error('Error al dar like:', error.message);
    }
}

document.addEventListener("DOMContentLoaded", initCommentModal);

window.openCommentsModal = openCommentsModal;
