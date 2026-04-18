import { showToast } from '../shared/toast.js';

async function compartirPerfil(username, name) {
    const url = `${window.location.origin}/profile/${username}`;
    const title = `${name} en Fluxa`;
    const text = `Mira el perfil de ${name} en Fluxa: builder construyendo en público.`;

    // Web Share API → solo en móvil (falla silenciosamente en desktop sin HTTPS)
    const isMobile = /Mobi|Android/i.test(navigator.userAgent);
    if (navigator.share && isMobile) {
        try {
            await navigator.share({ title, text, url });
        } catch (err) {
            if (err.name !== 'AbortError') console.error(err);
        }
        return;
    }

    // Fallback → copiar al portapapeles
    try {
        await navigator.clipboard.writeText(url);
        showToast('¡Enlace copiado al portapapeles!', 'success');
    } catch {
        // Fallback legacy para HTTP o navegadores sin clipboard API
        const input = document.createElement('input');
        input.value = url;
        document.body.appendChild(input);
        input.select();
        document.execCommand('copy');
        document.body.removeChild(input);
        showToast('¡Enlace copiado al portapapeles!', 'success');
    }
}

window.compartirPerfil = compartirPerfil;
