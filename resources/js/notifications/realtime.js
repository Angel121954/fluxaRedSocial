/**
 * notifications/realtime.js - Conexiones WebSocket para notificaciones y mensajes
 */

function renderConversationItem(data, messageData) {
    var convList = document.getElementById('msgsConvList');
    var emptySidebar = convList ? convList.querySelector('.msgs-empty-sidebar') : null;
    if (emptySidebar) {
        emptySidebar.remove();
    }
    
    if (!convList) return;
    
    var existingItem = convList.querySelector('a[href*="conv=' + data.id + '"]');
    if (existingItem) {
        convList.prepend(existingItem);
        if (emptySidebar) emptySidebar.remove();
    } else {
        var currentUserId = parseInt(document.body.dataset.userId || '0');
        var isFromMe = messageData && messageData.sender_id === currentUserId;
        var previewText = '';
        var hasUnread = false;
        
        if (messageData) {
            previewText = isFromMe ? 'Tú: ' + messageData.body : messageData.body;
            if (previewText.length > 40) {
                previewText = previewText.substring(0, 40) + '...';
            }
            if (!isFromMe) {
                var badge = item.querySelector('.msgs-unread-badge');
                if (badge) {
                    var currentCount = parseInt(badge.textContent) || 0;
                    badge.textContent = currentCount + 1;
                } else {
                    hasUnread = true;
                }
            }
        } else {
            previewText = 'Nueva conversación';
        }
        
        var item = document.createElement('a');
        item.href = '/messages?conv=' + data.id;
        item.className = 'msgs-conv-item';
        item.setAttribute('role', 'listitem');
        item.innerHTML = 
            '<div class="msgs-conv-avatar-wrap">' +
                '<img src="' + (data.other_user.avatar_url || '/img/default-avatar.png') + '" ' +
                    'alt="' + data.other_user.name + '" class="msgs-conv-avatar" ' +
                    'onerror="this.src=\'/img/default-avatar.png\'">' +
            '</div>' +
            '<div class="msgs-conv-info">' +
                '<div class="msgs-conv-row-top">' +
                    '<span class="msgs-conv-name">' + data.other_user.name + '</span>' +
                    '<span class="msgs-conv-time" data-timestamp="' + Date.now() + '">Ahora</span>' +
                '</div>' +
                '<div class="msgs-conv-row-bottom">' +
                    '<span class="msgs-conv-preview">' + previewText + '</span>' +
                    (hasUnread ? '<span class="msgs-unread-badge">1</span>' : '') +
                '</div>' +
            '</div>';
        
        var firstItem = convList.querySelector('.msgs-conv-item');
        if (firstItem) {
            convList.insertBefore(item, firstItem);
        } else {
            convList.appendChild(item);
        }
    }
    
    if (messageData && typeof window.appendReceivedBubble === 'function') {
        var bubbleList = document.getElementById('msgsBubbleList');
        var currentConvId = new URLSearchParams(window.location.search).get('conv');
        if (currentConvId && parseInt(currentConvId) === data.id && bubbleList) {
            window.appendReceivedBubble(messageData, bubbleList);
            if (typeof window.scrollToBottom === 'function') {
                window.scrollToBottom(bubbleList, true);
            }
        }
    }
}

window.renderConversationItem = renderConversationItem;

function initNotificationsRealtime(userId) {
    if (!userId || !window.Echo) {
        return;
    }

    window.Echo.private('notifications.' + userId)
    .on('pusher:subscription_succeeded', function(data) {
    })
    .on('pusher:subscription_error', function(err) {
    })
    .listen('.notification.created', function(data) {
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
    .on('pusher:subscription_succeeded', function(data) {
    })
    .on('pusher:subscription_error', function(err) {
    })
    .listen('.conversation.created', function(data) {
        if (window.renderConversationItem) {
            window.renderConversationItem(data, data.message);
        }
        
        if (window.updateBadges) {
            window.updateBadges();
        }
    })
    .listen('.message.sent', function(data) {
        
        var convList = document.getElementById('msgsConvList');
        var currentConvId = new URLSearchParams(window.location.search).get('conv');
        var currentUserId = parseInt(document.body.dataset.userId || '0');
        var isFromMe = data.sender_id === currentUserId;
        
        if (convList) {
            var previewText = isFromMe ? 'Tú: ' + data.body : data.body;
            if (previewText.length > 40) {
                previewText = previewText.substring(0, 40) + '...';
            }
            
            var item = convList.querySelector('a[href*="conv=' + data.conversation_id + '"]');
            if (item) {
                var preview = item.querySelector('.msgs-conv-preview');
                if (preview) {
                    preview.textContent = previewText;
                }
                var time = item.querySelector('.msgs-conv-time');
                if (time) {
                    time.textContent = 'Ahora';
                    time.dataset.timestamp = Date.now();
                }
                var badge = item.querySelector('.msgs-unread-badge');
                if (!isFromMe && (!currentConvId || parseInt(currentConvId) !== data.conversation_id)) {
                    if (badge) {
                        var currentCount = parseInt(badge.textContent) || 0;
                        badge.textContent = currentCount + 1;
                    } else {
                        badge = document.createElement('span');
                        badge.className = 'msgs-unread-badge';
                        badge.textContent = '1';
                        var rowBottom = item.querySelector('.msgs-conv-row-bottom');
                        if (rowBottom) rowBottom.appendChild(badge);
                    }
                }
                convList.prepend(item);
            } else if (!isFromMe) {
                var item = document.createElement('a');
                item.href = '/messages?conv=' + data.conversation_id;
                item.className = 'msgs-conv-item';
                item.setAttribute('role', 'listitem');
                item.dataset.timestamp = Date.now();
                item.innerHTML = 
                    '<div class="msgs-conv-avatar-wrap">' +
                        '<img src="' + (data.sender?.avatar_url || '/img/default-avatar.png') + '" ' +
                            'alt="' + (data.sender?.name || 'Usuario') + '" class="msgs-conv-avatar" ' +
                            'onerror="this.src=\'/img/default-avatar.png\'">' +
                    '</div>' +
                    '<div class="msgs-conv-info">' +
                        '<div class="msgs-conv-row-top">' +
                            '<span class="msgs-conv-name">' + (data.sender?.name || 'Usuario') + '</span>' +
                            '<span class="msgs-conv-time" data-timestamp="' + Date.now() + '">Ahora</span>' +
                        '</div>' +
                        '<div class="msgs-conv-row-bottom">' +
                            '<span class="msgs-conv-preview">' + previewText + '</span>' +
                            '<span class="msgs-unread-badge">1</span>' +
                        '</div>' +
                    '</div>';
                convList.insertBefore(item, convList.firstChild);
            }
        }
        
        if (currentConvId && parseInt(currentConvId) === data.conversation_id && !isFromMe) {
            var bubbleList = document.getElementById('msgsBubbleList');
            if (typeof window.appendReceivedBubble === 'function' && bubbleList) {
                window.appendReceivedBubble(data, bubbleList);
                if (typeof window.scrollToBottom === 'function') {
                    window.scrollToBottom(bubbleList, true);
                }
                fetch('/messages/message/' + data.id + '/read', {
                    method: 'PATCH',
                    headers: { 
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                        'Content-Type': 'application/json'
                    }
                });
            }
        }
        
        if (window.updateBadges) {
            window.updateBadges();
        }
    });
}

window.initNotificationsRealtime = initNotificationsRealtime;