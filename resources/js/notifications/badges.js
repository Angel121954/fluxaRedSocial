/**
 * notifications/badges.js - Actualización de badges en tiempo real
 */

function updateBadges() {
    Promise.all([
        fetch('/notifications/unread', { headers: { 'Accept': 'application/json' } }).then(r => r.json()),
        fetch('/messages/unread/count', { headers: { 'Accept': 'application/json' } }).then(r => r.json())
    ])
    .then(function(results) {
        var notifData = results[0];
        var msgData = results[1];
        
        var notifBadge = document.getElementById('navNotificationsBadge');
        var msgBadge = document.getElementById('navMessagesBadge');
        var mobileNotifBadge = document.querySelector('.mobile-menu-link[href*="notifications"] .mobile-badge');
        var mobileMsgBadge = document.querySelector('.mobile-menu-link[href*="messages"] .mobile-badge');
        
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
    .catch(function(err) {
        console.error('[Badges] Error actualizando:', err);
    });
}

window.updateBadges = updateBadges;