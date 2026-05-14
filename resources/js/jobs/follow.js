/* ═══════════════════════════════════════════════════════════
   resources/js/jobs/follow.js
   Seguir / dejar de seguir empresa desde el sidebar
═══════════════════════════════════════════════════════════ */

document.addEventListener('DOMContentLoaded', () => {

    document.addEventListener('click', async (e) => {
        const btn = e.target.closest('.btn-follow');
        if (!btn) return;

        const url        = btn.dataset.url;
        const csrf       = document.querySelector('meta[name="csrf-token"]')?.content;
        const isFollowing = btn.classList.contains('is-following');

        if (!url || !csrf) return;

        // ── Optimistic UI ────────────────────────────────────────────────
        _setFollowState(btn, !isFollowing);

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

            if (!res.ok) throw new Error('Error en la petición');

            const data = await res.json();
            _setFollowState(btn, data.following);

        } catch (err) {
            console.error('follow.js:', err);
            _setFollowState(btn, isFollowing); // revierte
            window.toast?.error('No se pudo completar la acción.');
        }
    });

    function _setFollowState(btn, following) {
        btn.classList.toggle('is-following', following);
        btn.textContent = following ? 'Siguiendo' : 'Seguir';
        btn.setAttribute('aria-pressed', String(following));
    }
});
