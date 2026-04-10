// account.js
document.addEventListener("DOMContentLoaded", () => {
    // 2FA Toggle
    const toggle2FA = document.getElementById("toggle2FA");
    const twoFaOptions = document.getElementById("twoFaOptions");

    // 👇 verifica que exista antes de usarlo
    if (toggle2FA) {
        toggle2FA.addEventListener("change", () => {
            const hint = toggle2FA
                .closest(".toggle-row")
                .querySelector(".toggle-hint strong");
            const isEnabled = toggle2FA.checked;

            twoFaOptions.style.display = isEnabled ? "flex" : "none";
            hint.textContent = isEnabled ? "activada" : "desactivada";
        });
    }

    document.querySelector(".btn-setup-2fa")?.addEventListener("click", () => {
        const method = document.querySelector(
            'input[name="twoFaMethod"]:checked',
        )?.value;
        const label = method === "app" ? "Aplicación de autenticación" : "SMS";
        alert(`⚙️ Configurando 2FA con: ${label}`);
    });

    document.getElementById("btnDeactivate")?.addEventListener("click", () => {
        const confirmed = confirm(
            "¿Desactivar tu cuenta? Tu perfil dejará de ser visible hasta que vuelvas a iniciar sesión.",
        );
        if (confirmed) {
        }
    });

    document.querySelectorAll(".sidebar-item").forEach((item) => {
        item.addEventListener("click", function () {
            document
                .querySelectorAll(".sidebar-item")
                .forEach((i) => i.classList.remove("active"));
            this.classList.add("active");
        });
    });
});
