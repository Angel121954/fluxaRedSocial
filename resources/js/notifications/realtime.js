/**
 * notifications/realtime.js - Conexiones WebSocket para notificaciones y mensajes
 */

function escapeHtml(str) {
    const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
    return String(str ?? '').replace(/[&<>"']/g, function(c) { return map[c]; });
}

function renderConversationItem(data, messageData) {
    const convList = document.getElementById('msgsConvList');
    const emptySidebar = convList ? convList.querySelector('.msgs-empty-sidebar') : null;
    if (emptySidebar) {
        emptySidebar.remove();
    }

    if (!convList) return;

    const existingItem = convList.querySelector('a[href*="conv=' + data.id + '"]');
    if (existingItem) {
        convList.prepend(existingItem);
        if (emptySidebar) emptySidebar.remove();
    } else {
        const currentUserId = parseInt(document.body.dataset.userId || '0');
        const isFromMe = messageData && messageData.sender_id === currentUserId;
        let previewText = '';
        let hasUnread = false;

        if (messageData) {
            previewText = isFromMe ? 'Tú: ' + messageData.body : messageData.body;
            if (previewText.length > 40) {
                previewText = previewText.substring(0, 40) + '...';
            }
            if (!isFromMe) {
                const badge = item.querySelector('.msgs-unread-badge');
                if (badge) {
                    const currentCount = parseInt(badge.textContent) || 0;
                    badge.textContent = currentCount + 1;
                } else {
                    hasUnread = true;
                }
            }
        } else {
            previewText = 'Nueva conversación';
        }

        const item = document.createElement('a');
        item.href = '/messages?conv=' + data.id;
        item.className = 'msgs-conv-item';
        item.setAttribute('role', 'listitem');
        item.innerHTML =
            '<div class="msgs-conv-avatar-wrap">' +
            '<img src="' + escapeHtml(data.other_user.avatar_url || '/img/default-avatar.png') + '" ' +
            'alt="' + escapeHtml(data.other_user.name) + '" class="msgs-conv-avatar" ' +
            'onerror="this.src=\'/img/default-avatar.png\'">' +
            '</div>' +
            '<div class="msgs-conv-info">' +
            '<div class="msgs-conv-row-top">' +
            '<span class="msgs-conv-name">' + escapeHtml(data.other_user.name) + '</span>' +
            '<span class="msgs-conv-time" data-timestamp="' + Date.now() + '">Ahora</span>' +
            '</div>' +
            '<div class="msgs-conv-row-bottom">' +
            '<span class="msgs-conv-preview">' + escapeHtml(previewText) + '</span>' +
            (hasUnread ? '<span class="msgs-unread-badge">1</span>' : '') +
            '</div>' +
            '</div>';

        const firstItem = convList.querySelector('.msgs-conv-item');
        if (firstItem) {
            convList.insertBefore(item, firstItem);
        } else {
            convList.appendChild(item);
        }
    }

    if (messageData && typeof window.appendReceivedBubble === 'function') {
        const bubbleList = document.getElementById('msgsBubbleList');
        const currentConvId = new URLSearchParams(window.location.search).get('conv');
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
        .on('pusher:subscription_succeeded', function (data) {
        })
        .on('pusher:subscription_error', function (err) {
        })
        .listen('.notification.created', function (data) {
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
        .on('pusher:subscription_succeeded', function (data) {
        })
        .on('pusher:subscription_error', function (err) {
        })
        .listen('.conversation.created', function (data) {
            if (window.renderConversationItem) {
                window.renderConversationItem(data, data.message);
            }

            if (window.updateBadges) {
                window.updateBadges();
            }
        })
        .listen('.user.blocked', function (data) {
            const input = document.getElementById('msgsInput');
            const sendBtn = document.getElementById('msgsSendBtn');
            const shareBtn = document.getElementById('msgsShareProjectBtn');
            const toolbar = document.querySelector('.msgs-toolbar');
            const disabled = document.getElementById('msgsInputDisabled');
            const disabledText = document.getElementById('msgsDisabledText');
            const blockBtn = document.getElementById('msgsBlockBtn');

            if (!blockBtn || blockBtn.dataset.userId != data.blocker_id) return;

            if (data.blocked) {
                if (input) input.style.display = 'none';
                if (sendBtn) sendBtn.style.display = 'none';
                if (shareBtn) shareBtn.style.display = 'none';
                if (toolbar) toolbar.style.display = 'none';
                if (disabled) disabled.style.display = 'flex';
                const blockerName = document.querySelector('.msgs-chat-header-name')?.textContent || 'Este usuario';
                if (disabledText) disabledText.textContent = 'No puedes enviar mensajes a este usuario. ' + blockerName + ' te ha bloqueado';
            } else {
                const acceptsMessages = blockBtn.dataset.acceptsMessages !== 'false';
                if (acceptsMessages) {
                    if (input) input.style.display = '';
                    if (sendBtn) sendBtn.style.display = '';
                    if (shareBtn) shareBtn.style.display = '';
                    if (toolbar) toolbar.style.display = '';
                    if (disabled) disabled.style.display = 'none';
                }
            }

            if (window.showToast) {
                window.showToast(data.blocked ? 'Has sido bloqueado por este usuario' : 'Has sido desbloqueado por este usuario');
            }
        })

        .listen('.message.sent', function (data) {
            const convList = document.getElementById('msgsConvList');
            const currentConvId = new URLSearchParams(window.location.search).get('conv');
            const currentUserId = parseInt(document.body.dataset.userId || '0');
            const isFromMe = data.sender_id === currentUserId;

            if (!convList) {
                if (window.updateBadges) window.updateBadges();
                return;
            }

            let previewText = data.body ?? (data.media_type === 'image' ? '📷 Imagen' : data.media_type === 'gif' ? 'GIF' : '📎 Archivo');
            if (isFromMe) previewText = 'Tú: ' + previewText;
            if (previewText.length > 40) {
                previewText = previewText.substring(0, 40) + '...';
            }

            const item = convList.querySelector('a[href*="conv=' + data.conversation_id + '"]');
            if (item) {
                const preview = item.querySelector('.msgs-conv-preview');
                if (preview) {
                    preview.textContent = previewText;
                }
                const time = item.querySelector('.msgs-conv-time');
                if (time) {
                    time.textContent = 'Ahora';
                    time.dataset.timestamp = Date.now();
                }
                if (!isFromMe && (!currentConvId || parseInt(currentConvId) !== data.conversation_id)) {
                    const badge = item.querySelector('.msgs-unread-badge');
                    if (badge) {
                        const currentCount = parseInt(badge.textContent) || 0;
                        badge.textContent = currentCount + 1;
                    } else {
                        badge = document.createElement('span');
                        badge.className = 'msgs-unread-badge';
                        badge.textContent = '1';
                        const rowBottom = item.querySelector('.msgs-conv-row-bottom');
                        if (rowBottom) rowBottom.appendChild(badge);
                    }
                }
                convList.prepend(item);
            } else if (!isFromMe) {
                const item = document.createElement('a');
                item.href = '/messages?conv=' + data.conversation_id;
                item.className = 'msgs-conv-item';
                item.setAttribute('role', 'listitem');
                item.dataset.timestamp = Date.now();
                item.innerHTML =
                    '<div class="msgs-conv-avatar-wrap">' +
                    '<img src="' + escapeHtml(data.sender?.avatar_url || '/img/default-avatar.png') + '" ' +
                    'alt="' + escapeHtml(data.sender?.name || 'Usuario') + '" class="msgs-conv-avatar" ' +
                    'onerror="this.src=\'/img/default-avatar.png\'">' +
                    '</div>' +
                    '<div class="msgs-conv-info">' +
                    '<div class="msgs-conv-row-top">' +
                    '<span class="msgs-conv-name">' + escapeHtml(data.sender?.name || 'Usuario') + '</span>' +
                    '<span class="msgs-conv-time" data-timestamp="' + Date.now() + '">Ahora</span>' +
                    '</div>' +
                    '<div class="msgs-conv-row-bottom">' +
                    '<span class="msgs-conv-preview">' + escapeHtml(previewText) + '</span>' +
                    '<span class="msgs-unread-badge">1</span>' +
                    '</div>' +
                    '</div>';
                convList.insertBefore(item, convList.firstChild);
            }

            if (window.updateBadges) {
                window.updateBadges();
            }
        });
}

window.initNotificationsRealtime = initNotificationsRealtime;