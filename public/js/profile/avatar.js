const avatarWrap = document.getElementById("avatarWrap");
const avatarImg = document.getElementById("avatarImg");
const btnCam = document.querySelector(".btnCam");
const btnView = document.querySelector(".btnView");
const btnDelete = document.getElementById("btnDelete");
const fileIn = document.getElementById("fileIn");
const imgModal = document.getElementById("imgModal");
const modalImg = document.getElementById("modalImg");
const modalX = document.getElementById("modalX");

const DEFAULT_AVATAR = "/img/default-avatar.png";

// <meta name="csrf-token" content="{{ csrf_token() }}">
const csrfToken =
    document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute("content") ?? "";

const isMobile = () => window.innerWidth <= 680;

/* ── Toggle overlay en móvil ── */
if (avatarWrap && avatarImg) {
    avatarWrap.addEventListener("click", (e) => {
        if (isMobile() && (e.target === avatarWrap || e.target === avatarImg)) {
            avatarWrap.classList.toggle("active");
        }
    });

    document.addEventListener("click", (e) => {
        if (isMobile() && !avatarWrap.contains(e.target)) {
            avatarWrap.classList.remove("active");
        }
    });
}

/* ── Cambiar foto ── */
if (btnCam && fileIn) {
    btnCam.addEventListener("click", (e) => {
        e.stopPropagation();
        avatarWrap?.classList.remove("active");
        fileIn.click();
    });

    fileIn.addEventListener("change", async (e) => {
        const file = e.target.files[0];
        if (!file) return;

        // Preview inmediato antes de esperar al servidor
        const localUrl = URL.createObjectURL(file);
        setAllAvatars(localUrl);

        await uploadAvatar(file);

        fileIn.value = "";
    });
}

/* ── Eliminar foto ── */
if (btnDelete) {
    btnDelete.addEventListener("click", async () => {
        const result = await Swal.fire({
            title: "¿Eliminar foto de perfil?",
            text: "Esta acción no se puede deshacer.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Sí, eliminar",
            cancelButtonText: "Cancelar",
            confirmButtonColor: "#ef4444",
            cancelButtonColor: "#12b3b6",
            customClass: {
                popup: "swal-fluxa",
                title: "swal-fluxa__title",
                htmlContainer: "swal-fluxa__text",
                cancelButton: "swal-fluxa__cancel",
                icon: "swal-fluxa__icon",
            },
            reverseButtons: true,
        });

        if (!result.isConfirmed) return;

        try {
            const response = await fetch("/profile/avatar", {
                method: "DELETE",
                headers: {
                    "X-CSRF-TOKEN": csrfToken,
                    Accept: "application/json",
                },
            });

            const data = await response.json();
            if (!response.ok || !data.success) throw new Error(data.message);

            setAllAvatars(DEFAULT_AVATAR);
            showToast("Foto eliminada correctamente", "success");
        } catch (err) {
            console.error("destroyAvatar:", err);
            showToast(err.message ?? "No se pudo eliminar la foto", "error");
        }
    });
}

/**
 * Sube el avatar al servidor y actualiza la UI con la URL de Cloudinary.
 * @param {File} file
 */
async function uploadAvatar(file) {
    setAvatarLoading(true);

    const formData = new FormData();
    formData.append("avatar", file);
    formData.append("_token", csrfToken);

    try {
        const response = await fetch("/profile/avatar", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": csrfToken,
                Accept: "application/json",
            },
            body: formData,
        });

        const data = await response.json();

        if (!response.ok || !data.success) {
            throw new Error(data.message ?? "Error al subir la imagen");
        }

        // Reemplazar el blob local por la URL definitiva de Cloudinary
        setAllAvatars(data.url);
        showToast("Avatar actualizado correctamente", "success");
    } catch (err) {
        console.error("uploadAvatar:", err);
        showToast(err.message ?? "No se pudo actualizar el avatar", "error");

        // Revertir al avatar anterior
        if (avatarImg?.dataset.originalSrc) {
            setAllAvatars(avatarImg.dataset.originalSrc);
        }
    } finally {
        setAvatarLoading(false);
    }
}

/* ── Helpers UI ── */

/**
 * Actualiza el src de todas las imágenes de avatar en la página.
 * @param {string} url
 */
function setAllAvatars(url) {
    if (avatarImg) avatarImg.src = url;
    if (modalImg) modalImg.src = url;
    document.querySelectorAll(".nav-user-av").forEach((img) => (img.src = url));
}

/**
 * Muestra/oculta un estado de carga sobre el avatar.
 * @param {boolean} loading
 */
function setAvatarLoading(loading) {
    if (!avatarWrap) return;
    avatarWrap.classList.toggle("loading", loading);
    if (btnCam) btnCam.disabled = loading;
    if (btnDelete) btnDelete.disabled = loading;
}

/**
 * Muestra una notificación flotante simple.
 * @param {string} message
 * @param {"success"|"error"} type
 */
function showToast(message, type = "success") {
    let toast = document.getElementById("toast");

    if (!toast) {
        toast = document.createElement("div");
        toast.id = "toast";
        Object.assign(toast.style, {
            position: "fixed",
            bottom: "1.5rem",
            right: "1.5rem",
            padding: "0.75rem 1.25rem",
            borderRadius: "0.5rem",
            color: "#fff",
            fontSize: "0.9rem",
            zIndex: "9999",
            opacity: "0",
            transition: "opacity 0.3s",
        });
        document.body.appendChild(toast);
    }

    toast.textContent = message;
    toast.style.backgroundColor = type === "success" ? "#22c55e" : "#ef4444";
    toast.style.opacity = "1";

    clearTimeout(toast._hideTimer);
    toast._hideTimer = setTimeout(() => {
        toast.style.opacity = "0";
    }, 3000);
}

/* ── Modal ── */
const closeModal = () => {
    imgModal?.classList.remove("show");
    document.body.style.overflow = "";
};

if (btnView && imgModal) {
    btnView.addEventListener("click", (e) => {
        e.stopPropagation();
        avatarWrap?.classList.remove("active");
        imgModal.classList.add("show");
        document.body.style.overflow = "hidden";
    });

    imgModal.addEventListener("click", (e) => {
        if (e.target === imgModal) closeModal();
    });
}

if (modalX) modalX.addEventListener("click", closeModal);

document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") closeModal();
});
