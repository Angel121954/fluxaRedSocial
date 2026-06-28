// commentModal.js - Modal de comentarios para respuestas del Diario

import { renderComments, addComment, addReply, startTimeUpdates } from './commentRenderer.js';
import { initCommentForm, setReplyingTo as setReply, resetCommentForm as resetForm } from './commentForm.js';

let commentsModal, closeCommentsBtn, modalCommentsList;
let currentResponseId = null;

function getElements() {
    commentsModal = document.getElementById("commentsModal");
    closeCommentsBtn = document.getElementById("closeCommentsModal");
    modalCommentsList = document.getElementById("modalCommentsList");
}

function setCurrentResponseId(id) {
    currentResponseId = id;
}

function getCurrentResponseId() {
    return currentResponseId;
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

    initCommentForm({
        onSubmit: (comment, isReply) => {
            if (isReply) {
                addReply(isReply, comment, modalCommentsList);
            } else {
                addComment(comment, modalCommentsList);
            }
        },
        getCurrentResponseId: getCurrentResponseId,
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
                return;
            }

            const deleteBtn = e.target.closest('.comment-delete-btn');
            if (deleteBtn) {
                deleteComment(deleteBtn.dataset.commentId, deleteBtn);
            }
        });
    }
}

export function openDiaryCommentsModal(postData) {
    getElements();
    if (!commentsModal) return;
    lockBodyScroll();

    const responseId = postData.responseId;
    if (!responseId) {
        console.error('responseId is undefined in postData');
        return;
    }

    setCurrentResponseId(responseId);

    document.getElementById("modalPostAvatar").src = postData.avatar;
    document.getElementById("modalPostAuthor").textContent = postData.author;
    document.getElementById("modalPostHandleTime").textContent =
        `${postData.handle} · ${postData.time}`;
    document.getElementById("modalPostContent").textContent = postData.content;

    const badge = document.getElementById("modalPostBadge");
    if (badge) {
        badge.style.display = postData.isOpenSource ? "flex" : "none";
    }

    // Limpiar comentarios anteriores mientras se cargan los nuevos
    if (modalCommentsList) modalCommentsList.innerHTML = '';

    loadComments();

    commentsModal.classList.add("show");
    startTimeUpdates(modalCommentsList);
    setTimeout(() => {
        const commentTextarea = document.getElementById("commentTextarea");
        commentTextarea?.focus();
    }, 100);
}

window.openDiaryCommentsModal = openDiaryCommentsModal;

function closeCommentsModal() {
    commentsModal.classList.remove("show");
    unlockBodyScroll();
    resetForm();
    setCurrentResponseId(null);
}

async function loadComments() {
    const responseId = getCurrentResponseId();
    if (!responseId) {
        console.error('loadComments: currentResponseId is undefined');
        return;
    }
    try {
        const res = await fetch(`/diary/${responseId}/comments`, {
            credentials: 'same-origin',
        });
        if (!res.ok) throw new Error('Error cargando comentarios');

        // Ignorar si el modal se cerró o cambió a otra respuesta
        if (getCurrentResponseId() !== responseId) return;

        const data = await res.json();
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

    if (button.disabled) return;
    button.disabled = true;

    try {
        const res = await fetch(`/diary/comments/${commentId}/like`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
        });

        if (!res.ok) {
            const error = await res.json().catch(() => ({}));
            throw new Error(error.message || `Error ${res.status} al dar like`);
        }

        const data = await res.json();

        const svgPath = button.querySelector('svg path');

        button.classList.remove('liked');
        if (svgPath) svgPath.setAttribute('fill', 'none');

        if (data.liked) {
            button.classList.add('liked');
            if (svgPath) svgPath.setAttribute('fill', 'currentColor');
        }
    } catch (error) {
        console.error('Error al dar like:', error.message);
    } finally {
        button.disabled = false;
    }
}

async function deleteComment(commentId, button) {
    if (!commentId || commentId === 'undefined') return;

    const savedResponseId = currentResponseId;
    closeCommentsModal();

    const { isConfirmed } = await Swal.fire({
        title: '¿Eliminar comentario?',
        text: 'Esta acción no se puede deshacer.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
    });

    if (!isConfirmed) return;

    try {
        const res = await fetch(`/diary/comments/${commentId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                'Accept': 'application/json',
            },
            credentials: 'same-origin',
        });

        if (!res.ok) throw new Error('Error al eliminar comentario');

        const data = await res.json();
        if (!data.success) throw new Error('Error al eliminar comentario');

        // Actualizar contador en la tarjeta de respuesta
        if (savedResponseId && data.comments_count != null) {
            const commentBtn = document.querySelector(`.diary-comment-btn[data-response-id="${savedResponseId}"]`);
            if (commentBtn) {
                const countSpan = commentBtn.querySelector('span');
                if (countSpan) {
                    countSpan.textContent = data.comments_count;
                }
            }
        }
    } catch (error) {
        console.error('Error al eliminar comentario:', error);
        Swal.fire('Error', 'No se pudo eliminar el comentario. Intenta de nuevo.', 'error');
    }
}

document.addEventListener("DOMContentLoaded", () => {
    initCommentModal();

    // Delegación de clics en botones de comentarios del Diario
    document.addEventListener("click", (e) => {
        const btn = e.target.closest('.diary-comment-btn');
        if (!btn) return;

        const card = btn.closest('.diary-response-card');
        if (!card) return;

        const responseId = btn.dataset.responseId;
        const avatar = card.querySelector('.diary-response-card__avatar')?.src || '';
        const author = card.querySelector('.diary-response-card__name')?.textContent || '';
        const handle = document.body.dataset.userHandle || '';
        const timeEl = card.querySelector('.diary-response-card__time');
        const time = timeEl?.textContent?.trim() || '';
        const content = card.querySelector('.diary-response-card__text')?.textContent || '';

        openDiaryCommentsModal({
            responseId,
            avatar,
            author,
            handle,
            time,
            content,
            isOpenSource: card.dataset.openSource === '1',
        });
    });
});

window.openDiaryCommentsModal = openDiaryCommentsModal;
