export function initFollowUpdates() {
    const meta = document.querySelector('.meta-actions');
    if (!meta) return;

    const isOwner = meta.dataset.isOwner === 'true';
    const profileUserId = parseInt(meta.dataset.profileUserId || '0');
    if (isOwner || !profileUserId || !window.Echo) return;

    const currentUserId = parseInt(document.body.dataset.userId || '0');
    if (!currentUserId) return;

    const channel = window.Echo.private(`user.follow.${profileUserId}`);

    channel.listen('.follow.toggled', (data) => {
        const btnFollow = document.getElementById('btnFollow');
        const statsN = document.querySelectorAll('.stat-n');

        // The profile user followed/unfollowed someone → update "Siguiendo"
        if (data.follower_id === profileUserId && statsN.length >= 2) {
            statsN[1].textContent = data.follower_following_count;
        }

        // Someone followed/unfollowed the profile user → update "Seguidores"
        if (data.target_id === profileUserId && statsN.length >= 3) {
            statsN[2].textContent = data.target_followers_count;
        }

        // When the profile user toggled their follow on ME → update button
        if (data.follower_id === profileUserId && data.target_id === currentUserId) {
            if (!btnFollow) return;

            const isFollowing = btnFollow.classList.contains('is-following');
            if (isFollowing) return;

            const btnFollowText = document.getElementById('btnFollowText');
            if (btnFollowText) {
                btnFollowText.textContent = data.following
                    ? 'Seguir también'
                    : 'Seguir';
            }
        }
    });
}
