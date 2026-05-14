/* ═══════════════════════════════════════════════════════════
   resources/js/jobs/bookmark.js
   Guardar / quitar oferta — optimistic UI + fetch
═══════════════════════════════════════════════════════════ */

document.addEventListener('DOMContentLoaded', () => {

    const SVG_OUTLINE = `
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
        </svg>`;

    const SVG_FILLED = `
        <svg fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path d="M5 4a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 20V4z"/>
        </svg>`;

    // Delegación en el document para cubrir cards cargadas con "Cargar más"
    document.addEventListener('click', async (e) => {
        const btn = e.target.closest('.btn-bookmark');
        if (!btn) return;

        e.preventDefault();

        const jobId   = btn.dataset.jobId;
        const url     = btn.dataset.url;
        const isSaved = btn.classList.contains('is-saved');
        const csrf    = document.querySelector('meta[name="csrf-token"]')?.content;

        if (!jobId || !url || !csrf) return;

        // ── Optimistic UI ────────────────────────────────────────────────
        _toggleBtn(btn, !isSaved);

        try {
            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                credentials: 'same-origin',
                body: JSON.stringify({ job_id: jobId }),
            });

            if (!res.ok) throw new Error('Error en la petición');

            const data = await res.json();

            // Confirma el estado real que devuelve el servidor
            _toggleBtn(btn, data.saved);

            window.toast?.success(data.saved ? 'Oferta guardada' : 'Oferta eliminada de guardados');

        } catch (err) {
            console.error('bookmark.js:', err);
            // Revierte si falló
            _toggleBtn(btn, isSaved);
            window.toast?.error('No se pudo completar la acción. Intenta de nuevo.');
        }
    });

    // ── Helper ─────────────────────────────────────────────────────────────
    function _toggleBtn(btn, saved) {
        btn.classList.toggle('is-saved', saved);
        btn.setAttribute('aria-pressed', String(saved));
        btn.setAttribute('aria-label', saved ? 'Quitar de guardados' : 'Guardar oferta');
        btn.innerHTML = saved ? SVG_FILLED : SVG_OUTLINE;
    }
});
