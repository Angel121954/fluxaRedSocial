/**
 * profile/account.js — Configuración de cuenta + 2FA
 */

document.addEventListener("DOMContentLoaded", () => {

    // ── Helpers ────────────────────────────────────────────────
    const csrf = () =>
        document.querySelector('meta[name="csrf-token"]')?.content ?? "";

    const apiFetch = (url, method = "GET", body = null) =>
        fetch(url, {
            method,
            headers: {
                "X-CSRF-TOKEN": csrf(),
                "Content-Type": "application/json",
                Accept: "application/json",
            },
            ...(body ? { body: JSON.stringify(body) } : {}),
        });

    // ── 2FA refs ───────────────────────────────────────────────
    const toggle2FA = document.getElementById("toggle2FA");
    const twoFaStatus = document.getElementById("twoFaStatus");
    const modal2FA = document.getElementById("modal2FA");
    const qrContainer = document.getElementById("qrContainer");
    const secretKeyEl = document.getElementById("secretKey");
    const codeInput = document.getElementById("codeInput2FA");
    const error2FA = document.getElementById("error2FA");
    const modalTitle = document.getElementById("modal2FATitle");
    const modalActions = document.getElementById("modalActions");
    const recoveryCodes = document.getElementById("recoveryCodes");
    const recoveryList = document.getElementById("recoveryList");

    // ── Toggle 2FA ─────────────────────────────────────────────
    if (toggle2FA) {
        toggle2FA.addEventListener("change", async () => {
            if (toggle2FA.checked) {
                await handleEnable2FA();
            } else {
                await handleDisable2FA();
            }
        });
    }

    async function handleEnable2FA() {
        await apiFetch("/user/two-factor-authentication", "DELETE").catch(() => { });

        const res = await apiFetch("/user/two-factor-authentication", "POST");

        if (!res.ok) {
            toggle2FA.checked = false;
            return;
        }

        const [qrRes, keyRes] = await Promise.all([
            apiFetch("/user/two-factor-qr-code"),
            apiFetch("/user/two-factor-secret-key"),
        ]);

        const { svg } = await qrRes.json();
        const { secretKey: key } = await keyRes.json();

        qrContainer.innerHTML = svg;
        secretKeyEl.textContent = key;
        codeInput.value = "";
        error2FA.style.display = "none";

        resetModalView();

        modal2FA.style.display = "flex";
        setTimeout(() => codeInput.focus(), 100);
    }

    async function handleDisable2FA() {
        const confirmed = typeof Swal !== "undefined"
            ? (await Swal.fire({
                title: "Desactivar autenticacion en dos pasos",
                text: "Tu cuenta quedara menos protegida.",
                icon: "warning",
                confirmButtonText: "Desactivar",
                cancelButtonText: "Cancelar",
                showCancelButton: true,
                confirmButtonColor: "#dc2626",
            })).isConfirmed
            : confirm("Desactivar la autenticacion en dos pasos?");

        if (!confirmed) {
            toggle2FA.checked = true;
            return;
        }

        const res = await apiFetch("/user/two-factor-authentication", "DELETE");
        if (res.ok) {
            twoFaStatus.textContent = "desactivada";
        } else {
            toggle2FA.checked = true;
        }
    }

    // ── Confirmar código OTP ───────────────────────────────────
    document.getElementById("btnConfirm2FA")?.addEventListener("click", async () => {
        const code = codeInput.value.replace(/\D/g, "").trim();

        if (code.length !== 6) {
            error2FA.textContent = "El codigo debe tener 6 digitos.";
            error2FA.style.display = "block";
            return;
        }

        error2FA.style.display = "none";

        const res = await apiFetch(
            "/user/confirmed-two-factor-authentication",
            "POST",
            { code }
        );

        if (res.ok) {
            twoFaStatus.textContent = "activada";

            const codesRes = await apiFetch("/user/two-factor-recovery-codes");
            const codes = await codesRes.json();
            showRecoveryCodes(codes);
        } else {
            error2FA.textContent = "Codigo incorrecto. Intenta de nuevo.";
            error2FA.style.display = "block";
            codeInput.value = "";
            codeInput.focus();
        }
    });

    function showRecoveryCodes(codes) {
        qrContainer.style.display = "none";
        document.querySelector(".modal-secret").style.display = "none";
        codeInput.style.display = "none";
        error2FA.style.display = "none";
        modalActions.style.display = "none";
        document.querySelector("#modal2FA p").style.display = "none";

        modalTitle.textContent = "Guarda tus codigos de recuperacion";

        recoveryList.innerHTML = codes
            .map(c => `<code style="padding:2px 0">${c}</code>`)
            .join("");

        recoveryCodes.style.display = "block";

        const btnClose = document.getElementById("btnCloseModal");
        if (btnClose) btnClose.onclick = () => {
            modal2FA.style.display = "none";
            resetModalView();
        };
    }

    function resetModalView() {
        qrContainer.style.display = "";
        document.querySelector(".modal-secret").style.display = "";
        codeInput.style.display = "";
        error2FA.style.display = "none";
        modalActions.style.display = "";
        document.querySelector("#modal2FA p").style.display = "";
        recoveryCodes.style.display = "none";
        if (modalTitle) modalTitle.textContent = "Escanea el codigo QR";
    }

    // Solo números
    codeInput?.addEventListener("input", () => {
        codeInput.value = codeInput.value.replace(/\D/g, "");
    });

    // Enter → confirmar
    codeInput?.addEventListener("keydown", (e) => {
        if (e.key === "Enter") document.getElementById("btnConfirm2FA")?.click();
    });

    // ── Cancelar modal ─────────────────────────────────────────
    document.getElementById("btnCancel2FA")?.addEventListener("click", () => {
        modal2FA.style.display = "none";
        toggle2FA.checked = false;
        resetModalView();
        apiFetch("/user/two-factor-authentication", "DELETE");
    });

    // Click en overlay cierra modal
    modal2FA?.addEventListener("click", (e) => {
        if (e.target === modal2FA) {
            document.getElementById("btnCancel2FA")?.click();
        }
    });

    // ── Btn "Configurar ahora" ─────────────────────────────────
    document.querySelector(".btn-setup-2fa")?.addEventListener("click", () => {
        const method = document.querySelector('input[name="twoFaMethod"]:checked')?.value;
        if (method === "app") {
            handleEnable2FA();
        }
    });

    // ── Sidebar active state ───────────────────────────────────
    document.querySelectorAll(".sidebar-item").forEach((item) => {
        item.addEventListener("click", function () {
            document.querySelectorAll(".sidebar-item")
                .forEach((i) => i.classList.remove("active"));
            this.classList.add("active");
        });
    });
});