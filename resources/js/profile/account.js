/**
 * profile/account.js — Configuración de cuenta + 2FA
 */

import '../settings/locationSelects.js';

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
            credentials: "same-origin",
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

    const openModal = () => modal2FA.classList.add("is-open");
    const closeModal = () => modal2FA.classList.remove("is-open");

    // ── Toggle 2FA ─────────────────────────────────────────────
    if (toggle2FA) {
        toggle2FA.closest(".toggle-switch")?.addEventListener("click", async (e) => {
            e.preventDefault();

            if (toggle2FA.checked) {
                await handleDisable2FA();
            } else {
                await handleEnable2FA();
            }
        });
    }

    async function handleEnable2FA() {
        await apiFetch("/user/two-factor-authentication", "DELETE").catch(() => { });

        const res = await apiFetch("/user/two-factor-authentication", "POST");

        if (!res.ok) {
            return;
        }

        toggle2FA.checked = true;

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

        openModal();
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
                allowOutsideClick: false,
            })).isConfirmed
            : confirm("Desactivar la autenticacion en dos pasos?");

        if (!confirmed) {
            return;
        }

        const res = await apiFetch("/user/two-factor-authentication", "DELETE");
        if (res.ok) {
            toggle2FA.checked = false;
            twoFaStatus.textContent = "desactivada";
        }
    }

    // ── Confirmar código OTP ───────────────────────────────────
    const btnConfirm2FA = document.getElementById("btnConfirm2FA");
    btnConfirm2FA?.addEventListener("click", async () => {
        if (btnConfirm2FA.disabled) return;

        const code = codeInput.value.replace(/\D/g, "").trim();

        if (code.length !== 6) {
            error2FA.textContent = "El codigo debe tener 6 digitos.";
            error2FA.style.display = "block";
            return;
        }

        error2FA.style.display = "none";
        btnConfirm2FA.disabled = true;

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
            btnConfirm2FA.disabled = false;
            error2FA.textContent = "Codigo incorrecto. Intenta de nuevo.";
            error2FA.style.display = "block";
            codeInput.value = "";
            codeInput.focus();
        }
    });

    function showRecoveryCodes(codes) {
        qrContainer.style.display = "none";
        const secretEl = document.querySelector(".modal-secret");
        if (secretEl) secretEl.style.display = "none";
        codeInput.style.display = "none";
        error2FA.style.display = "none";
        const btnCancel = document.getElementById("btnCancel2FA");
        const btnConfirm = document.getElementById("btnConfirm2FA");
        if (btnCancel) btnCancel.style.display = "none";
        if (btnConfirm) btnConfirm.style.display = "none";
        const subtitle = document.querySelector("#modal2FA .modal-subtitle");
        if (subtitle) subtitle.style.display = "none";

        modalTitle.textContent = "Guarda tus codigos de recuperacion";

        recoveryList.innerHTML = codes
            .map(c => `<code style="padding:2px 0">${c}</code>`)
            .join("");

        recoveryCodes.style.display = "block";

        const btnClose = document.getElementById("btnCloseModal");
        if (btnClose) {
            btnClose.style.display = "";
            btnClose.onclick = () => {
                closeModal();
                resetModalView();
            };
        }
    }

    function resetModalView() {
        qrContainer.style.display = "";
        const secretEl = document.querySelector(".modal-secret");
        if (secretEl) secretEl.style.display = "";
        codeInput.style.display = "";
        error2FA.style.display = "none";
        modalActions.style.display = "";
        const btnCancel = document.getElementById("btnCancel2FA");
        const btnConfirm = document.getElementById("btnConfirm2FA");
        if (btnCancel) btnCancel.style.display = "";
        if (btnConfirm) {
            btnConfirm.style.display = "";
            btnConfirm.disabled = false;
        }
        const subtitle = document.querySelector("#modal2FA .modal-subtitle");
        if (subtitle) subtitle.style.display = "";
        recoveryCodes.style.display = "none";
        const btnClose = document.getElementById("btnCloseModal");
        if (btnClose) btnClose.style.display = "none";
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
    const cancel2FA = () => {
        closeModal();
        resetModalView();
    };

    document.getElementById("btnCancel2FA")?.addEventListener("click", cancel2FA);
    document.getElementById("btnClose2FAHeader")?.addEventListener("click", cancel2FA);

    // Click en overlay cierra modal
    modal2FA?.addEventListener("click", (e) => {
        if (e.target === modal2FA) {
            cancel2FA();
        }
    });

    // Escape cierra modal
    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape" && modal2FA?.classList.contains("is-open")) {
            cancel2FA();
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