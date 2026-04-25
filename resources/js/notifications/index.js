/**
 * notifications/index.js - Carga y renderizado de notificaciones
 */

var currentFilter = 'all';

function getIconForType(type) {
    var icons = {
        message: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>',
        follow: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><path d="M20 8v6m-3-3h6"/></svg>',
        like: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67 10.94 4.61a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>',
        comment: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>',
        mention: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="4"/><path d="M16 8v5a3 3 0 0 0 6 0v-1a10 10 0 1 0-4 8"/></svg>',
        project: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>',
        endorsement: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2"><path d="M12 2l2.39 7.27h7.6l-6.15 4.46 2.35 7.27-5.89-4.28-5.89 4.28 2.35-7.27-6.15-4.46h7.6z"/></svg>'
    };
    return icons[type] || '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>';
}

function renderNotificationToast(data) {
    if (!window.ToastManager) return;
    
    var icon = getIconForType(data.type);
    var msg = data.body || 'Nueva notificación';
    if (data.from_user) msg = data.from_user.name + ' ' + msg;
    
    new ToastManager().create({
        type: 'info',
        message: '<span style="display:flex;align-items:center;gap:8px"><span style="flex-shrink:0">' + icon + '</span><span>' + msg + '</span></span>',
        duration: 5000,
        dismissible: true
    });
}

window.renderNotificationToast = renderNotificationToast;

function formatTimeAgo(dateStr) {
    var d = new Date(dateStr), now = new Date(), diff = now - d;
    var mins = Math.floor(diff / 60000), hours = Math.floor(diff / 3600000), days = Math.floor(diff / 86400000);
    if (mins < 1) return 'Ahora';
    if (mins < 60) return mins + 'm';
    if (hours < 24) return hours + 'h';
    if (days < 7) return days + 'd';
    return d.toLocaleDateString('es', { day: 'numeric', month: 'short' });
}

function renderNotificationCard(n) {
    var time = formatTimeAgo(n.created_at);
    var icon = getIconForType(n.type);
    
    var card = document.createElement('div');
    card.className = 'notif-card ' + (n.is_read ? '' : 'unread');
    card.dataset.id = n.id;
    
    var contentLink = document.createElement('a');
    contentLink.href = n.link || '#';
    contentLink.className = 'notif-content';
    contentLink.innerHTML = 
        (n.from_user?.avatar_url ? '<img src="' + n.from_user.avatar_url + '" alt="" class="notif-avatar">' : '<div class="notif-icon">' + icon + '</div>') +
        '<div class="notif-body"><div class="notif-text">' + (n.from_user ? '<strong>' + n.from_user.name + '</strong> ' : '') + n.body + '</div>' +
        '<div class="notif-meta">@' + (n.from_user?.username || 'sistema') + ' · ' + time + '</div></div>' +
        '<div class="notif-time">' + time + (!n.is_read ? '<span class="unread-dot"></span>' : '') + '</div>';
    
    var deleteBtn = document.createElement('button');
    deleteBtn.className = 'notif-delete';
    deleteBtn.title = 'Eliminar';
    deleteBtn.innerHTML = '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>';
    
    card.appendChild(contentLink);
    card.appendChild(deleteBtn);
    
    deleteBtn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        card.classList.add('deleting');
        
        fetch('/notifications/' + n.id, {
            method: 'DELETE',
            headers: { 
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content, 
                'Content-Type': 'application/json' 
            }
        })
        .then(function(r) { return r.json(); })
        .then(function() {
            setTimeout(function() {
                card.remove();
                updateEmptyState();
            }, 300);
            
            if (window.updateBadges) {
                window.updateBadges();
            }
        })
        .catch(function(err) {
            card.classList.remove('deleting');
            console.error('[Notificaciones] Error al eliminar:', err);
        });
    });
    
    if (!n.is_read) {
        contentLink.addEventListener('click', function(e) {
            e.preventDefault();
            
            var id = card.dataset.id;
            if (id) {
                fetch('/notifications/' + id + '/read', {
                    method: 'PATCH',
                    headers: { 
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content, 
                        'Content-Type': 'application/json' 
                    }
                })
                .then(function() {
                    if (window.updateBadges) {
                        window.updateBadges();
                    }
                });
                card.classList.remove('unread');
                var dot = card.querySelector('.unread-dot');
                if (dot) dot.remove();
                
                var countEl = document.getElementById('notificationCount');
                if (countEl) {
                    var current = parseInt(countEl.textContent) || 0;
                    if (current > 0) {
                        var newCount = current - 1;
                        countEl.textContent = newCount > 0 ? newCount + ' sin leer' : 'Estás al día';
                    }
                }
                
                window.location.href = n.link || '#';
            }
        });
    }
    
    return card;
}

function updateEmptyState() {
    var list = document.getElementById('notificationList');
    if (!list) return;
    
    var cards = list.querySelectorAll('.notif-card');
    if (cards.length === 0) {
        list.innerHTML = '<div class="empty-box"><svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg><p>No tienes notificaciones</p></div>';
    }
}

function loadNotifications() {
    var list = document.getElementById('notificationList');
    if (!list) return;
    
    list.innerHTML = '<div class="loading-box"><div class="spinner"></div><p>Cargando...</p></div>';
    
    var url = new URL('/notifications/list', window.location.origin);
    url.searchParams.set('filter', currentFilter);
    
    fetch(url, { headers: { 'Accept': 'application/json' } })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        var countEl = document.getElementById('notificationCount');
        if (countEl) {
            countEl.textContent = data.unread_count > 0 ? data.unread_count + ' sin leer' : 'Estás al día';
        }
        
        if (data.notifications && data.notifications.length > 0) {
            list.innerHTML = '';
            data.notifications.forEach(function(n) {
                list.appendChild(renderNotificationCard(n));
            });
        } else {
            list.innerHTML = '<div class="empty-box"><div class="empty-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg></div><h3 class="empty-title">Todo al día</h3><p class="empty-text">No tienes notificaciones pendientes. ¡Sigue así!</p></div>';
        }
    })
    .catch(function(err) {
        console.error('[Notificaciones] Error:', err);
        list.innerHTML = '<div class="error-box"><p>Error al cargar</p><button class="btn-retry" onclick="loadNotifications()">Reintentar</button></div>';
    });
}

function setFilter(filter) {
    currentFilter = filter;
    loadNotifications();
}

function initNotificationsList() {
    if (!document.getElementById('notificationList')) return;
    
    loadNotifications();
    
    var filterChips = document.querySelectorAll('.filter-chip');
    filterChips.forEach(function(chip) {
        chip.onclick = function() {
            filterChips.forEach(function(c) { c.classList.remove('active'); });
            chip.classList.add('active');
            setFilter(chip.dataset.filter);
        };
    });
    
    var markAllBtn = document.getElementById('markAllRead');
    if (markAllBtn) {
        markAllBtn.onclick = function() {
            markAllBtn.disabled = true;
            fetch('/notifications/read-all', {
                method: 'PATCH',
                headers: { 
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content, 
                    'Content-Type': 'application/json' 
                }
            })
            .then(function() {
                loadNotifications();
                if (window.updateBadges) {
                    window.updateBadges();
                }
            })
            .catch(function() {
                loadNotifications();
            })
            .finally(function() {
                markAllBtn.disabled = false;
            });
        };
    }
}

window.initNotificationsList = initNotificationsList;
window.loadNotifications = loadNotifications;
window.renderNotificationCard = renderNotificationCard;