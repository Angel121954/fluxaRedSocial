/* ═══════════════════════════════════════════════════════════
   resources/js/diary/interactions.js
   Like, bookmark, menú contextual y eliminar respuesta
══════════════════════════════════════════════════════════ */

export function initDiaryInteractions() {
    _initLikes();
    _initBookmarks();
    _initMenus();
    _initDelete();
}

// ── Likes (optimistic UI) ─────────────────────────────────────────────
function _initLikes() {
    document.addEventListener('click', async (e) => {
        const btn = e.target.closest('.diary-like-btn');
        if (!btn) return;

        e.preventDefault();

        const csrf  = document.querySelector('meta[name="csrf-token"]')?.content;
        const url   = btn.dataset.url;
        if (!csrf || !url) return;

        const countEl = btn.querySelector('.diary-like-btn__count');
        const svg     = btn.querySelector('svg');
        const isLiked = btn.classList.contains('is-liked');
        const count   = parseInt(countEl?.textContent.replace(/\D/g, '') || '0', 10);

        // Optimistic UI
        btn.classList.toggle('is-liked');
        btn.setAttribute('aria-pressed', String(!isLiked));
        if (svg) svg.setAttribute('fill', isLiked ? 'none' : 'currentColor');
        if (countEl) countEl.textContent = _formatCount(isLiked ? count - 1 : count + 1);

        try {
            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                credentials: 'same-origin',
            });

            if (!res.ok) throw new Error('Error en like');

            const data = await res.json();
            if (countEl && data.likes_count != null) {
                countEl.textContent = _formatCount(data.likes_count);
            }

        } catch (err) {
            console.error('Error al registrar like:', err);
            // Revertir
            btn.classList.toggle('is-liked');
            btn.setAttribute('aria-pressed', String(isLiked));
            if (svg) svg.setAttribute('fill', isLiked ? 'currentColor' : 'none');
            if (countEl) countEl.textContent = _formatCount(count);
        }
    });
}

// ── Bookmarks (optimistic UI) ─────────────────────────────────────────
function _initBookmarks() {
    document.addEventListener('click', async (e) => {
        const btn = e.target.closest('.diary-bookmark-btn');
        if (!btn) return;

        e.preventDefault();

        const csrf    = document.querySelector('meta[name="csrf-token"]')?.content;
        const url     = btn.dataset.url;
        if (!csrf || !url) return;

        const isSaved = btn.classList.contains('is-saved');
        const svg     = btn.querySelector('svg');

        // Optimistic UI
        btn.classList.toggle('is-saved');
        btn.setAttribute('aria-pressed', String(!isSaved));
        if (svg) svg.setAttribute('fill', isSaved ? 'none' : 'currentColor');

        try {
            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                credentials: 'same-origin',
            });

            if (!res.ok) throw new Error('Error al guardar');

        } catch (err) {
            console.error('Error al guardar respuesta:', err);
            // Revertir
            btn.classList.toggle('is-saved');
            btn.setAttribute('aria-pressed', String(isSaved));
            if (svg) svg.setAttribute('fill', isSaved ? 'currentColor' : 'none');
        }
    });
}

// ── Menús contextuales ────────────────────────────────────────────────
function _initMenus() {
    // Abrir/cerrar al hacer click en el botón
    document.addEventListener('click', (e) => {
        const menuBtn = e.target.closest('.diary-menu-btn');

        // Cerrar todos primero
        document.querySelectorAll('.diary-response-menu.is-open').forEach(m => {
            m.classList.remove('is-open');
            m.previousElementSibling?.setAttribute('aria-expanded', 'false');
        });

        document.querySelectorAll('.diary-sort-dropdown.is-open').forEach(d => {
            d.classList.remove('is-open');
            d.previousElementSibling?.setAttribute('aria-expanded', 'false');
        });

        if (menuBtn) {
            e.stopPropagation();
            const wrap   = menuBtn.closest('.diary-response-card__menu-wrap');
            const menu   = wrap?.querySelector('.diary-response-menu');
            const isOpen = menu?.classList.contains('is-open');
            if (menu && !isOpen) {
                menu.classList.add('is-open');
                menuBtn.setAttribute('aria-expanded', 'true');
            }
        }

        // Sort dropdown
        const sortBtn = e.target.closest('#diary-sort-toggle');
        if (sortBtn) {
            e.stopPropagation();
            const dropdown = document.getElementById('diary-sort-dropdown');
            const isOpen   = dropdown?.classList.contains('is-open');
            if (dropdown) {
                dropdown.classList.toggle('is-open', !isOpen);
                sortBtn.setAttribute('aria-expanded', String(!isOpen));
            }
        }
    });
}

// ── Eliminar respuesta ────────────────────────────────────────────────
function _initDelete() {
    document.addEventListener('click', async (e) => {
        const btn = e.target.closest('.diary-delete-btn');
        if (!btn) return;

        if (!confirm('¿Eliminar tu respuesta? Esta acción no se puede deshacer.')) return;

        const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
        const url  = btn.dataset.url;
        if (!csrf || !url) return;

        try {
            const res = await fetch(url, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json',
                },
                credentials: 'same-origin',
            });

            if (!res.ok) throw new Error('Error al eliminar');

            const card = btn.closest('.diary-response-card');
            card?.remove();

        } catch (err) {
            console.error('Error al eliminar respuesta:', err);
        }
    });
}

// ── Utilidades ────────────────────────────────────────────────────────
function _formatCount(n) {
    if (n >= 1000) return (n / 1000).toFixed(1).replace('.0', '') + 'K';
    return String(n);
}
