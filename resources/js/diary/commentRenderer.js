// commentRenderer.js - Renderizado de comentarios en el modal del Diario

function getCurrentUserId() {
    return document.body.dataset.userId || null;
}

function initializeLikeButtons(container) {
    if (!container) return;
    container.querySelectorAll('.like-comment-btn').forEach(button => {
        const path = button.querySelector('svg path');
        if (!path) return;
        path.setAttribute('fill', button.classList.contains('liked') ? 'currentColor' : 'none');
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
                <div class="comments-empty-text">Sé el primero en comentar esta respuesta</div>
            </div>
        `;
        return;
    }

    container.innerHTML = comments.map(comment => {
        const timestamp = comment.created_at ? new Date(comment.created_at).getTime() : '';
        return `
        <div class="comment-item" data-comment-id="${comment.id}">
            <img src="${comment.user.avatar_url}" alt="${comment.user.name}" class="comment-avatar">
            <div class="comment-body">
                <div class="comment-header">
                    <span class="comment-author">${comment.user.name}</span>
                    <span style="color:var(--ink-200);">·</span>
                    <span class="comment-time" data-timestamp="${timestamp}">${comment.created_at_human}</span>
                    ${String(comment.user_id) === getCurrentUserId() ? `
                    <button class="comment-action comment-delete-btn" data-comment-id="${comment.id}" aria-label="Eliminar comentario">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="3 6 5 6 21 6" />
                            <path d="M19 6l-1 14H6L5 6" />
                            <path d="M10 11v6M14 11v6" />
                            <path d="M9 6V4h6v2" />
                        </svg>
                    </button>
                    ` : ''}
                </div>
                <p class="comment-text">${comment.content}</p>
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
                                <img src="${reply.user.avatar_url}" alt="${reply.user.name}" class="comment-avatar">
                                <div class="comment-body">
                                    <div class="comment-header">
                                        <span class="comment-author">${reply.user.name}</span>
                                        <span style="color:var(--ink-200);">·</span>
                                        <span class="comment-time" data-timestamp="${replyTimestamp}">${reply.created_at_human}</span>
                                        ${String(reply.user_id) === getCurrentUserId() ? `
                                        <button class="comment-action comment-delete-btn" data-comment-id="${reply.id}" aria-label="Eliminar comentario">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="3 6 5 6 21 6" />
                                                <path d="M19 6l-1 14H6L5 6" />
                                                <path d="M10 11v6M14 11v6" />
                                                <path d="M9 6V4h6v2" />
                                            </svg>
                                        </button>
                                        ` : ''}
                                    </div>
                                    <p class="comment-text">${reply.content}</p>
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

    // Evitar duplicados
    if (container.querySelector(`[data-comment-id="${comment.id}"]`)) return;

    const isOwner = String(comment.user_id) === getCurrentUserId();
    const commentHTML = `
        <div class="comment-item" data-comment-id="${comment.id}">
            <img src="${comment.user.avatar_url}" alt="${comment.user.name}" class="comment-avatar">
            <div class="comment-body">
                <div class="comment-header">
                    <span class="comment-author">${comment.user.name}</span>
                    <span style="color:var(--ink-200);">·</span>
                    <span class="comment-time">Justo ahora</span>
                    ${isOwner ? `
                    <button class="comment-action comment-delete-btn" data-comment-id="${comment.id}" aria-label="Eliminar comentario">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="3 6 5 6 21 6" />
                            <path d="M19 6l-1 14H6L5 6" />
                            <path d="M10 11v6M14 11v6" />
                            <path d="M9 6V4h6v2" />
                        </svg>
                    </button>
                    ` : ''}
                </div>
                <p class="comment-text">${comment.content}</p>
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

    if (container.querySelector(`[data-comment-id="${reply.id}"]`)) return;

    const parentComment = container.querySelector(`[data-comment-id="${parentId}"]`);
    if (!parentComment) return;

    let repliesDiv = parentComment.querySelector('.comment-replies');
    if (!repliesDiv) {
        repliesDiv = document.createElement('div');
        repliesDiv.className = 'comment-replies';
        parentComment.querySelector('.comment-body').appendChild(repliesDiv);
    }

    const isOwner = String(reply.user_id) === getCurrentUserId();
    const replyHTML = `
        <div class="comment-item reply" data-comment-id="${reply.id}">
            <img src="${reply.user.avatar_url}" alt="${reply.user.name}" class="comment-avatar">
            <div class="comment-body">
                <div class="comment-header">
                    <span class="comment-author">${reply.user.name}</span>
                    <span style="color:var(--ink-200);">·</span>
                    <span class="comment-time">Justo ahora</span>
                    ${isOwner ? `
                    <button class="comment-action comment-delete-btn" data-comment-id="${reply.id}" aria-label="Eliminar comentario">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="3 6 5 6 21 6" />
                            <path d="M19 6l-1 14H6L5 6" />
                            <path d="M10 11v6M14 11v6" />
                            <path d="M9 6V4h6v2" />
                        </svg>
                    </button>
                    ` : ''}
                </div>
                <p class="comment-text">${reply.content}</p>
            </div>
        </div>
    `;
    repliesDiv.insertAdjacentHTML('beforeend', replyHTML);
    updateCommentTimes(container);
    initializeLikeButtons(container);
}
