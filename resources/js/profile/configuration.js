// ── Char counter ────────────────────────────────────
const bioInput = document.getElementById("input-bio");
const charCount = document.getElementById("charCount");

if (bioInput && charCount) {
    function updateCharCount() {
        const current = bioInput.value.length;
        const max = parseInt(bioInput.getAttribute("maxlength"));
        charCount.textContent = `${current}/${max}`;

        // Cambiar color según proximidad al límite
        charCount.classList.remove("warn", "danger");
        if (current >= max) {
            charCount.classList.add("danger");
        } else if (current >= max * 0.8) {
            charCount.classList.add("warn");
        }
    }

    bioInput.addEventListener("input", updateCharCount);
    updateCharCount();
}
