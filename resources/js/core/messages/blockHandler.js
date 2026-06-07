function setBlockButtonState(btn, blocked) {
    btn.dataset.blocked = blocked ? 'true' : 'false';
    btn.classList.toggle('is-blocked', blocked);
    btn.setAttribute('aria-label', blocked ? 'Desbloquear usuario' : 'Bloquear usuario');

    const svg = btn.querySelector('svg');
    if (svg) {
        svg.innerHTML = blocked
            ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />'
            : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />';
    }

    btn.childNodes.forEach(node => {
        if (node.nodeType === 3 && node.textContent.trim()) {
            node.textContent = blocked ? 'Desbloquear usuario' : 'Bloquear usuario';
        }
    });
}

function updateInputState(blocked, userName) {
    const input = document.getElementById('msgsInput');
    const sendBtn = document.getElementById('msgsSendBtn');
    const shareBtn = document.getElementById('msgsShareProjectBtn');
    const toolbar = document.querySelector('.msgs-toolbar');
    const disabled = document.getElementById('msgsInputDisabled');
    const disabledText = document.getElementById('msgsDisabledText');
    if (!disabled) return;

    if (blocked) {
        if (input) input.style.display = 'none';
        if (sendBtn) sendBtn.style.display = 'none';
        if (shareBtn) shareBtn.style.display = 'none';
        if (toolbar) toolbar.style.display = 'none';
        disabled.style.display = 'flex';
        if (disabledText) {
            disabledText.textContent = `No puedes enviar mensajes a este usuario. ${userName} te ha bloqueado.`;
        }
    } else {
        if (input) input.style.display = '';
        if (sendBtn) sendBtn.style.display = '';
        if (shareBtn) shareBtn.style.display = '';
        if (toolbar) toolbar.style.display = '';
        disabled.style.display = 'none';
    }
}

export function initBlockHandler() {
    const blockBtn = document.getElementById('msgsBlockBtn');
    if (!blockBtn) return;

    const userId = blockBtn.dataset.userId;
    const userName = document.querySelector('.msgs-chat-header-name')?.textContent?.trim() ?? 'Usuario';
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    blockBtn.addEventListener('click', async () => {
        const wasBlocked = blockBtn.dataset.blocked === 'true';

        setBlockButtonState(blockBtn, !wasBlocked);
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
                throw new Error(err.error || 'Error al cambiar estado de bloqueo');
            }

            const data = await res.json();

            setBlockButtonState(blockBtn, data.blocked);
            updateInputState(data.blocked, userName);

            if (window.showToast) {
                window.showToast(data.message);
            }
        } catch (err) {
            setBlockButtonState(blockBtn, wasBlocked);

            if (window.showToast) {
                window.showToast(err.message, 'error');
            }
        } finally {
            blockBtn.disabled = false;
        }
    });
}
