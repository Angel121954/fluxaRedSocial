import { scrollToBottom, autosizeInput } from './messageUtils.js';
import { createOwnBubble, updateBubbleStatus, ensureDateSeparator } from './messageRenderer.js';
import { sendMessage } from './messageService.js';
import { removeTypingIndicator } from './typingHandler.js';

let timeUpdateInterval = null;

function updateConversationTimes() {
    const convList = document.getElementById('msgsConvList');
    if (!convList) return;
    
    const times = convList.querySelectorAll('.msgs-conv-time');
    const now = new Date();
    
    times.forEach((timeEl) => {
        let timestamp = parseInt(timeEl.dataset.timestamp);
        
        if (!timestamp) {
            if (timeEl.textContent === 'Ahora') {
                timestamp = Date.now();
                timeEl.dataset.timestamp = timestamp;
            } else if (timeEl.textContent === 'Ayer') {
                timestamp = Date.now() - 86400000;
                timeEl.dataset.timestamp = timestamp;
            }
        }
        
        if (!timestamp) return;
        
        const diff = Math.floor((now - timestamp) / 1000);
        let text = '';
        
        if (diff < 1) {
            text = 'Ahora';
        } else if (diff < 60) {
            text = 'Hace ' + diff + 's';
        } else if (diff < 3600) {
            text = 'Hace ' + Math.floor(diff / 60) + 'm';
        } else if (diff < 86400) {
            text = 'Hace ' + Math.floor(diff / 3600) + 'h';
        } else {
            const days = Math.floor(diff / 86400);
            text = days === 1 ? 'Ayer' : 'Hace ' + days + 'd';
        }
        
        timeEl.textContent = text;
    });
}

export function startTimeUpdates() {
    const convList = document.getElementById('msgsConvList');
    if (!convList) return;
    
    const times = convList.querySelectorAll('.msgs-conv-time');
    times.forEach((timeEl) => {
        if (timeEl.dataset.timestamp) return;
        
        const timeText = timeEl.textContent;
        if (!timeText) return;
        
        if (timeText === 'Ahora') {
            timeEl.dataset.timestamp = Date.now();
        } else if (timeText === 'Ayer') {
            timeEl.dataset.timestamp = Date.now() - 86400000;
        } else {
            const match = timeText.match(/Hace (\d+)\s*(segundo|minuto|hora|día)/);
            if (match) {
                const num = parseInt(match[1]);
                const unit = match[2];
                let seconds = 0;
                if (unit.startsWith('segundo')) seconds = num;
                else if (unit.startsWith('minuto')) seconds = num * 60;
                else if (unit.startsWith('hora')) seconds = num * 3600;
                else if (unit.startsWith('día')) seconds = num * 86400;
                
                timeEl.dataset.timestamp = Date.now() - (seconds * 1000);
            }
        }
    });
    
    updateConversationTimes();
    timeUpdateInterval = setInterval(updateConversationTimes, 1000);
}

export function stopTimeUpdates() {
    if (timeUpdateInterval) {
        clearInterval(timeUpdateInterval);
        timeUpdateInterval = null;
    }
}

export async function handleSendMessage({ input, sendBtn, bubbleList, syncSendBtn }) {
    if (!input || !sendBtn) return;

    const body = input.value.trim();
    if (!body) return;

    const convId = sendBtn.dataset.convId;
    const recipient = sendBtn.dataset.recipient;
    if (!convId && !recipient) return;

    sendBtn.disabled = true;
    input.disabled = true;

    const tempBubble = createOwnBubble(body, 'sending');
    if (bubbleList) {
        ensureDateSeparator(bubbleList);
        bubbleList.appendChild(tempBubble);
        scrollToBottom(bubbleList, true);
    }

    const convList = document.getElementById('msgsConvList');
    if (convList && convId) {
        const existingItem = convList.querySelector('a[href*="conv=' + convId + '"]');
        if (existingItem) {
            const preview = existingItem.querySelector('.msgs-conv-preview');
            if (preview) {
                preview.textContent = 'Tú: ' + (body.length > 40 ? body.substring(0, 40) + '...' : body);
            }
            const time = existingItem.querySelector('.msgs-conv-time');
            if (time) {
                time.textContent = 'Ahora';
                time.dataset.timestamp = Date.now();
            }
            convList.prepend(existingItem);
        } else {
            const currentUser = window.currentUser || { id: 0, name: '', avatar_url: '' };
            const item = document.createElement('a');
            item.href = '/messages?conv=' + convId;
            item.className = 'msgs-conv-item';
            item.setAttribute('role', 'listitem');
            item.innerHTML = 
                '<div class="msgs-conv-avatar-wrap">' +
                    '<img src="' + (currentUser.avatar_url || '/img/default-avatar.png') + '" ' +
                        'alt="' + currentUser.name + '" class="msgs-conv-avatar" ' +
                        'onerror="this.src=\'/img/default-avatar.png\'">' +
                '</div>' +
                '<div class="msgs-conv-info">' +
                    '<div class="msgs-conv-row-top">' +
                        '<span class="msgs-conv-name">' + currentUser.name + '</span>' +
                        '<span class="msgs-conv-time" data-timestamp="' + Date.now() + '">Ahora</span>' +
                    '</div>' +
                    '<div class="msgs-conv-row-bottom">' +
                        '<span class="msgs-conv-preview">Tú: ' + (body.length > 40 ? body.substring(0, 40) + '...' : body) + '</span>' +
                    '</div>' +
                '</div>';
            const firstItem = convList.querySelector('.msgs-conv-item');
            if (firstItem) {
                convList.insertBefore(item, firstItem);
            } else {
                convList.appendChild(item);
            }
            var emptySidebar = convList.querySelector('.msgs-empty-sidebar');
            if (emptySidebar) emptySidebar.remove();
        }
    }

    input.value = '';
    autosizeInput(input);

    try {
        const data = await sendMessage(body, convId, recipient);
        tempBubble.dataset.msgId = data.id ?? '';
        updateBubbleStatus(tempBubble, 'sent');
        removeTypingIndicator();
    } catch (err) {
        console.error('[Fluxa Messages]', err);
        updateBubbleStatus(tempBubble, 'error');
    }

    input.disabled = false;
    input.focus();
    syncSendBtn();
}

export function attachSendHandler(input, sendBtn, bubbleList, syncSendBtn) {
    if (input) {
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                handleSendMessage({ input, sendBtn, bubbleList, syncSendBtn });
            }
        });
    }

    if (sendBtn) {
        sendBtn.addEventListener('click', () => handleSendMessage({ input, sendBtn, bubbleList, syncSendBtn }));
    }
}