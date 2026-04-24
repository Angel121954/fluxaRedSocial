/**
 * notifications/realtime.js - Realtime listener para notificaciones
 */

function initNotificationsRealtime(userId) {
    if (!userId || !window.Echo) {
        console.log('[Notificaciones] Echo no disponible');
        return;
    }
    
    console.log('[Notificaciones] Suscribiendo a:', 'notifications.' + userId);
    
    window.Echo.private('notifications.' + userId)
    .listen('.notification.created', function(data) {
        console.log('[Notificaciones] Nueva:', data);
        
        // Mostrar toast
        if (window.renderNotificationToast) {
            window.renderNotificationToast(data);
        }
        
        // Recargar lista si existe
        if (window.loadNotifications) {
            window.loadNotifications();
        }
        
        // Actualizar badge en topbar
        fetch('/notifications/unread', { headers: { 'Accept': 'application/json' } })
        .then(function(r) { return r.json(); })
        .then(function(resp) {
            var badge = document.querySelector('.nav-link[href*="notifications"] .nav-badge');
            var mobileBadge = document.querySelector('.mobile-menu-link[href*="notifications"] .mobile-badge');
            
            if (resp.count > 0) {
                var txt = resp.count > 99 ? '99+' : resp.count;
                if (badge) { badge.textContent = txt; badge.style.display = 'inline-flex'; }
                if (mobileBadge) { mobileBadge.textContent = txt; mobileBadge.style.display = 'inline-flex'; }
            } else {
                if (badge) badge.style.display = 'none';
                if (mobileBadge) mobileBadge.style.display = 'none';
            }
        });
    });
}

window.initNotificationsRealtime = initNotificationsRealtime;