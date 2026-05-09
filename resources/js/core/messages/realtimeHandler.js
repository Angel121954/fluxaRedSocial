import { appendReceivedBubble, showNotAcceptingMessages } from './messageRenderer.js';
import { scrollToBottom } from './messageUtils.js';
import { showTypingIndicatorBelowLastMessage, removeTypingIndicator } from './typingHandler.js';

let currentUserId = null;

export function initRealtime(convId, bubbleList, currentUser) {
    if (!convId || !window.Echo) return;

    currentUserId = currentUser?.id || parseInt(document.getElementById('msgsInput')?.dataset.userId || '0');

    const channel = window.Echo.private(`messages.${convId}`);

    channel.on('pusher:subscription_succeeded', () => {
        channel.listenForWhisper('typing', (data) => {
            if (data.is_typing && data.user_id !== currentUserId) {
                showTypingIndicatorBelowLastMessage(data.user_name, data.avatar_url);
            } else if (data.user_id !== currentUserId) {
                setTimeout(() => removeTypingIndicator(), 500);
            }
        });
    });

    channel.on('pusher:subscription_error', (err) => {
        console.error('[Reverb] Error suscripción:', err);
    });

    channel.listen('.message.sent', (msg) => {
        if (msg.sender_id !== currentUserId) {
            appendReceivedBubble(msg, bubbleList);
            removeTypingIndicator();
            scrollToBottom(bubbleList, true);
            
            fetch(`/messages/message/${msg.id}/read`, {
                method: 'PATCH',
                headers: { 
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                credentials: 'same-origin',
            });

            if (window.updateBadges) {
                window.updateBadges();
            }
        }
    });

    // Listen for privacy settings changes
    channel.listen('.privacy.updated', (data) => {
        const input = document.getElementById('msgsInput');
        const sendBtn = document.getElementById('msgsSendBtn');
        const disabled = document.getElementById('msgsInputDisabled');
        const disabledText = document.getElementById('msgsDisabledText');
        if (!input || !sendBtn || !disabled) return;

        if (data.accept_messages === false) {
            input.style.display = 'none';
            sendBtn.style.display = 'none';
            disabled.style.display = 'flex';
            if (disabledText) disabledText.textContent = `${data.user_name} no acepta mensajes directos`;
        } else if (data.accept_messages === true) {
            input.style.display = '';
            sendBtn.style.display = '';
            disabled.style.display = 'none';
        }
    });
}