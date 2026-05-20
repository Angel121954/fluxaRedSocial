/* ═══════════════════════════════════════════════════════════
   resources/js/diary/reply.js
   Publicar respuesta al Diario + "Cargar más"
══════════════════════════════════════════════════════════ */

export function initDiaryReply() {
    const input   = document.getElementById('diary-reply-input');
    const btn     = document.getElementById('diary-reply-btn');
    const list    = document.getElementById('diary-responses-list');

    if (!input || !btn) return;

    // ── Habilitar/deshabilitar botón según contenido ──────────────────
    input.addEventListener('input', () => {
        const hasText = input.value.trim().length > 0;
        btn.disabled = !hasText;

        // Auto-resize textarea
        input.style.height = 'auto';
        input.style.height = `${input.scrollHeight}px`;
    });

    // ── Publicar respuesta ────────────────────────────────────────────
    btn.addEventListener('click', async () => {
        const content = input.value.trim();
        if (!content) return;

        const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
        const url  = btn.dataset.url;

        if (!csrf || !url) return;

        btn.disabled = true;
        btn.textContent = 'Publicando...';

        try {
            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                credentials: 'same-origin',
                body: JSON.stringify({ content }),
            });

            if (!res.ok) throw new Error('Error al publicar');

            const data = await res.json();

            // Limpiar input
            input.value = '';
            input.style.height = 'auto';

            // Insertar la nueva respuesta al inicio de la lista
            if (list && data.html) {
                const empty = list.querySelector('.diary-empty');
                if (empty) empty.remove();

                list.insertAdjacentHTML('afterbegin', data.html);
            }

            // Actualizar contador de respondentes
            _updateRespondentCount(data.responses_count);

        } catch (err) {
            console.error('Error publicando respuesta del Diario:', err);
        } finally {
            btn.innerHTML = `
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <line x1="22" y1="2" x2="11" y2="13"/>
                    <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                </svg>
                Publicar
            `;
            btn.disabled = true;
        }
    });
}

// ── Cargar más respuestas ─────────────────────────────────────────────
export function initLoadMore() {
    const loadMoreBtn = document.getElementById('diary-load-more');
    const list        = document.getElementById('diary-responses-list');

    if (!loadMoreBtn || !list) return;

    loadMoreBtn.addEventListener('click', async () => {
        const url = loadMoreBtn.dataset.nextUrl;
        if (!url) return;

        loadMoreBtn.disabled = true;
        loadMoreBtn.textContent = 'Cargando...';

        try {
            const res = await fetch(url, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin',
            });

            if (!res.ok) throw new Error('Error cargando respuestas');

            const data = await res.json();

            if (data.html) {
                list.insertAdjacentHTML('beforeend', data.html);
            }

            if (data.next_page_url) {
                loadMoreBtn.dataset.nextUrl = data.next_page_url;
                loadMoreBtn.disabled = false;
                loadMoreBtn.textContent = 'Cargar más respuestas';
            } else {
                loadMoreBtn.closest('.diary-load-more-wrap')?.remove();
            }

        } catch (err) {
            console.error('Error al cargar más respuestas:', err);
            loadMoreBtn.disabled = false;
            loadMoreBtn.textContent = 'Cargar más respuestas';
        }
    });
}

// ── Helpers internos ──────────────────────────────────────────────────
function _updateRespondentCount(count) {
    const countEl = document.querySelector('.diary-respondents__count');
    if (!countEl || count == null) return;

    const formatted = Number(count).toLocaleString('es');
    const label     = count === 1 ? 'persona ya respondió' : 'personas ya respondieron';
    countEl.textContent = `${formatted} ${label}`;
}
