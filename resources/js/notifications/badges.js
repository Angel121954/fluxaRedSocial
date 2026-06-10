/**
 * notifications/badges.js - Actualización de badges en tiempo real
 */

function updateBadges() {
    let currentConvId = null;
    const convLink = document.querySelector('.msgs-conv-item.active');
    if (convLink) {
        const match = convLink.href.match(/conv=(\d+)/);
        if (match) currentConvId = match[1];
    }

    function fetchJson(url) {
        return fetch(url, {
            headers: { 'Accept': 'application/json' },
            credentials: 'same-origin',
        }).then(function (r) {
            if (!r.ok) return { count: 0 };
            return r.json().catch(function () { return { count: 0 }; });
        });
    }

    Promise.all([
        fetchJson('/notifications/unread'),
        fetchJson('/messages/unread/count?exclude_conv=' + (currentConvId || '')),
    ])
        .then(function (results) {
            const notifData = results[0];
            const msgData = results[1];

            const notifBadge = document.getElementById('navNotificationsBadge');
            const msgBadge = document.getElementById('navMessagesBadge');
            const mobileNotifBadge = document.querySelector('.mobile-menu-link[href*="notifications"] .mobile-badge');
            const mobileMsgBadge = document.querySelector('.mobile-menu-link[href*="messages"] .mobile-badge');

            if (notifBadge) {
                if (notifData.count > 0) {
                    notifBadge.textContent = notifData.count > 99 ? '99+' : notifData.count;
                    notifBadge.style.display = 'inline-flex';
                    notifBadge.classList.remove('new');
                    void notifBadge.offsetWidth;
                    notifBadge.classList.add('pulse');
                } else {
                    notifBadge.style.display = 'none';
                }
            }

            if (msgBadge) {
                if (msgData.count > 0) {
                    msgBadge.textContent = msgData.count > 99 ? '99+' : msgData.count;
                    msgBadge.style.display = 'inline-flex';
                    msgBadge.classList.remove('new');
                    void msgBadge.offsetWidth;
                    msgBadge.classList.add('pulse');
                } else {
                    msgBadge.style.display = 'none';
                }
            }

            if (mobileNotifBadge) {
                mobileNotifBadge.textContent = notifData.count > 99 ? '99+' : notifData.count;
                mobileNotifBadge.style.display = notifData.count > 0 ? 'inline-flex' : 'none';
            }

            if (mobileMsgBadge) {
                mobileMsgBadge.textContent = msgData.count > 99 ? '99+' : msgData.count;
                mobileMsgBadge.style.display = msgData.count > 0 ? 'inline-flex' : 'none';
            }
        })
        .catch(function (err) {
            console.error('[Badges] Error actualizando:', err);
        });
}

window.updateBadges = updateBadges;