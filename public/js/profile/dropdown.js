const btnMore = document.getElementById("btnMore");
const dropMenu = document.getElementById("dropMenu");

if (btnMore && dropMenu) {
    btnMore.addEventListener("click", (e) => {
        e.stopPropagation();
        const isOpen = dropMenu.classList.toggle("open");
        btnMore.classList.toggle("is-open", isOpen);
    });
}

document.addEventListener("click", (e) => {
    if (
        dropMenu &&
        btnMore &&
        !dropMenu.contains(e.target) &&
        e.target !== btnMore
    ) {
        dropMenu.classList.remove("open");
        btnMore.classList.remove("is-open");
    }
});

document.addEventListener("keydown", (e) => {
    if (e.key === "Escape" && dropMenu) {
        dropMenu.classList.remove("open");
        btnMore?.classList.remove("is-open");
    }
});
