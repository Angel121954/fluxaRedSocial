import { scrollToBottom, autosizeInput } from './messageUtils.js';
import { searchUsers, sendMediaMessage } from './messageService.js';
import { renderModalResults, createOwnMediaBubble, ensureDateSeparator } from './messageRenderer.js';
import { updateConvPreview } from './sender.js';

export function initUI({ input, sendBtn, bubbleList, convList, sidebarSearch, layout, backBtn }) {
    scrollToBottom(bubbleList);

    if (input) {
        input.addEventListener('input', () => autosizeInput(input));
        autosizeInput(input);
    }

    function syncSendBtn() {
        if (!sendBtn || !input) return;
        if (input.disabled) {
            sendBtn.disabled = true;
            return;
        }
        sendBtn.disabled = input.value.trim().length === 0;
    }

    if (input) {
        input.addEventListener('input', syncSendBtn);
        syncSendBtn();
    }

    return { syncSendBtn };
}

export function initConversationSearch(sidebarSearch, convList) {
    if (!sidebarSearch) return;

    let emptyMsg = null;

    function updateSearchEmptyState() {
        if (!convList) return;
        const visible = convList.querySelectorAll('.msgs-conv-item[style*="display: none"]');
        const total = convList.querySelectorAll('.msgs-conv-item');
        const hasQuery = sidebarSearch.value.trim().length > 0;

        if (hasQuery && total.length > 0 && visible.length === total.length) {
            if (!emptyMsg) {
                emptyMsg = document.createElement('div');
                emptyMsg.className = 'msgs-empty-sidebar';
                emptyMsg.style.padding = '2rem 1rem';
                emptyMsg.innerHTML = `
                    <p style="font-size:0.875rem;color:var(--ink-400);font-weight:500;text-align:center;">
                        No se encontraron conversaciones con "<strong>${escapeHtml(sidebarSearch.value.trim())}</strong>"
                    </p>
                `;
                convList.appendChild(emptyMsg);
            }
        } else {
            if (emptyMsg) {
                emptyMsg.remove();
                emptyMsg = null;
            }
        }
    }

    sidebarSearch.addEventListener('input', () => {
        const query = sidebarSearch.value.trim().toLowerCase();
        const items = convList?.querySelectorAll('.msgs-conv-item') ?? [];

        items.forEach((item) => {
            const name = item.querySelector('.msgs-conv-name')?.textContent?.toLowerCase() ?? '';
            const preview = item.querySelector('.msgs-conv-preview')?.textContent?.toLowerCase() ?? '';
            item.style.display = (name.includes(query) || preview.includes(query)) ? '' : 'none';
        });

        updateSearchEmptyState();
    });
}

function escapeHtml(str) {
    const div = document.createElement('div');
    div.appendChild(document.createTextNode(String(str ?? '')));
    return div.innerHTML;
}

export function initMobileNavigation(convList, layout, backBtn, bubbleList) {
    const isMobile = () => window.innerWidth <= 768;

    if (bubbleList && layout && isMobile()) {
        layout.classList.add('chat-active');
    }

    if (backBtn && layout) {
        backBtn.addEventListener('click', () => {
            layout.classList.remove('chat-active');
            // Limpiar ?conv= de la URL sin recargar
            if (window.history.replaceState) {
                const url = new URL(window.location);
                url.searchParams.delete('conv');
                window.history.replaceState({}, '', url);
            }
        });
    }

    // Al cambiar tamaño de ventana, sincronizar estado mobile
    let resizeTimer = null;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            const mobile = isMobile();
            if (!mobile && bubbleList) {
                layout?.classList.add('chat-active');
            }
        }, 150);
    });
}

