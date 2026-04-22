/* ─────────────────────────────────────────────────────────────
   resources/js/core/messages/index.js
   Sistema de mensajería de Fluxa — lógica de UI
───────────────────────────────────────────────────────────── */

document.addEventListener('DOMContentLoaded', () => {

    /* ── Referencias DOM ── */
    const layout = document.querySelector('.msgs-layout');
    const convList = document.getElementById('msgsConvList');
    const sidebarSearch = document.getElementById('msgsSearch');
    const input = document.getElementById('msgsInput');
    const sendBtn = document.getElementById('msgsSendBtn');
    const bubbleList = document.getElementById('msgsBubbleList');
    const backBtn = document.getElementById('msgsBackBtn');
    const btnNuevo = document.getElementById('btnNuevoMensaje');
    const btnNuevoEmpty = document.getElementById('btnNuevoMensajeEmpty');
    const modalOverlay = document.getElementById('msgsModalOverlay');
    const modalClose = document.getElementById('msgsModalClose');
    const modalSearch = document.getElementById('msgsModalSearch');
    const modalResults = document.getElementById('msgsModalResults');

    /* FIX: declarar vars de reply para evitar ReferenceError al enviar */
    let replyingToMsgId = null;
    let replyToMessage = null;

    /* ══════════════════════════════════════════
       SCROLL AL ÚLTIMO MENSAJE
    ══════════════════════════════════════════ */
    function scrollToBottom(smooth = false) {
        if (!bubbleList) return;
        bubbleList.scrollTo({
            top: bubbleList.scrollHeight,
            behavior: smooth ? 'smooth' : 'instant',
        });
    }

    scrollToBottom();

    /* ══════════════════════════════════════════
       AUTOSIZE DEL TEXTAREA
    ══════════════════════════════════════════ */
    function autosizeInput() {
        if (!input) return;
        input.style.height = 'auto';
        input.style.height = Math.min(input.scrollHeight, 140) + 'px';
    }

    if (input) {
        input.addEventListener('input', autosizeInput);
        autosizeInput();
    }

    /* ══════════════════════════════════════════
       HABILITAR / DESHABILITAR BOTÓN ENVIAR
    ══════════════════════════════════════════ */
    function syncSendBtn() {
        if (!sendBtn || !input) return;
        sendBtn.disabled = input.value.trim().length === 0;
    }

    if (input) {
        input.addEventListener('input', syncSendBtn);
        syncSendBtn();
    }

    /* ══════════════════════════════════════════
       ENVÍO DE MENSAJE (Enter sin Shift)
    ══════════════════════════════════════════ */
    if (input) {
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });
    }

    if (sendBtn) {
        sendBtn.addEventListener('click', sendMessage);
    }

    async function sendMessage() {
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
            bubbleList.appendChild(tempBubble);
            scrollToBottom(true);
        }

        input.value = '';
        autosizeInput();

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
            const url = convId
                ? `/messages/${convId}`
                : `/messages/user/${recipient}`;

            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    body,
                    ...(replyingToMsgId ? { parent_id: replyingToMsgId } : {}),
                }),
            });

            if (res.ok && replyingToMsgId) {
                replyingToMsgId = null;
                replyToMessage = null;
            }

            if (!res.ok) throw new Error('Error al enviar el mensaje');

            const data = await res.json();

            tempBubble.dataset.msgId = data.id ?? '';
            tempBubble.querySelector('.msgs-bubble-time')?.classList.remove('sending');

        } catch (err) {
            console.error('[Fluxa Messages]', err);
            tempBubble.classList.add('msgs-bubble-failed');
            const time = tempBubble.querySelector('.msgs-bubble-time');
            if (time) time.textContent = 'Error al enviar';
        }

        input.disabled = false;
        input.focus();
        syncSendBtn();
    }

    function createOwnBubble(text, status = '') {
        const now = new Date();
        const time = now.toLocaleTimeString('es-CO', { hour: '2-digit', minute: '2-digit', hour12: false });

        const wrap = document.createElement('div');
        wrap.className = 'msgs-bubble-wrap mine';

        const bubble = document.createElement('div');
        bubble.className = 'msgs-bubble msgs-bubble-mine';
        bubble.textContent = text;

        const timeSpan = document.createElement('span');
        timeSpan.className = `msgs-bubble-time${status ? ' ' + status : ''}`;
        timeSpan.textContent = time;

        bubble.appendChild(timeSpan);
        wrap.appendChild(bubble);
        return wrap;
    }

    /* ══════════════════════════════════════════
       BÚSQUEDA EN LA LISTA DE CONVERSACIONES
    ══════════════════════════════════════════ */
    if (sidebarSearch) {
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

    /* ══════════════════════════════════════════
       NAVEGACIÓN MÓVIL — sidebar ↔ chat
    ══════════════════════════════════════════ */
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

    /* ══════════════════════════════════════════
       MODAL — NUEVA CONVERSACIÓN
    ══════════════════════════════════════════ */
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

    btnNuevo?.addEventListener('click', openModal);
    btnNuevoEmpty?.addEventListener('click', openModal);
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

        searchTimeout = setTimeout(() => searchUsers(query), 320);
    });

    async function searchUsers(query) {
        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
            const res = await fetch(`/users/search?q=${encodeURIComponent(query)}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
            });

            if (!res.ok) throw new Error('Error en la búsqueda');

            const users = await res.json();
            renderModalResults(users);

        } catch (err) {
            console.error('[Fluxa Messages] Search error:', err);
            if (modalResults) {
                modalResults.innerHTML = '<p class="msgs-modal-hint">No se pudo realizar la búsqueda.</p>';
            }
        }
    }

    function renderModalResults(users) {
        if (!modalResults) return;

        if (!users.length) {
            modalResults.innerHTML = '<p class="msgs-modal-hint">No se encontraron usuarios.</p>';
            return;
        }

        modalResults.innerHTML = '';

        users.forEach((user) => {
            const item = document.createElement('a');
            item.href = `/messages/user/${user.username}`;
            item.className = 'msgs-modal-user-item';
            item.setAttribute('role', 'option');

            item.innerHTML = `
                <img src="${escapeHtml(user.avatar_url)}"
                    alt="${escapeHtml(user.name)}"
                    class="msgs-modal-user-avatar"
                    onerror="this.src='/img/default-avatar.png'">
                <div class="msgs-modal-user-info">
                    <div class="msgs-modal-user-name">${escapeHtml(user.name)}</div>
                    <div class="msgs-modal-user-handle">@${escapeHtml(user.username)}</div>
                </div>
            `;

            modalResults.appendChild(item);
        });
    }

    /* ══════════════════════════════════════════
       UTILIDADES
    ══════════════════════════════════════════ */
    function escapeHtml(str) {
        const div = document.createElement('div');
        div.appendChild(document.createTextNode(String(str ?? '')));
        return div.innerHTML;
    }

    /* ══════════════════════════════════════════
       MENÚ DE OPCIONES DE CONVERSACIÓN
    ══════════════════════════════════════════ */
    let activeConvMenu = null;

    convList?.addEventListener('click', (e) => {
        const menuBtn = e.target.closest('.msgs-conv-menu-btn');
        if (!menuBtn) {
            if (activeConvMenu) {
                activeConvMenu.remove();
                activeConvMenu = null;
            }
            return;
        }

        e.preventDefault();
        e.stopPropagation();

        const convId = menuBtn.dataset.convId;
        const userId = menuBtn.dataset.userId;
        const wrapper = menuBtn.closest('.msgs-conv-item-wrapper');

        if (activeConvMenu) {
            activeConvMenu.remove();
        }

        const menu = document.createElement('div');
        menu.className = 'msgs-conv-menu';
        menu.innerHTML = `
            <button class="msgs-conv-menu-item" data-action="block" data-user-id="${userId}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" /></svg>
                Bloquear usuario
            </button>
            <button class="msgs-conv-menu-item danger" data-action="delete" data-conv-id="${convId}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                Eliminar chat
            </button>
        `;

        wrapper.style.position = 'relative';
        wrapper.appendChild(menu);
        activeConvMenu = menu;

        menu.querySelectorAll('.msgs-conv-menu-item').forEach(item => {
            item.addEventListener('click', async (evt) => {
                evt.stopPropagation();
                handleConvAction(item.dataset.action, item, convId, userId);
            });
        });
    });

    async function handleConvAction(action, item, convId, userId) {
        if (activeConvMenu) {
            activeConvMenu.remove();
            activeConvMenu = null;
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

        switch (action) {
            case 'block':
                alert('Función de bloqueo pronto disponible');
                break;

            case 'delete':
                if (!confirm('¿Eliminar esta conversación?')) return;
                try {
                    const res = await fetch(`/messages/conversation/${convId}`, {
                        method: 'PUT',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                    });

                    if (res.ok) {
                        /* FIX: redirect limpio sin el parámetro ?conv para evitar
                           que el controlador vuelva a cargar la conv eliminada */
                        window.location.href = '/messages';
                    } else {
                        alert('Error al eliminar');
                    }
                } catch (err) {
                    console.error('[Fluxa Messages] Delete chat error:', err);
                    alert('Error al eliminar chat');
                }
                break;
        }
    }

    /* ══════════════════════════════════════════
       REALTIME - Escuchar mensajes nuevos
    ══════════════════════════════════════════ */
    function initReverb() {
        if (typeof window.Echo === 'undefined') return;

        const userId = sendBtn?.dataset?.userId;
        if (!userId) return;

        const currentConvId = new URLSearchParams(window.location.search).get('conv');

        if (currentConvId) {
            window.Echo.private(`conversation.${currentConvId}`)
                .listen('NewMessageEvent', (e) => {
                    if (e.message) {
                        addMessageToBubbleList(e.message);
                    }
                });
        }

        window.Echo.private(`user.${userId}`)
            .listen('NewMessageEvent', (e) => {
                updateConversationList(e.message);
            });
    }

    function addMessageToBubbleList(message) {
        if (!bubbleList) return;

        const userId = sendBtn?.dataset?.userId;
        if (!userId) return;

        const currentConvId = new URLSearchParams(window.location.search).get('conv');

        if (message.conversation_id == currentConvId) {
            const isMine = message.sender_id === userId;
            const bubble = createIncomingBubble(message.body, message.sender, message.created_at);
            bubbleList.appendChild(bubble);
            scrollToBottom(true);
        }

        if (typeof updateUnreadBadge === 'function') {
            updateUnreadBadge();
        }
    }

    function createIncomingBubble(text, sender, createdAt) {
        const wrap = document.createElement('div');
        wrap.className = 'msgs-bubble-wrap theirs';

        const avatar = document.createElement('img');
        avatar.src = sender.avatar_url;
        avatar.alt = sender.name;
        avatar.className = 'msgs-bubble-avatar';
        avatar.onerror = function() { this.src = '/img/default-avatar.png'; };

        const bubble = document.createElement('div');
        bubble.className = 'msgs-bubble msgs-bubble-theirs';
        bubble.textContent = text;

        const timeSpan = document.createElement('span');
        timeSpan.className = 'msgs-bubble-time';
        const time = new Date(createdAt).toLocaleTimeString('es-CO', { hour: '2-digit', minute: '2-digit', hour12: false });
        timeSpan.textContent = time;

        bubble.appendChild(timeSpan);
        wrap.appendChild(avatar);
        wrap.appendChild(bubble);

        return wrap;
    }

    function updateConversationList(message) {
        const convItem = document.querySelector(`.msgs-conv-item-wrapper a[href*="conv=${message.conversation_id}"]`);
        if (convItem) {
            window.location.reload();
        }
    }

    initReverb();

});