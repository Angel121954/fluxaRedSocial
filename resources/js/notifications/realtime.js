/**
 * notifications/realtime.js - Conexiones WebSocket para notificaciones y mensajes
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
        
        if (window.renderNotificationToast) {
            window.renderNotificationToast(data);
        }
        
        if (window.loadNotifications) {
            window.loadNotifications();
        }
        
        if (window.updateBadges) {
            window.updateBadges();
        }
    });
    
    window.Echo.private('messages.user.' + userId)
    .listen('.message.sent', function(data) {
        console.log('[Mensajes] Nuevo:', data);
        
        var currentConvId = new URLSearchParams(window.location.search).get('conv');
        
        if (currentConvId && parseInt(currentConvId) === data.conversation_id) {
            return;
        }
        
        if (window.updateBadges) {
            window.updateBadges();
        }
    });
}

window.initNotificationsRealtime = initNotificationsRealtime;