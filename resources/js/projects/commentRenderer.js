// commentRenderer.js - Renderizado de comentarios y respuestas

import { escapeHtml } from '../shared/escapeHtml.js';

function initializeLikeButtons(container) {
    if (!container) return;
    const likeButtons = container.querySelectorAll('.like-comment-btn');
    likeButtons.forEach(button => {
        const path = button.querySelector('svg path');
        if (!path) return;
        if (button.classList.contains('liked')) {
            path.setAttribute('fill', 'currentColor');
        } else {
            path.setAttribute('fill', 'none');
        }
    });
}

function updateCommentTimes(container) {
    if (!container) return;

    const times = container.querySelectorAll('.comment-time');
    const now = new Date();

    times.forEach((timeEl) => {
        let timestamp = parseInt(timeEl.dataset.timestamp);

        if (!timestamp) {
            const text = timeEl.textContent;
            if (text === 'Justo ahora' || text.includes('segundo')) {
                timestamp = Date.now();
                timeEl.dataset.timestamp = timestamp;
            }
            return;
        }

        const diff = Math.floor((now - timestamp) / 1000);
        let text = '';

        if (diff < 1) {
            text = 'Justo ahora';
        } else if (diff < 60) {
            text = 'Hace ' + diff + 's';
        } else if (diff < 3600) {
            text = 'Hace ' + Math.floor(diff / 60) + 'm';
        } else if (diff < 86400) {
            text = 'Hace ' + Math.floor(diff / 3600) + 'h';
        } else {
            const days = Math.floor(diff / 86400);
            text = days === 1 ? 'Ayer' : 'Hace ' + days + 'd';
        }

        timeEl.textContent = text;
    });
}

export function startTimeUpdates(container) {
    if (!container) return;
    updateCommentTimes(container);
    setInterval(() => updateCommentTimes(container), 1000);
}

export function renderComments(comments, container) {
    if (!container) return;

    if (!comments || comments.length === 0) {
        container.innerHTML = `
            <div class="comments-empty">
                <div class="comments-empty-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                </div>
                <div class="comments-empty-title">Sin comentarios aún</div>
                <div class="comments-empty-text">Sé el primero en comentar este post</div>
            </div>
        `;
        return;
    }

    container.innerHTML = comments.map(comment => {
        const timestamp = comment.created_at ? new Date(comment.created_at).getTime() : '';
        return `
        <div class="comment-item" data-comment-id="${comment.id}">
            <img src="${escapeHtml(comment.user.avatar_url)}" alt="${escapeHtml(comment.user.name)}" class="comment-avatar">
            <div class="comment-body">
                <div class="comment-header">
                    <span class="comment-author">${escapeHtml(comment.user.name)}</span>
                    <span style="color:var(--ink-200);">·</span>
                    <span class="comment-time" data-timestamp="${timestamp}">${comment.created_at_human}</span>
                </div>
                <p class="comment-text">${escapeHtml(comment.content)}</p>
        <div class="comment-actions">
            <button class="comment-action like-comment-btn ${comment.is_liked ? 'liked' : ''}" data-comment-id="${comment.id}">
                <svg stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
                Me gusta
            </button>
            <button class="comment-action reply-btn" data-comment-id="${comment.id}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                </svg>
                Responder
            </button>
        </div>
        ${comment.children && comment.children.length > 0 ? `
                    <div class="comment-replies">
                        ${comment.children.map(reply => {
                            const replyTimestamp = reply.created_at ? new Date(reply.created_at).getTime() : '';
                            return `
                            <div class="comment-item reply" data-comment-id="${reply.id}">
                                <img src="${escapeHtml(reply.user.avatar_url)}" alt="${escapeHtml(reply.user.name)}" class="comment-avatar">
                                <div class="comment-body">
                                    <div class="comment-header">
                                        <span class="comment-author">${escapeHtml(reply.user.name)}</span>
                                        <span style="color:var(--ink-200);">·</span>
                                        <span class="comment-time" data-timestamp="${replyTimestamp}">${reply.created_at_human}</span>
                                    </div>
                                    <p class="comment-text">${escapeHtml(reply.content)}</p>
                                </div>
                            </div>
                        `}).join('')}
                    </div>
                ` : ''}
            </div>
        </div>
    `    }).join('');

    updateCommentTimes(container);
    initializeLikeButtons(container);
}

export function addComment(comment, container) {
    if (!container) return;

    const commentHTML = `
        <div class="comment-item" data-comment-id="${comment.id}">
            <img src="${escapeHtml(comment.user.avatar_url)}" alt="${escapeHtml(comment.user.name)}" class="comment-avatar">
            <div class="comment-body">
                <div class="comment-header">
                    <span class="comment-author">${escapeHtml(comment.user.name)}</span>
                    <span style="color:var(--ink-200);">·</span>
                    <span class="comment-time">Justo ahora</span>
                </div>
                <p class="comment-text">${escapeHtml(comment.content)}</p>
                <div class="comment-actions">
                    <button class="comment-action like-comment-btn" data-comment-id="${comment.id}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                        Me gusta
                    </button>
                    <button class="comment-action reply-btn" data-comment-id="${comment.id}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                        </svg>
                        Responder
                    </button>
                </div>
            </div>
        </div>
    `;

    if (container.querySelector(".comments-empty")) {
        container.innerHTML = commentHTML;
    } else {
        container.insertAdjacentHTML("beforeend", commentHTML);
    }

    updateCommentTimes(container);
    initializeLikeButtons(container);
    container.scrollTop = container.scrollHeight;
}

export function addReply(parentId, reply, container) {
    if (!container) return;

    const parentComment = container.querySelector(`[data-comment-id="${parentId}"]`);
    if (!parentComment) return;

    let repliesDiv = parentComment.querySelector('.comment-replies');
    if (!repliesDiv) {
        repliesDiv = document.createElement('div');
        repliesDiv.className = 'comment-replies';
        parentComment.querySelector('.comment-body').appendChild(repliesDiv);
    }

    const replyHTML = `
        <div class="comment-item reply" data-comment-id="${reply.id}">
            <img src="${escapeHtml(reply.user.avatar_url)}" alt="${escapeHtml(reply.user.name)}" class="comment-avatar">
            <div class="comment-body">
                <div class="comment-header">
                    <span class="comment-author">${escapeHtml(reply.user.name)}</span>
                    <span style="color:var(--ink-200);">·</span>
                    <span class="comment-time">Justo ahora</span>
                </div>
                <p class="comment-text">${escapeHtml(reply.content)}</p>
            </div>
        </div>
    `;
    repliesDiv.insertAdjacentHTML('beforeend', replyHTML);
    updateCommentTimes(container);
    initializeLikeButtons(container);
}
