import { showToast } from '../../shared/toast.js';

export function initLikeButton() {
    document.addEventListener("click", async (e) => {
        const likeBtn = e.target.closest(".like-btn");
        if (!likeBtn || likeBtn.dataset.loading) return;

        e.preventDefault();
        const projectId = likeBtn.dataset.projectId;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        const countSpan = likeBtn.querySelector(".like-count");
        const svg = likeBtn.querySelector("svg");

        const isLiked = likeBtn.classList.contains("liked");
        const currentCount = parseInt(countSpan.textContent, 10);

        likeBtn.classList.toggle("liked");
        countSpan.textContent = isLiked ? currentCount - 1 : currentCount + 1;
        svg?.setAttribute("fill", isLiked ? "none" : "currentColor");

        likeBtn.dataset.loading = "true";
        likeBtn.disabled = true;

        try {
            const response = await fetch(`/projects/${projectId}/like`, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": csrfToken,
                    "Content-Type": "application/json",
                    "Accept": "application/json"
                },
                credentials: "same-origin"
            });

            if (!response.ok) throw new Error("Network response was not ok");

            const data = await response.json();
            countSpan.textContent = data.likes_count;
        } catch (error) {
            console.error("Error:", error);
            likeBtn.classList.toggle("liked");
            countSpan.textContent = currentCount;
            svg?.setAttribute("fill", isLiked ? "currentColor" : "none");
            showToast('No se pudo actualizar el like', 'error');
        } finally {
            delete likeBtn.dataset.loading;
            likeBtn.disabled = false;
        }
    });
}
