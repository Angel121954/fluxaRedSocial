import { scrollToBottom, autosizeInput } from './messageUtils.js';
import { createOwnBubble, updateBubbleStatus, ensureDateSeparator } from './messageRenderer.js';
import { sendMessage } from './messageService.js';
import { initRealtime } from './realtimeHandler.js';
import { initTypingBroadcast, showTypingIndicatorBelowLastMessage, removeTypingIndicator } from './typingHandler.js';

function initUI({ input, sendBtn, bubbleList, convList, sidebarSearch, layout, backBtn }) {
    scrollToBottom(bubbleList);

    if (input) {
        input.addEventListener('input', () => autosizeInput(input));
        autosizeInput(input);
    }

    function syncSendBtn() {
        if (!sendBtn || !input) return;
        sendBtn.disabled = input.value.trim().length === 0;
    }

    if (input) {
        input.addEventListener('input', syncSendBtn);
        syncSendBtn();
    }

    return { syncSendBtn };
}

function initSendHandlers({ input, sendBtn, bubbleList, syncSendBtn }) {
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

async function handleSendMessage({ input, sendBtn, bubbleList, syncSendBtn }) {
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

function initConversationSearch(sidebarSearch, convList) {
    if (!sidebarSearch) return;

    sidebarSearch.addEventListener('input', () => {
        const query = sidebarSearch.value.trim().toLowerCase();
        const items = convList?.querySelectorAll('.msgs-conv-item') ?? [];

        items.forEach((item) => {
            const name = item.querySelector('.msgs-conv-name')?.textContent?.toLowerCase() ?? '';
            const preview = item.querySelector('.msgs-conv-preview')?.textContent?.toLowerCase() ?? '';
            item.style.display = (name.includes(query) || preview.includes(query)) ? '' : 'none';
        });
    });
}

function initMobileNavigation(convList, layout, backBtn, bubbleList) {
    if (bubbleList && layout) {
        layout.classList.add('chat-active');
    }

    if (backBtn && layout) {
        backBtn.addEventListener('click', () => {
            layout.classList.remove('chat-active');
        });
    }

    convList?.addEventListener('click', (e) => {
        const item = e.target.closest('.msgs-conv-item');
        if (!item) return;
        if (window.innerWidth <= 680 && layout) {
            e.preventDefault();
            layout.classList.add('chat-active');
            window.location.href = item.href;
        }
    });
}

function initModal({ modalOverlay, modalClose, modalSearch, modalResults }) {
    function openModal() {
        if (!modalOverlay) return;
        modalOverlay.classList.add('active');
        modalOverlay.setAttribute('aria-hidden', 'false');
        setTimeout(() => modalSearch?.focus(), 80);
    }

    function closeModal() {
        if (!modalOverlay) return;
        modalOverlay.classList.remove('active');
        modalOverlay.setAttribute('aria-hidden', 'true');
        if (modalSearch) modalSearch.value = '';
        if (modalResults) modalResults.innerHTML = '<p class="msgs-modal-hint">Empieza a escribir para buscar usuarios.</p>';
    }

    document.getElementById('btnNuevoMensaje')?.addEventListener('click', openModal);
    document.getElementById('btnNuevoMensajeEmpty')?.addEventListener('click', openModal);
    modalClose?.addEventListener('click', closeModal);

    modalOverlay?.addEventListener('click', (e) => {
        if (e.target === modalOverlay) closeModal();
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeModal();
    });

    let searchTimeout = null;

    modalSearch?.addEventListener('input', () => {
        clearTimeout(searchTimeout);
        const query = modalSearch.value.trim();

        if (query.length < 2) {
            if (modalResults) {
                modalResults.innerHTML = '<p class="msgs-modal-hint">Empieza a escribir para buscar usuarios.</p>';
            }
            return;
        }

        if (modalResults) {
            modalResults.innerHTML = '<p class="msgs-modal-hint">Buscando...</p>';
        }

        searchTimeout = setTimeout(() => {
            searchModalUsers(query, modalResults);
        }, 320);
    });
}

async function searchModalUsers(query, modalResults) {
    try {
        const { searchUsers } = await import('./messageService.js');
        const { renderModalResults } = await import('./messageRenderer.js');
        const users = await searchUsers(query);
        renderModalResults(users, modalResults);
    } catch (err) {
        console.error('[Fluxa Messages] Search error:', err);
        if (modalResults) {
            modalResults.innerHTML = '<p class="msgs-modal-hint">No se pudo realizar la búsqueda.</p>';
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const layout = document.querySelector('.msgs-layout');
    const convList = document.getElementById('msgsConvList');
    const sidebarSearch = document.getElementById('msgsSearch');
    const input = document.getElementById('msgsInput');
    const sendBtn = document.getElementById('msgsSendBtn');
    const bubbleList = document.getElementById('msgsBubbleList');
    const backBtn = document.getElementById('msgsBackBtn');
    const modalOverlay = document.getElementById('msgsModalOverlay');
    const modalClose = document.getElementById('msgsModalClose');
    const modalSearch = document.getElementById('msgsModalSearch');
    const modalResults = document.getElementById('msgsModalResults');

    const currentUser = {
        id: parseInt(input?.dataset.userId || '0'),
        name: input?.dataset.userName || '',
        avatar_url: input?.dataset.userAvatar || ''
    };

    const uiHelpers = initUI({ input, sendBtn, bubbleList, convList, sidebarSearch, layout, backBtn });
    initSendHandlers({ input, sendBtn, bubbleList, syncSendBtn: uiHelpers.syncSendBtn });
    initConversationSearch(sidebarSearch, convList);
    initMobileNavigation(convList, layout, backBtn, bubbleList);
    initModal({ modalOverlay, modalClose, modalSearch, modalResults });
    initTypingBroadcast(sendBtn?.dataset.convId, input);
    initRealtime(sendBtn?.dataset.convId, bubbleList, currentUser);
});