import { initUI, initConversationSearch, initMobileNavigation, initModal, initConversationTabs, initMoreDropdown, initToolbarActions } from './ui.js';
import { initGiphyPicker } from './giphy.js';
import { attachSendHandler, startTimeUpdates } from './sender.js';
import { initRealtime } from './realtimeHandler.js';
import { initTypingBroadcast } from './typingHandler.js';
import { initBlockHandler } from './blockHandler.js';
import { initEmojiPicker } from './emojiPicker.js';
import { updateMessage, sendMessage } from './messageService.js';
import { updateBubbleBody, createOwnBubble, ensureDateSeparator } from './messageRenderer.js';
import { scrollToBottom } from './messageUtils.js';

function initChatFeatures() {
    const input = document.getElementById('msgsInput');
    const sendBtn = document.getElementById('msgsSendBtn');
    const bubbleList = document.getElementById('msgsBubbleList');
    const convList = document.getElementById('msgsConvList');
    const sidebarSearch = document.getElementById('msgsSearch');
    const layout = document.querySelector('.msgs-layout');
    const backBtn = document.getElementById('msgsBackBtn');

    const uiHelpers = initUI({ input, sendBtn, bubbleList, convList, sidebarSearch, layout, backBtn });
    attachSendHandler(input, sendBtn, bubbleList, uiHelpers.syncSendBtn);
    initMobileNavigation(convList, layout, backBtn, bubbleList);
    initMoreDropdown();
    initToolbarActions();
    initGiphyPicker();
    initTypingBroadcast(sendBtn?.dataset.convId, input);
    initRealtime(bubbleList?.dataset.convId, bubbleList, window.currentUser);
    initBlockHandler();
    initEmojiPicker();

    if (input?.disabled && sendBtn) sendBtn.disabled = true;
}

document.addEventListener('DOMContentLoaded', () => {
    const layout = document.querySelector('.msgs-layout');
    const convList = document.getElementById('msgsConvList');
    const sidebarSearch = document.getElementById('msgsSearch');
    const input = document.getElementById('msgsInput');
    const bubbleList = document.getElementById('msgsBubbleList');
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
    window.reinitChat = initChatFeatures;

    initConversationSearch(sidebarSearch, convList);
    initConversationTabs(convList);
    initModal({ modalOverlay, modalClose, modalSearch, modalResults });
    startTimeUpdates();

    initChatFeatures();

    if (window.updateBadges) window.updateBadges();

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

            const createdAt = parseInt(wrap?.dataset.createdAt);
            if (createdAt && (Date.now() - createdAt) > 3600000) {
                if (window.showToast) window.showToast('Ya no puedes editar este mensaje (pasó más de 1 hora)', 'error');
                return;
            }

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

    /* ─── Share project modal ─── */
    const shareModal = document.getElementById('msgsShareModal');
    const shareList = document.getElementById('msgsShareList');
    const shareLoading = document.getElementById('msgsShareLoading');
    const shareEmpty = document.getElementById('msgsShareEmpty');
    const shareSend = document.getElementById('msgsShareSend');
    const shareCancel = document.getElementById('msgsShareCancel');
    const shareClose = document.getElementById('msgsShareModalClose');
    let selectedProjectId = null;
    let selectedProjectTitle = null;

    async function openShareModal() {
        selectedProjectId = null;
        selectedProjectTitle = null;
        shareSend.disabled = true;
        shareList.innerHTML = '';
        shareLoading.style.display = 'flex';
        shareEmpty.style.display = 'none';

        shareModal.classList.add('is-open');
        shareModal.setAttribute('aria-hidden', 'false');
        window.lockBodyScroll?.();

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
            const res = await fetch('/messages/projects', {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                credentials: 'same-origin',
            });

            if (!res.ok) throw new Error('Error al cargar proyectos');

            const projects = await res.json();
            shareLoading.style.display = 'none';

            if (!projects.length) {
                shareEmpty.style.display = 'flex';
                return;
            }

            projects.forEach((p) => {
                const item = document.createElement('button');
                item.className = 'msgs-share-item';
                item.dataset.projectId = p.id;
                item.dataset.projectTitle = p.title;
                item.textContent = p.title;
                item.addEventListener('click', () => {
                    shareList.querySelectorAll('.msgs-share-item').forEach((el) => el.classList.remove('selected'));
                    item.classList.add('selected');
                    selectedProjectId = p.id;
                    selectedProjectTitle = p.title;
                    shareSend.disabled = false;
                });
                shareList.appendChild(item);
            });
        } catch (err) {
            console.error('[Fluxa Messages]', err);
            shareLoading.style.display = 'none';
            shareEmpty.style.display = 'flex';
            shareEmpty.querySelector('p').textContent = 'Error al cargar proyectos.';
            if (window.showToast) window.showToast('Error al cargar proyectos', 'error');
        }
    }

    function closeShareModal() {
        shareModal.classList.remove('is-open');
        shareModal.setAttribute('aria-hidden', 'true');
        window.unlockBodyScroll?.();
    }

    document.getElementById('msgsShareProjectBtn')?.addEventListener('click', openShareModal);

    shareCancel?.addEventListener('click', closeShareModal);
    shareClose?.addEventListener('click', closeShareModal);

    shareModal?.addEventListener('click', (e) => {
        if (e.target === shareModal) closeShareModal();
    });

    shareSend?.addEventListener('click', async () => {
        if (!selectedProjectId) return;

        const projectUrl = `/projects/${selectedProjectId}`;
        const body = `Compartió un proyecto: ${selectedProjectTitle}\n${window.location.origin}${projectUrl}`;
        const convId = sendBtn?.dataset.convId;
        const recipient = sendBtn?.dataset.recipient;

        shareSend.disabled = true;

        try {
            const data = await sendMessage(body, convId, recipient);
            closeShareModal();

            const now = new Date();
            const dateKey = now.toLocaleDateString('en-CA', { timeZone: 'America/Bogota' });
            const tempBubble = createOwnBubble(body, '', now.toISOString());
            if (bubbleList) {
                ensureDateSeparator(bubbleList, dateKey);
                bubbleList.appendChild(tempBubble);
                scrollToBottom(bubbleList, true);
                tempBubble.dataset.msgId = data.id ?? '';
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
                }
            }

            if (window.updateBadges) window.updateBadges();
        } catch (err) {
            console.error('[Fluxa Messages]', err);
            if (window.showToast) window.showToast('Error al enviar el proyecto', 'error');
        }

        shareSend.disabled = false;
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && shareModal?.classList.contains('is-open')) closeShareModal();
    });
});