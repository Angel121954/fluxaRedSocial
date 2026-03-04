document.querySelectorAll(".tab").forEach((t) => {
    t.addEventListener("click", () => {
        document
            .querySelectorAll(".tab")
            .forEach((x) => x.classList.remove("active"));
        t.classList.add("active");
    });
});
