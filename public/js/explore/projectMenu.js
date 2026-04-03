/**
 * Maneja el dropdown del menú de cada proyecto
 */
document.addEventListener("click", (e) => {
    const menuBtn = e.target.closest(".post-menu-btn");
    const dropItem = e.target.closest(".drop-item");

    if (menuBtn) {
        e.preventDefault();
        e.stopPropagation();
        const projectId = menuBtn.dataset.projectId;
        const dropdown = document.querySelector(`.drop-menu[data-project-id="${projectId}"]`);

        document.querySelectorAll(".drop-menu").forEach(d => {
            if (d !== dropdown) d.classList.remove("open");
        });
        document.querySelectorAll(".post-menu-btn").forEach(b => {
            if (b !== menuBtn) b.classList.remove("is-open");
        });

        const isOpen = dropdown?.classList.toggle("open");
        menuBtn.classList.toggle("is-open", isOpen);
        return;
    }

    if (dropItem) {
        e.preventDefault();
        const action = dropItem.dataset.action;
        const projectId = dropItem.dataset.projectId;

        handleProjectAction(action, projectId, dropItem, () => {
            const dropdown = dropItem.closest(".drop-menu");
            const menuBtn = document.querySelector(`.post-menu-btn[data-project-id="${projectId}"]`);
            dropdown?.classList.remove("open");
            menuBtn?.classList.remove("is-open");
        });
        return;
    }

    document.querySelectorAll(".drop-menu.open").forEach(d => d.classList.remove("open"));
    document.querySelectorAll(".post-menu-btn.is-open").forEach(b => b.classList.remove("is-open"));
});

document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") {
        document.querySelectorAll(".drop-menu.open").forEach(d => d.classList.remove("open"));
        document.querySelectorAll(".post-menu-btn.is-open").forEach(b => b.classList.remove("is-open"));
        document.querySelectorAll(".modal-backdrop.show").forEach(m => m.classList.remove("show"));
    }
});

function handleProjectAction(action, projectId, dropItem, closeMenu) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    const url = `${window.location.origin}/projects/${projectId}`;

    switch (action) {
        case "bookmark":
            const currentText = dropItem.querySelector("span").textContent;
            const isCurrentlyBookmarked = currentText.includes("Quitar");
            dropItem.querySelector("span").textContent = isCurrentlyBookmarked ? "Agregar a favoritos" : "Quitar de favoritos";

            if (closeMenu) closeMenu();

            fetch(`/projects/${projectId}/bookmark`, {
                method: "POST",
                headers: { "X-CSRF-TOKEN": csrfToken }
            })
            .then(res => res.json())
            .then(data => dropItem.querySelector("span").textContent = data.is_bookmarked ? "Quitar de favoritos" : "Agregar a favoritos")
            .catch(() => dropItem.querySelector("span").textContent = currentText);
            break;

        case "share":
        case "copy-link":
            if (closeMenu) closeMenu();
            if (navigator.share) {
                navigator.share({ url });
            } else {
                navigator.clipboard.writeText(url);
                showToast("Enlace copiado");
            }
            break;

        case "report":
            if (closeMenu) closeMenu();
            document.getElementById("reportModal").classList.add("show");
            document.getElementById("reportForm").dataset.projectId = projectId;
            document.getElementById("reportReason").value = "";
            break;
    }
}

function showToast(message) {
    const toast = document.getElementById("toast");
    document.getElementById("toastMessage").textContent = message;
    toast.classList.add("show");
    setTimeout(() => toast.classList.remove("show"), 3000);
}

// Cerrar modales
document.addEventListener("click", (e) => {
    const closeBtn = e.target.closest("[data-close]");
    if (closeBtn) {
        document.getElementById(closeBtn.dataset.close).classList.remove("show");
        return;
    }
    if (e.target.classList.contains("modal-backdrop")) {
        e.target.classList.remove("show");
    }
});

// Submit reporte
document.getElementById("reportForm")?.addEventListener("submit", function(e) {
    e.preventDefault();
    const reason = document.getElementById("reportReason").value;
    if (reason.length < 10) return;

    fetch(`/projects/${this.dataset.projectId}/report`, {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
            "Content-Type": "application/json"
        },
        body: JSON.stringify({ reason })
    })
    .then(res => res.json())
    .then(data => {
        document.getElementById("reportModal").classList.remove("show");
        showToast(data.message || "Reporte enviado");
    });
});