export function initModal({ modalOverlay, modalClose, modalSearch, modalResults }) {
    let searchTimeout = null;

    function openModal() {
        if (!modalOverlay) return;
        modalOverlay.classList.add('active');
        modalOverlay.setAttribute('aria-hidden', 'false');
        lockBodyScroll();
        setTimeout(() => modalSearch?.focus(), 80);
    }

    function closeModal() {
        if (!modalOverlay) return;
        modalOverlay.classList.remove('active');
        modalOverlay.setAttribute('aria-hidden', 'true');
        unlockBodyScroll();
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
        if (e.key === 'Escape' && modalOverlay?.classList.contains('active')) closeModal();
    });

    if (modalSearch && modalResults) {
        modalSearch.addEventListener('input', () => {
            clearTimeout(searchTimeout);
            const query = modalSearch.value.trim();

            if (!query) {
                modalResults.innerHTML = '<p class="msgs-modal-hint">Empieza a escribir para buscar usuarios.</p>';
                return;
            }

            modalResults.innerHTML = '<p class="msgs-modal-hint">Buscando...</p>';

            searchTimeout = setTimeout(async () => {
                try {
                    const users = await searchUsers(query);
                    renderModalResults(users, modalResults);
                } catch {
                    modalResults.innerHTML = '<p class="msgs-modal-hint">Error al buscar usuarios.</p>';
                }
            }, 300);
        });
    }
}

/**
 * Tabs de filtro: Todos / No leídos / Grupos
 */
export function initConversationTabs(convList) {
    const tabs = document.querySelectorAll('.msgs-tab');
    if (!tabs.length || !convList) return;

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            // Actualizar estado de tabs
            tabs.forEach(t => {
                t.classList.remove('active');
                t.setAttribute('aria-selected', 'false');
            });
            tab.classList.add('active');
            tab.setAttribute('aria-selected', 'true');

            const type = tab.dataset.tab;
            const items = convList.querySelectorAll('.msgs-conv-item');

            items.forEach(item => {
                if (type === 'all') {
                    item.classList.remove('tab-hidden');
                } else if (type === 'unread') {
                    const hasUnread = item.dataset.unread === 'true';
                    item.classList.toggle('tab-hidden', !hasUnread);
                } else if (type === 'groups') {
                    const isGroup = item.dataset.type === 'group';
                    item.classList.toggle('tab-hidden', !isGroup);
                }
            });
        });
    });
}

/**
 * Dropdown "más opciones" del header
 */
export function initMoreDropdown() {
    const moreBtn = document.getElementById('msgsMoreBtn');
    const dropdown = document.getElementById('msgsMoreDropdown');
    if (!moreBtn || !dropdown) return;

    moreBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        const isOpen = dropdown.classList.contains('open');
        dropdown.classList.toggle('open', !isOpen);
        dropdown.setAttribute('aria-hidden', isOpen ? 'true' : 'false');
    });

    document.addEventListener('click', (e) => {
        if (!dropdown.contains(e.target) && e.target !== moreBtn) {
            dropdown.classList.remove('open');
            dropdown.setAttribute('aria-hidden', 'true');
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            dropdown.classList.remove('open');
            dropdown.setAttribute('aria-hidden', 'true');
        }
    });
}

