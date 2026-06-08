import { initUI, initConversationSearch, initMobileNavigation, initModal, initConversationTabs, initMoreDropdown, initToolbarActions } from './ui.js';
import { initGiphyPicker } from './giphy.js';
import { attachSendHandler, startTimeUpdates } from './sender.js';
import { initRealtime } from './realtimeHandler.js';
import { initTypingBroadcast } from './typingHandler.js';
import { initBlockHandler } from './blockHandler.js';
import { initEmojiPicker } from './emojiPicker.js';

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
    initConversationTabs(convList);
    initMobileNavigation(convList, layout, backBtn, bubbleList);
    initModal({ modalOverlay, modalClose, modalSearch, modalResults });
    initMoreDropdown();
    initToolbarActions();
    initGiphyPicker();
    startTimeUpdates();
    initTypingBroadcast(sendBtn?.dataset.convId, input);
    initRealtime(bubbleList?.dataset.convId, bubbleList, currentUser);
    initBlockHandler();
    initEmojiPicker();

    if (window.updateBadges) window.updateBadges();

    if (input?.disabled && sendBtn) sendBtn.disabled = true;

    /* ─── Image preview modal ─── */
    const imgModal = document.getElementById('msgsImgModal');
    const imgModalImg = document.getElementById('msgsImgModalImg');
    const imgModalClose = document.getElementById('msgsImgModalClose');

    function openImgPreview(src) {
        if (!imgModal || !imgModalImg) return;
        imgModalImg.src = src;
        imgModal.classList.add('show');
        window.lockBodyScroll?.();
    }

    function closeImgPreview() {
        if (!imgModal) return;
        imgModal.classList.remove('show');
        window.unlockBodyScroll?.();
    }

    document.querySelector('#msgsBubbleList')?.addEventListener('click', (e) => {
        const img = e.target.closest('.msgs-media-img');
        if (img) openImgPreview(img.src);
    });

    imgModalClose?.addEventListener('click', closeImgPreview);

    imgModal?.addEventListener('click', (e) => {
        if (e.target === imgModal) closeImgPreview();
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && imgModal?.classList.contains('show')) closeImgPreview();
    });
});