document.querySelectorAll(".f-pill").forEach((p) => {
    p.addEventListener("click", () => {
        document
            .querySelectorAll(".f-pill")
            .forEach((x) => x.classList.remove("active"));
        p.classList.add("active");
    });
});
