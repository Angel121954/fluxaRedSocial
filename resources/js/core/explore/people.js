/* ═══════════════════════════════════════════════════════════
   people.js — Seguir / dejar de seguir desde el directorio
   Depende de: SweetAlert2 no; usa window.toast si existe.
═══════════════════════════════════════════════════════════ */

document.addEventListener('DOMContentLoaded', () => {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    if (!csrf) return;

    document.addEventListener('click', async (event) => {
        const button = event.target.closest('.person-card__follow[data-url]');
        if (!button || button.dataset.loading === 'true') return;

        const wasFollowing = button.classList.contains('is-following');
        const counter = button.closest('.person-card')?.querySelector('[data-followers-count]');
        const previousCount = counter ? Number(counter.textContent) : null;

        setFollowState(button, !wasFollowing);
        button.dataset.loading = 'true';
        button.disabled = true;

        try {
            const response = await fetch(button.dataset.url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                credentials: 'same-origin',
            });

            if (!response.ok) {
                throw new Error('No se pudo actualizar el seguimiento');
            }

            const data = await response.json();
            setFollowState(button, Boolean(data.following));

            if (counter && data.followers_count !== undefined) {
                counter.textContent = data.followers_count;
            }
        } catch (error) {
            setFollowState(button, wasFollowing);

            if (counter && previousCount !== null) {
                counter.textContent = previousCount;
            }

            window.toast?.error('No se pudo completar la acción.');
        } finally {
            delete button.dataset.loading;
            button.disabled = false;
        }
    });

    function setFollowState(button, following) {
        button.classList.toggle('is-following', following);
        button.textContent = following ? 'Siguiendo' : 'Seguir';
        button.setAttribute('aria-pressed', String(following));
    }
});
