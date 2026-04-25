import { appendReceivedBubble } from './messageRenderer.js';
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
                    'Content-Type': 'application/json'
                }
            });
        }
    });
}