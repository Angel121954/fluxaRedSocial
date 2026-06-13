import { initUI, initConversationSearch, initMobileNavigation, initModal, initConversationTabs, initMoreDropdown, initToolbarActions } from './ui.js';
import { initGiphyPicker } from './giphy.js';
import { attachSendHandler, startTimeUpdates } from './sender.js';
import { initRealtime } from './realtimeHandler.js';
import { initTypingBroadcast } from './typingHandler.js';
import { initBlockHandler } from './blockHandler.js';
import { initEmojiPicker } from './emojiPicker.js';
import { updateMessage } from './messageService.js';
import { updateBubbleBody } from './messageRenderer.js';

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

    /* ─── Edit message modal ─── */
    const editModal = document.getElementById('msgsEditModal');
    const editTextarea = document.getElementById('msgsEditTextarea');
    const editCharCount = document.getElementById('msgsEditCharCount');
    const editSave = document.getElementById('msgsEditSave');
    const editCancel = document.getElementById('msgsEditCancel');
    const editClose = document.getElementById('msgsEditModalClose');
    let editingMsgId = null;

    function openEditModal(msgId, currentBody) {
        editingMsgId = msgId;
        editTextarea.value = currentBody;
        updateEditCharCount();
        editModal.classList.add('is-open');
        editModal.setAttribute('aria-hidden', 'false');
        window.lockBodyScroll?.();
        setTimeout(() => editTextarea?.focus(), 80);
    }

    function closeEditModal() {
        editingMsgId = null;
        editModal.classList.remove('is-open');
        editModal.setAttribute('aria-hidden', 'true');
        window.unlockBodyScroll?.();
    }

    function updateEditCharCount() {
        if (!editTextarea || !editCharCount) return;
        const len = editTextarea.value.length;
        editCharCount.textContent = `${len}/2000`;
    }

    editTextarea?.addEventListener('input', updateEditCharCount);

    bubbleList?.addEventListener('click', (e) => {
        const editBtn = e.target.closest('.msgs-edit-btn');
        if (editBtn) {
            const wrap = editBtn.closest('.msgs-bubble-wrap');
            const msgId = wrap?.dataset.msgId;
            const bodyEl = wrap?.querySelector('.msgs-bubble-body');
            const body = bodyEl?.textContent ?? '';
            if (msgId) openEditModal(msgId, body);
        }
    });

    editSave?.addEventListener('click', async () => {
        if (!editingMsgId) return;
        const body = editTextarea.value.trim();
        if (!body) return;

        editSave.disabled = true;

        try {
            const data = await updateMessage(editingMsgId, body);
            const wrap = bubbleList?.querySelector(`.msgs-bubble-wrap[data-msg-id="${editingMsgId}"]`);
            if (wrap) {
                updateBubbleBody(wrap, data.body, data.edited_at);
            }
            closeEditModal();
        } catch (err) {
            console.error('[Fluxa Messages]', err);
            if (window.showToast) window.showToast('Error al editar el mensaje', 'error');
        }

        editSave.disabled = false;
    });

    editCancel?.addEventListener('click', closeEditModal);
    editClose?.addEventListener('click', closeEditModal);

    editModal?.addEventListener('click', (e) => {
        if (e.target === editModal) closeEditModal();
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && editModal?.classList.contains('is-open')) closeEditModal();
    });
});