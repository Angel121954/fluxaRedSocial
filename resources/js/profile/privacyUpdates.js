export function initPrivacyUpdates() {
    const meta = document.querySelector('.meta-actions');
    if (!meta) return;

    const isOwner = meta.dataset.isOwner === 'true';
    const profileUserId = meta.dataset.profileUserId;
    if (isOwner || !profileUserId || !window.Echo) return;

    const channel = window.Echo.private(`user.privacy.${profileUserId}`);

    channel.listen('.privacy.updated', (data) => {
        const msgBtn = document.querySelector('.btn-message');
        if (!msgBtn) return;

        msgBtn.style.display = data.accept_messages === false ? 'none' : '';
    });
}
