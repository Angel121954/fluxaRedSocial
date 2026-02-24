async function compartirPerfil(username, name) {
    const url = `${window.location.origin}/profile/${username}`;
    const title = `${name} en Fluxa`;
    const text = `Mira el perfil de ${name} en Fluxa: builder construyendo en público.`;

    // Web Share API → móvil / navegadores modernos
    if (navigator.share) {
        try {
            await navigator.share({ title, text, url });
        } catch (err) {
            // El usuario canceló — no hacer nada
            if (err.name !== "AbortError") console.error(err);
        }
        return;
    }

    // Fallback → copiar al portapapeles
    try {
        await navigator.clipboard.writeText(url);
        mostrarToast("¡Enlace copiado al portapapeles!");
    } catch {
        // Fallback legacy para HTTP o navegadores sin clipboard API
        const input = document.createElement("input");
        input.value = url;
        document.body.appendChild(input);
        input.select();
        document.execCommand("copy");
        document.body.removeChild(input);
        mostrarToast("¡Enlace copiado al portapapeles!");
    }
}

function mostrarToast(mensaje) {
    const toast = document.getElementById("share-toast");
    const msg = document.getElementById("share-toast-msg");

    msg.textContent = mensaje;
    toast.classList.remove("hidden");
    toast.classList.add("show");

    setTimeout(() => {
        toast.classList.remove("show");
        setTimeout(() => toast.classList.add("hidden"), 300);
    }, 2500);
}
