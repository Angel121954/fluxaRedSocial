// commentRenderer.js - Renderizado de comentarios y respuestas

import { escapeHtml } from '../shared/escapeHtml.js';
import { relativeTime } from '../shared/relativeTime.js';

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

    container.querySelectorAll('.comment-time').forEach((timeEl) => {
        let timestamp = parseInt(timeEl.dataset.timestamp);

        if (!timestamp) {
            const text = timeEl.textContent;
            if (text === 'Justo ahora' || text.includes('segundo')) {
                timestamp = Date.now();
                timeEl.dataset.timestamp = timestamp;
            }
            return;
        }

        timeEl.textContent = relativeTime(timestamp);
    });
}

const timeUpdateIntervals = new WeakMap();

export function startTimeUpdates(container) {
    if (!container) return;

    stopTimeUpdates(container);
    updateCommentTimes(container);

    const interval = setInterval(() => updateCommentTimes(container), 30000);
    timeUpdateIntervals.set(container, interval);
}

export function stopTimeUpdates(container) {
    if (!container) return;

    const interval = timeUpdateIntervals.get(container);

    if (interval) {
        clearInterval(interval);
        timeUpdateIntervals.delete(container);
    }
}

function commentToHtml(comment) {
    const timestamp = comment.created_at ? new Date(comment.created_at).getTime() : '';
    return `
        <div class="comment-item" data-comment-id="${comment.id}">
            <img src="${escapeHtml(comment.user.avatar_url)}" alt="${escapeHtml(comment.user.name)}" class="comment-avatar">
            <div class="comment-body">
                <div class="comment-header">
                    <span class="comment-author">${escapeHtml(comment.user.name)}</span>
                    <span class="comment-dot">·</span>
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
                                        <span class="comment-dot">·</span>
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
    `;
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

    container.innerHTML = comments.map(commentToHtml).join('');

    updateCommentTimes(container);
    initializeLikeButtons(container);
}

export function appendComments(comments, container) {
    if (!container || !comments || comments.length === 0) return;

    const html = comments.map(commentToHtml).join('');
    const loadMoreBtn = container.querySelector('#commentsLoadMoreBtn');

    if (loadMoreBtn) {
        loadMoreBtn.insertAdjacentHTML('beforebegin', html);
    } else {
        container.insertAdjacentHTML('beforeend', html);
    }

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
                    <span class="comment-dot">·</span>
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
        const loadMoreBtn = container.querySelector('#commentsLoadMoreBtn');

        if (loadMoreBtn) {
            loadMoreBtn.insertAdjacentHTML('beforebegin', commentHTML);
        } else {
            container.insertAdjacentHTML("beforeend", commentHTML);
        }
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
                    <span class="comment-dot">·</span>
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
