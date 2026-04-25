import { initUI, initConversationSearch, initMobileNavigation, initModal } from './ui.js';
import { attachSendHandler } from './sender.js';
import { startTimeUpdates } from './sender.js';
import { initRealtime } from './realtimeHandler.js';
import { initTypingBroadcast } from './typingHandler.js';

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

    window.currentUser = currentUser;

    const uiHelpers = initUI({ input, sendBtn, bubbleList, convList, sidebarSearch, layout, backBtn });
    attachSendHandler(input, sendBtn, bubbleList, uiHelpers.syncSendBtn);
    initConversationSearch(sidebarSearch, convList);
    initMobileNavigation(convList, layout, backBtn, bubbleList);
    initModal({ modalOverlay, modalClose, modalSearch, modalResults });
    startTimeUpdates();
    initTypingBroadcast(sendBtn?.dataset.convId, input);
    initRealtime(sendBtn?.dataset.convId, bubbleList, currentUser);
    
    if (window.updateBadges) {
        window.updateBadges();
    }
});