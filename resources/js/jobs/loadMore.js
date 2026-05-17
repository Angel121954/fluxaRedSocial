/* ═══════════════════════════════════════════════════════════
   resources/js/jobs/loadMore.js
   Cargar más ofertas — append de HTML + actualizar botón
═══════════════════════════════════════════════════════════ */

document.addEventListener('DOMContentLoaded', () => {

    const btn  = document.getElementById('btnLoadMore');
    const list = document.getElementById('jobsList');

    if (!btn || !list) return;

    btn.addEventListener('click', async () => {
        const url = btn.dataset.url;
        if (!url) return;

        btn.disabled = true;
        btn.textContent = 'Cargando…';

        try {
            const res = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                credentials: 'same-origin',
            });

            if (!res.ok) throw new Error('Error en la petición');

            const data = await res.json();
            const html = data.html;

            const temp = document.createElement('div');
            temp.innerHTML = html;

            // Extrae las tarjetas del HTML parcial devuelto
            const newCards = temp.querySelectorAll('.job-card');
            newCards.forEach(card => list.appendChild(card));

            // Actualiza la URL del botón con la siguiente página
            const nextBtn = temp.querySelector('#btnLoadMore');
            if (nextBtn) {
                btn.dataset.url = nextBtn.dataset.url;
                btn.disabled    = false;
                btn.innerHTML   = 'Ver más ofertas <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true" style="width:15px;height:15px;display:inline-block;vertical-align:-2px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>';
            } else {
                // No hay más páginas
                btn.closest('.jobs-load-more-wrap')?.remove();
            }

        } catch (err) {
            console.error('loadMore.js:', err);
            btn.disabled    = false;
            btn.textContent = 'Ver más ofertas';
            window.toast?.error('No se pudo cargar más ofertas. Intenta de nuevo.');
        }
    });
});
