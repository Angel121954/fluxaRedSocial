(function () {
    const backdrop = document.getElementById("passwordModal");
    const openBtn = document.getElementById("btnOpenPasswordModal");
    const closeBtns = [
        document.getElementById("btnClosePasswordModal"),
        document.getElementById("btnCancelPasswordModal"),
    ];
    const pwdInput = document.getElementById("password");
    const confirmInput = document.getElementById("password_confirmation");
    const strengthFill = document.getElementById("pwdStrengthFill");
    const strengthLabel = document.getElementById("pwdStrengthLabel");
    const matchHint = document.getElementById("pwdMatchHint");

    if (!backdrop || !openBtn) return;

    /* ── Abrir / cerrar ── */
    function openModal() {
        backdrop.classList.add("is-open");
    }

    function closeModal() {
        backdrop.classList.remove("is-open");
        const form = document.getElementById("formChangePassword");
        if (form) form.reset();
        if (strengthFill) strengthFill.style.width = "0%";
        if (strengthLabel) {
            strengthLabel.textContent = "";
            strengthLabel.style.color = "";
        }
        if (matchHint) {
            matchHint.textContent = "";
            matchHint.className = "pwd-match-hint";
        }
    }

    openBtn.addEventListener("click", openModal);
    closeBtns.forEach((b) => b && b.addEventListener("click", closeModal));
    backdrop.addEventListener("click", (e) => {
        if (e.target === backdrop) closeModal();
    });
    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape") closeModal();
    });

    /* ── Reabrir modal si hay errores de servidor ── */
    if (backdrop) {
        const hasServerErrors = backdrop.querySelector(".pwd-field__error") !== null;
        if (hasServerErrors) openModal();
    }

    /* ── Toggle visibilidad contraseña ── */
    document.querySelectorAll(".pwd-field__toggle").forEach((btn) => {
        btn.addEventListener("click", () => {
            const input = document.getElementById(btn.dataset.target);
            if (!input) return;
            const isText = input.type === "text";
            input.type = isText ? "password" : "text";
            btn.querySelector(".eye-icon--show").style.display = isText
                ? ""
                : "none";
            btn.querySelector(".eye-icon--hide").style.display = isText
                ? "none"
                : "";
        });
    });

    if (!pwdInput || !confirmInput) return;

    /* ── Fortaleza de contraseña ── */
    function getStrength(pwd) {
        let score = 0;
        if (pwd.length >= 8) score++;
        if (pwd.length >= 12) score++;
        if (/[A-Z]/.test(pwd)) score++;
        if (/[0-9]/.test(pwd)) score++;
        if (/[^A-Za-z0-9]/.test(pwd)) score++;
        return score;
    }
    const levels = [
        {
            label: "",
            color: "",
            pct: "0%",
        },
        {
            label: "Muy débil",
            color: "#ef4444",
            pct: "20%",
        },
        {
            label: "Débil",
            color: "#f97316",
            pct: "40%",
        },
        {
            label: "Regular",
            color: "#eab308",
            pct: "60%",
        },
        {
            label: "Fuerte",
            color: "#22c55e",
            pct: "80%",
        },
        {
            label: "Muy fuerte",
            color: "#16a34a",
            pct: "100%",
        },
    ];

    function resetStrength() {
        if (strengthFill) strengthFill.style.width = "0%";
        if (strengthFill) strengthFill.style.background = "";
        if (strengthLabel) strengthLabel.textContent = "";
        if (strengthLabel) strengthLabel.style.color = "";
    }
    pwdInput.addEventListener("input", () => {
        const v = pwdInput.value;
        if (!v) {
            resetStrength();
            return;
        }
        const s = Math.min(getStrength(v), 5);
        const l = levels[s];
        if (strengthFill) {
            strengthFill.style.width = l.pct;
            strengthFill.style.background = l.color;
        }
        if (strengthLabel) {
            strengthLabel.textContent = l.label;
            strengthLabel.style.color = l.color;
        }
        checkMatch();
    });

    /* ── Coincidencia contraseñas ── */
    function checkMatch() {
        if (!matchHint) return;
        if (!confirmInput.value) {
            matchHint.textContent = "";
            matchHint.className = "pwd-match-hint";
            return;
        }
        const match = pwdInput.value === confirmInput.value;
        matchHint.textContent = match
            ? "✓ Las contraseñas coinciden"
            : "✗ Las contraseñas no coinciden";
        matchHint.className =
            "pwd-match-hint " + (match ? "match" : "no-match");
    }
    confirmInput.addEventListener("input", checkMatch);
})();
