/**
 * Inicializa el botón de likes
 * Optimistic UI: actualiza la UI inmediatamente, revierte si hay error
 */
export function initLikeButton() {
    document.addEventListener("click", (e) => {
        const likeBtn = e.target.closest(".like-btn");
        if (!likeBtn) return;

        e.preventDefault();
        const projectId = likeBtn.dataset.projectId;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        const countSpan = likeBtn.querySelector(".like-count");
        const svg = likeBtn.querySelector("svg");
        
        const isLiked = likeBtn.classList.contains("liked");
        const currentCount = parseInt(countSpan.textContent, 10);
        
        likeBtn.classList.toggle("liked");
        countSpan.textContent = isLiked ? currentCount - 1 : currentCount + 1;
        svg.setAttribute("fill", isLiked ? "none" : "currentColor");

        fetch(`/projects/${projectId}/like`, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": csrfToken,
                "Content-Type": "application/json"
            }
        })
        .then((response) => response.json())
        .then((data) => {
            countSpan.textContent = data.likes_count;
        })
        .catch((error) => {
            console.error("Error:", error);
            likeBtn.classList.toggle("liked");
            countSpan.textContent = currentCount;
            svg.setAttribute("fill", isLiked ? "currentColor" : "none");
        });
    });
}