/**
 * Inicialización principal de la página explore
 * Carga los módulos y configura los event listeners
 */

document.addEventListener("DOMContentLoaded", () => {
    const container = document.getElementById("publications-container");
    if (!container) return;

    initLoadMore(container)();
    initTabs(() => initLoadMore(container)());
    initLikeButton();

    document.addEventListener("click", (e) => {
        const commentBtn = e.target.closest(".comment-btn");
        if (!commentBtn) return;

        const postCard = commentBtn.closest(".post-card");
        if (!postCard) return;

        const avatar = postCard.querySelector(".post-avatar")?.src || "";
        const author = postCard.querySelector(".post-author")?.textContent || "";
        const handle = postCard.querySelector(".post-handle")?.textContent || "";
        const time = postCard.querySelector(".post-time")?.textContent || "";
        const content = postCard.querySelector(".post-content")?.textContent || "";

        const projectId = commentBtn.dataset.projectId;
        const commentsKey = projectId ? `project_${projectId}` : "post3";

        if (typeof openCommentsModal === "function") {
            openCommentsModal({ avatar, author, handle, time, content, commentsKey });
        } else {
            document.getElementById("commentsModal").classList.add("show");
        }
    });
});