export function initToolbarActions() {
    const showComingSoon = (label) => {
        if (window.showToast) {
            window.showToast(`${label} próximamente`, 'info');
        }
    };

    document.getElementById('msgsCallBtn')?.addEventListener('click', () => showComingSoon('Llamadas de voz'));
    document.getElementById('msgsVideoBtn')?.addEventListener('click', () => showComingSoon('Videollamadas'));
    document.getElementById('msgsChatSearchBtn')?.addEventListener('click', () => showComingSoon('Buscar en la conversación'));
    document.getElementById('msgsCodeBtn')?.addEventListener('click', () => {
        const input = document.getElementById('msgsInput');
        if (!input) return;
        const snippet = '```\n\n```';
        const start = input.selectionStart;
        const end = input.selectionEnd;
        input.value = input.value.slice(0, start) + snippet + input.value.slice(end);
        input.selectionStart = input.selectionEnd = start + 4;
        input.focus();
        input.dispatchEvent(new Event('input'));
    });
    // Share project button is now handled in index.js (opens modal)
    document.getElementById('msgsClearBtn')?.addEventListener('click', () => showComingSoon('Limpiar conversación'));

    /* ─── Input ocultos para upload ─── */
    const inputBar = document.getElementById('msgsInputBar');
    if (!inputBar) return;

    const imageInput = document.createElement('input');
    imageInput.type = 'file';
    imageInput.accept = 'image/jpeg,image/png,image/gif,image/webp';
    imageInput.hidden = true;
    imageInput.id = 'msgsImageInput';

    inputBar.appendChild(imageInput);

    function uploadFile(file, mediaType) {
        const sendBtn = document.getElementById('msgsSendBtn');
        const convId = sendBtn?.dataset.convId;
        if (!convId) {
            if (window.showToast) window.showToast('Selecciona una conversación primero', 'error');
            return;
        }

        const input = document.getElementById('msgsInput');
        const body = input?.value.trim() || '';

        const formData = new FormData();
        formData.append('file', file);
        formData.append('media_type', mediaType);
        if (body) formData.append('body', body);

        const bubbleList = document.getElementById('msgsBubbleList');

        const previewText = mediaType === 'image' ? 'Tú: 📷 Imagen' : 'Tú: 📎 ' + file.name;
        updateConvPreview(convId, previewText);

        const now = new Date();
        const dateKey = now.toLocaleDateString('en-CA', { timeZone: 'America/Bogota' });
        const tempBubble = createOwnMediaBubble(
            { id: null, media_type: mediaType, media_url: URL.createObjectURL(file), media_name: file.name, media_size: file.size },
            body,
            now.toISOString(),
            'sending'
        );
        if (bubbleList) {
            ensureDateSeparator(bubbleList, dateKey);
            bubbleList.appendChild(tempBubble);
            scrollToBottom(bubbleList, true);
            if (input) {
                input.value = '';
                autosizeInput(input);
                input.dispatchEvent(new Event('input'));
            }
        }

        sendMediaMessage(formData, convId)
            .then((data) => {
                tempBubble.dataset.msgId = data.id ?? '';
                const time = tempBubble.querySelector('.msgs-bubble-time');
                if (time) time.classList.remove('sending');

                if (data.media_url) {
                    const img = tempBubble.querySelector('.msgs-media-img');
                    if (img) {
                        URL.revokeObjectURL(img.src);
                        img.src = data.media_url;
                    }
                }
            })
            .catch((err) => {
                console.error('[Fluxa Messages]', err);
                const time = tempBubble.querySelector('.msgs-bubble-time');
                if (time) {
                    time.textContent = 'Error al enviar';
                    time.classList.remove('sending');
                }
                tempBubble.classList.add('msgs-bubble-failed');

                if (window.showToast) {
                    window.showToast('Error al enviar el archivo', 'error');
                }
            });
    }

    /* ─── Image upload ─── */
    document.getElementById('msgsImageBtn')?.addEventListener('click', () => {
        imageInput.click();
    });

    imageInput.addEventListener('change', () => {
        const file = imageInput.files?.[0];
        if (!file) return;
        uploadFile(file, 'image');
        imageInput.value = '';
    });

    /* ─── Attach / autocomplete https:// ─── */
    document.getElementById('msgsAttachBtn')?.addEventListener('click', () => {
        const input = document.getElementById('msgsInput');
        if (!input) return;
        const start = input.selectionStart;
        const end = input.selectionEnd;
        const text = input.value;
        input.value = text.slice(0, start) + 'https://' + text.slice(end);
        input.selectionStart = input.selectionEnd = start + 8;
        input.focus();
        input.dispatchEvent(new Event('input', { bubbles: true }));
    });

    /* GIF button is handled by giphy.js */
}