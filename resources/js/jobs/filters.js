/* ═══════════════════════════════════════════════════════════
   resources/js/jobs/filters.js
   Pills de filtro rápido: activan/desactivan y re-envían el form
═══════════════════════════════════════════════════════════ */

document.addEventListener('DOMContentLoaded', () => {

    const container = document.getElementById('jobQuickFilters');
    if (!container) return;

    const form    = document.getElementById('jobsSearchForm');
    const sortEl  = document.getElementById('jobsSort');

    // ── Toggle de pills ──────────────────────────────────────────────────
    container.addEventListener('click', (e) => {
        const pill = e.target.closest('.jobs-pill[data-filter]');
        if (!pill) return;

        pill.classList.toggle('is-active');

        // Reconstruye los inputs hidden para tags[] antes de enviar
        _syncTagInputs();

        // Reenvía el form automáticamente al cambiar un filtro rápido
        form?.submit();
    });

    // ── Re-envío al cambiar el select de orden ────────────────────────────
    sortEl?.addEventListener('change', () => form?.submit());

    // ── Helpers ───────────────────────────────────────────────────────────

    /**
     * Elimina los inputs hidden previos y crea uno por cada pill activa,
     * para que el form envíe tags[] en GET correctamente.
     */
    function _syncTagInputs() {
        form?.querySelectorAll('input[name="tags[]"]').forEach(el => el.remove());

        container.querySelectorAll('.jobs-pill.is-active[data-filter]').forEach(pill => {
            const input = document.createElement('input');
            input.type  = 'hidden';
            input.name  = 'tags[]';
            input.value = pill.dataset.filter;
            form?.appendChild(input);
        });
    }
});
