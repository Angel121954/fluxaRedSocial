export function initBlockHandler() {
    const blockBtn = document.getElementById('msgsBlockBtn');
    if (!blockBtn) return;

    const blockIcon = blockBtn.querySelector('.msgs-block-icon');
    const unblockIcon = blockBtn.querySelector('.msgs-unblock-icon');

    blockBtn.addEventListener('click', async () => {
        const userId = blockBtn.dataset.userId;
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

        blockBtn.disabled = true;

        try {
            const res = await fetch(`/messages/${userId}/block`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                credentials: 'same-origin',
            });

            if (!res.ok) {
                const err = await res.json();
                throw new Error(err.error || 'Error al bloquear usuario');
            }

            const data = await res.json();

            blockBtn.dataset.blocked = data.blocked ? 'true' : 'false';
            blockBtn.classList.toggle('is-blocked', data.blocked);
            blockBtn.setAttribute('aria-label', data.blocked ? 'Desbloquear usuario' : 'Bloquear usuario');

            if (blockIcon) blockIcon.style.display = data.blocked ? 'none' : 'block';
            if (unblockIcon) unblockIcon.style.display = data.blocked ? 'block' : 'none';

            if (window.showToast) {
                window.showToast(data.message);
            }
        } catch (err) {
            if (window.showToast) {
                window.showToast(err.message, 'error');
            }
        } finally {
            blockBtn.disabled = false;
        }
    });
}
