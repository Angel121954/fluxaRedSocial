// commentForm.js - Lógica del formulario de comentarios y respuestas

let commentTextarea, commentActions, btnCancelComment, btnSubmitComment;
let replyingTo = null;

export function initCommentForm({ onSubmit, getCurrentProjectId }) {
    commentTextarea = document.getElementById("commentTextarea");
    commentActions = document.getElementById("commentActions");
    btnCancelComment = document.getElementById("btnCancelComment");
    btnSubmitComment = document.getElementById("btnSubmitComment");

    if (!commentTextarea || !btnSubmitComment) return;

    commentTextarea.addEventListener("input", function () {
        const hasContent = this.value.trim().length > 0;
        if (commentActions) commentActions.style.display = hasContent ? "flex" : "none";
        if (btnSubmitComment) btnSubmitComment.disabled = !hasContent;

        this.style.height = "auto";
        this.style.height = Math.min(this.scrollHeight, 120) + "px";
    });

    commentTextarea.addEventListener("keydown", (e) => {
        if (e.key === "Enter" && !e.shiftKey) {
            e.preventDefault();
            if (btnSubmitComment && !btnSubmitComment.disabled) {
                btnSubmitComment.click();
            }
        }
    });

    if (btnCancelComment) {
        btnCancelComment.addEventListener("click", () => {
            resetCommentForm();
        });
    }

    btnSubmitComment.addEventListener("click", async () => {
        const commentText = commentTextarea.value.trim();
        if (!commentText) return;

        const projectId = getCurrentProjectId();
        if (!projectId) return;

        try {
            const body = { content: commentText };
            if (replyingTo) {
                body.parent_id = replyingTo;
            }

            const response = await fetch(`/projects/${projectId}/comments`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                credentials: 'same-origin',
                body: JSON.stringify(body),
            });

            if (!response.ok) throw new Error('Error al enviar comentario');

            const data = await response.json();
            
            if (onSubmit) {
                onSubmit(data.comment, replyingTo);
            }
            
            resetCommentForm();

            // Actualizar contador en la tarjeta
            const commentBtn = document.querySelector(`.comment-btn[data-project-id="${projectId}"]`);
            if (commentBtn) {
                const countSpan = commentBtn.querySelector('span');
                if (countSpan) {
                    const currentCount = parseInt(countSpan.textContent) || 0;
                    countSpan.textContent = currentCount + 1;
                }
            }
        } catch (error) {
            console.error('Error:', error);
        }
    });
}

export function setReplyingTo(commentId) {
    replyingTo = commentId;
    if (commentTextarea) {
        commentTextarea.value = '';
        commentTextarea.placeholder = `Respondiendo al comentario...`;
        if (commentActions) commentActions.style.display = "flex";
        commentTextarea.focus();
    }
}

export function resetCommentForm() {
    replyingTo = null;
    if (!commentTextarea) return;
    commentTextarea.value = "";
    commentTextarea.style.height = "auto";
    commentTextarea.placeholder = "Escribe un comentario...";
    if (commentActions) commentActions.style.display = "none";
    if (btnSubmitComment) btnSubmitComment.disabled = true;
}
