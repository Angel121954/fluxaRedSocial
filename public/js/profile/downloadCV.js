/**
 * Convierte una URL de imagen a base64 PNG.
 * - SVGs: fetch → Blob URL → dibuja en canvas → PNG base64
 *   (html2canvas NO puede renderizar SVGs, ni siquiera en base64)
 * - PNG/JPG: canvas directo
 */
async function urlAPngBase64(url) {
    return new Promise(async (resolve) => {
        try {
            // 1. Obtener el recurso como blob (evita CORS con fetch)
            const respuesta = await fetch(url);
            const blob = await respuesta.blob();
            const blobUrl = URL.createObjectURL(blob);

            const img = new Image();
            img.onload = () => {
                const canvas = document.createElement('canvas');
                // Para SVGs sin dimensiones intrínsecas usamos un tamaño fijo
                canvas.width = img.naturalWidth || 64;
                canvas.height = img.naturalHeight || 64;
                canvas.getContext('2d').drawImage(img, 0, 0, canvas.width, canvas.height);
                URL.revokeObjectURL(blobUrl);
                resolve(canvas.toDataURL('image/png'));
            };
            img.onerror = () => {
                URL.revokeObjectURL(blobUrl);
                resolve(url); // fallback: URL original
            };
            img.src = blobUrl;
        } catch (e) {
            console.warn('No se pudo convertir imagen:', url, e);
            resolve(url); // fallback: URL original
        }
    });
}

/**
 * Convierte todos los <img> del nodo a PNG base64 en paralelo.
 */
async function convertirImagenesAPng(nodo) {
    const imgs = Array.from(nodo.querySelectorAll('img'));
    await Promise.allSettled(
        imgs.map(async (img) => {
            if (!img.src || img.src.startsWith('data:')) return;
            img.src = await urlAPngBase64(img.src);
        })
    );
}

async function downloadCV() {
    const fuente = document.getElementById('cv-template').firstElementChild;
    const clon = fuente.cloneNode(true);

    const btn = document.querySelector('[onclick="downloadCV()"]');
    const original = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = `
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
             stroke="currentColor" width="18" height="18" style="animation:spin 1s linear infinite">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0
                     3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1
                     13.803-3.7l3.181 3.182m0-4.991v4.99" />
        </svg> Generando...
    `;

    // Convierte TODOS los <img> a PNG base64 antes de html2canvas
    // Esto incluye: avatar, íconos del stack, logo Fluxa, QR
    await convertirImagenesAPng(clon);

    html2pdf().set({
        margin: 0,
        filename: `CV_${CV_USERNAME}.pdf`,
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: {
            scale: 2,
            useCORS: true,
            allowTaint: true,
            backgroundColor: '#ffffff',
            width: 900,
        },
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
    }).from(clon).save().then(() => {
        btn.disabled = false;
        btn.innerHTML = original;
    });
}