// modalComment.js - Lógica principal del modal de comentarios

import { renderComments, addComment, addReply, startTimeUpdates } from './commentRenderer.js';
import { initCommentForm, setReplyingTo as setReply, resetCommentForm as resetForm } from './commentForm.js';

// Referencias DOM
let commentsModal, closeCommentsBtn, modalCommentsList;
let currentProjectId = null;

function getElements() {
    commentsModal = document.getElementById("commentsModal");
    closeCommentsBtn = document.getElementById("closeCommentsModal");
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
    if (!commentsModal || !closeCommentsBtn) return;

    closeCommentsBtn.addEventListener("click", closeCommentsModal);

    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape" && commentsModal.classList.contains("show")) {
            closeCommentsModal();
        }
    });

    commentsModal.addEventListener("click", (e) => {
        if (e.target === commentsModal) {
            closeCommentsModal();
        }
    });

    // Inicializar formulario
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

    // Manejar clics en botones de responder y like
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

    loadComments();

    commentsModal.classList.add("show");
    startTimeUpdates(modalCommentsList);
    setTimeout(() => {
        const commentTextarea = document.getElementById("commentTextarea");
        commentTextarea?.focus();
    }, 100);
}

window.openCommentsModal = openCommentsModal;

function closeCommentsModal() {
    commentsModal.classList.remove("show");
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
        console.log('Like response:', data);
        
        const svg = button.querySelector('svg');
        if (data.liked) {
            button.classList.add('liked');
            svg.setAttribute('fill', 'currentColor');
        } else {
            button.classList.remove('liked');
            svg.setAttribute('fill', 'none');
        }
    } catch (error) {
        console.error('Error al dar like:', error.message);
    }
}

document.addEventListener("DOMContentLoaded", initCommentModal);

// Exportar para uso global
window.openCommentsModal = openCommentsModal;
