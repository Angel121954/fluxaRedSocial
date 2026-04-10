// ═══════════════════════════════════════════════════════
// MODAL DE COMENTARIOS - FLUXA
// ═══════════════════════════════════════════════════════

// Referencias DOM (inicializadas en init)
let commentsModal, closeCommentsBtn, commentTextarea, commentActions;
let btnCancelComment, btnSubmitComment, modalCommentsList;

const commentsData = {
    post1: [
        {
            avatar: "https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=100",
            author: "Mario Gómez",
            time: "Hace 15 min",
            text: "¡Se ve increíble! ¿Qué stack usaste para el backend?",
        },
        {
            avatar: "https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=100",
            author: "Laura Martínez",
            time: "Hace 1 hora",
            text: "Excelente trabajo, me encanta la UI 🎨",
        },
    ],
    post2: [
        {
            avatar: "https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=100",
            author: "Carlos Ruiz",
            time: "Hace 30 min",
            text: "Muy buenos consejos, especialmente el punto 3 💡",
        },
    ],
    post3: [],
};

function getElements() {
    commentsModal = document.getElementById("commentsModal");
    closeCommentsBtn = document.getElementById("closeCommentsModal");
    commentTextarea = document.getElementById("commentTextarea");
    commentActions = document.getElementById("commentActions");
    btnCancelComment = document.getElementById("btnCancelComment");
    btnSubmitComment = document.getElementById("btnSubmitComment");
    modalCommentsList = document.getElementById("modalCommentsList");
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

    commentTextarea.addEventListener("input", (e) => {
        const hasContent = e.target.value.trim().length > 0;
        commentActions.style.display = hasContent ? "flex" : "none";
        btnSubmitComment.disabled = !hasContent;
    });

    commentTextarea.addEventListener("input", function () {
        this.style.height = "auto";
        this.style.height = Math.min(this.scrollHeight, 120) + "px";
    });

    btnCancelComment.addEventListener("click", resetCommentForm);

    btnSubmitComment.addEventListener("click", () => {
        const commentText = commentTextarea.value.trim();
        if (!commentText) return;

        console.log("💬 Comentario enviado:", commentText);
        addComment(commentText);
        resetCommentForm();
    });
}

export function openCommentsModal(postData) {
    getElements();
    if (!commentsModal) return;

    document.getElementById("modalPostAvatar").src = postData.avatar;
    document.getElementById("modalPostAuthor").textContent = postData.author;
    document.getElementById("modalPostHandleTime").textContent =
        `${postData.handle} · ${postData.time}`;
    document.getElementById("modalPostContent").textContent = postData.content;

    loadComments(postData.commentsKey);

    commentsModal.classList.add("show");
    setTimeout(() => commentTextarea?.focus(), 100);
}

function closeCommentsModal() {
    commentsModal.classList.remove("show");
    resetCommentForm();
}

function loadComments(commentsKey) {
    const comments = commentsData[commentsKey] || [];
    console.log(comments);

    if (comments.length === 0) {
        modalCommentsList.innerHTML = `
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
    } else {
        modalCommentsList.innerHTML = comments
            .map(
                (comment) => `
            <div class="comment-item">
                <img src="${comment.avatar}" alt="${comment.author}" class="comment-avatar">
                <div class="comment-body">
                    <div class="comment-header">
                        <span class="comment-author">${comment.author}</span>
                        <span style="color:var(--ink-200);">·</span>
                        <span class="comment-time">${comment.time}</span>
                    </div>
                    <p class="comment-text">${comment.text}</p>
                    <div class="comment-actions">
                        <button class="comment-action">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                            Me gusta
                        </button>
                        <button class="comment-action">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                            </svg>
                            Responder
                        </button>
                    </div>
                </div>
            </div>
        `,
            )
            .join("");
    }
}

function resetCommentForm() {
    if (!commentTextarea) return;
    commentTextarea.value = "";
    commentTextarea.style.height = "auto";
    if (commentActions) commentActions.style.display = "none";
    if (btnSubmitComment) btnSubmitComment.disabled = true;
}

function addComment(commentText) {
    if (!modalCommentsList) return;

    const newComment = {
        avatar: "https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100",
        author: "Tú",
        time: "Justo ahora",
        text: commentText,
    };

    const commentHTML = `
        <div class="comment-item">
            <img src="${newComment.avatar}" alt="${newComment.author}" class="comment-avatar">
            <div class="comment-body">
                <div class="comment-header">
                    <span class="comment-author">${newComment.author}</span>
                    <span style="color:var(--ink-200);">·</span>
                    <span class="comment-time">${newComment.time}</span>
                </div>
                <p class="comment-text">${newComment.text}</p>
                <div class="comment-actions">
                    <button class="comment-action">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                        Me gusta
                    </button>
                    <button class="comment-action">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                        </svg>
                        Responder
                    </button>
                </div>
            </div>
        </div>
    `;

    if (modalCommentsList.querySelector(".comments-empty")) {
        modalCommentsList.innerHTML = commentHTML;
    } else {
        modalCommentsList.insertAdjacentHTML("beforeend", commentHTML);
    }

    modalCommentsList.scrollTop = modalCommentsList.scrollHeight;
}

document.addEventListener("DOMContentLoaded", initCommentModal);
