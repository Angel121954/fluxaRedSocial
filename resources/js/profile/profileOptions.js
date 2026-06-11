const metaActions = document.querySelector('.meta-actions');
const isOwner = metaActions?.dataset.isOwner === 'true';
const isFollowing = metaActions?.dataset.isFollowing === 'true';
let isFollowedBy = metaActions?.dataset.isFollowedBy === 'true';

const ownOpts = document.getElementById('ownOpts');
const otherOpts = document.getElementById('otherOpts');
const btnFollow = document.getElementById('btnFollow');

if (ownOpts) ownOpts.style.display = isOwner ? '' : 'none';
if (otherOpts) otherOpts.style.display = isOwner ? 'none' : '';

if (btnFollow) {
    btnFollow.style.display = isOwner ? 'none' : '';

    const getLabel = (following) => following
        ? 'Siguiendo'
        : isFollowedBy
            ? 'Seguir también'
            : 'Seguir';

    const btnFollowText = document.getElementById('btnFollowText');

    btnFollow.addEventListener('click', async () => {
        const userId = btnFollow.dataset.userId;
        const wasFollowing = btnFollow.classList.contains('is-following');

        // Optimistic UI
        const nowFollowing = !wasFollowing;
        btnFollow.classList.toggle('is-following', nowFollowing);
        if (btnFollowText) btnFollowText.textContent = getLabel(nowFollowing);

        try {
            const res = await fetch(`/users/${userId}/follow`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
            });

            if (!res.ok) {
                const err = await res.json().catch(() => ({}));
                throw new Error(err.message || 'Error al seguir');
            }

            const data = await res.json();

            const following = data.following;
            isFollowedBy = data.is_followed_by;
            btnFollow.classList.toggle('is-following', following);
            if (btnFollowText) btnFollowText.textContent = getLabel(following);

            const followersEl = document.querySelector('.stat:last-child .stat-n');
            if (followersEl && data.followers_count !== undefined) {
                followersEl.textContent = data.followers_count;
            }
        } catch (err) {
            console.error('[Follow]', err);
            // Revertir
            btnFollow.classList.toggle('is-following', wasFollowing);
            if (btnFollowText) btnFollowText.textContent = getLabel(wasFollowing);
        }
    });
}

// ── Reportar usuario ─────────────────────────────────────────────────────
const btnReport = document.querySelector('#otherOpts .drop-item.danger');
const profileUserId = metaActions?.dataset.profileUserId;

if (btnReport && profileUserId && !isOwner) {
    btnReport.addEventListener('click', () => {
        const reportForm = document.getElementById('reportForm');
        const reportTitle = document.getElementById('reportModalTitle');
        const reportDesc = document.getElementById('reportModalDesc');
        if (reportTitle) reportTitle.textContent = 'Reportar usuario';
        if (reportDesc) reportDesc.textContent = '¿Por qué quieres reportar este usuario?';
        if (reportForm) {
            reportForm.dataset.userId = profileUserId;
            reportForm.removeAttribute('data-project-id');
            reportForm.dataset.type = 'user';
        }
        const reasonEl = document.getElementById('reportReason');
        if (reasonEl) reasonEl.value = '';
        document.getElementById('reportModal')?.classList.add('show');
    });
}
