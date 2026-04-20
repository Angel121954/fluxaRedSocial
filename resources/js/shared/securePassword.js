// Mostrar/ocultar contraseña
function togglePassword(fieldId, btn) {
    const input = document.getElementById(fieldId);
    if (!input) return;
    const isPassword = input.type === "password";
    input.type = isPassword ? "text" : "password";

    // Cambiar icono (ojo abierto / ojo cerrado)
    btn.querySelector("svg").style.opacity = isPassword ? "0.5" : "1";
}

window.togglePassword = togglePassword;

// Indicador de fortaleza de contraseña
const passwordInput = document.getElementById("password");
if (passwordInput) {
    passwordInput.addEventListener("input", function () {
        const val = this.value;
        const fill = document.getElementById("strength-fill");
        const label = document.getElementById("strength-label");
        const bar = document.getElementById("strength-bar");

        if (!val) {
            if (bar) bar.style.display = "none";
            return;
        }
        if (bar) bar.style.display = "flex";

        let score = 0;
        if (val.length >= 8) score++;
        if (/[A-Z]/.test(val)) score++;
        if (/[0-9]/.test(val)) score++;
        if (/[^A-Za-z0-9]/.test(val)) score++;

        const levels = [
            {
                pct: "25%",
                color: "#ef4444",
                text: "Muy débil",
            },
            {
                pct: "50%",
                color: "#f97316",
                text: "Débil",
            },
            {
                pct: "75%",
                color: "#eab308",
                text: "Regular",
            },
            {
                pct: "100%",
                color: "#22c55e",
                text: "Fuerte",
            },
        ];

        const level = levels[score - 1] || levels[0];
        if (fill) {
            fill.style.width = level.pct;
            fill.style.background = level.color;
        }
        if (label) {
            label.textContent = level.text;
            label.style.color = level.color;
        }
    });
}
