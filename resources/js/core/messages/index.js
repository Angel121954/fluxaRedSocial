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
                    'X-Socket-ID': window.Echo?.socketId() ?? '',
                },
                body: JSON.stringify({ body }),
            });

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
       NAVEGACIÓN MÓVIL
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
            item.href = `/messages/chat/${user.username}`;
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
       TIEMPO REAL — ESCUCHAR MENSAJES
    ══════════════════════════════════════════ */
    const activeConvId = sendBtn?.dataset.convId;

    console.log('[Reverb] convId:', activeConvId, 'Echo:', !!window.Echo);

    if (activeConvId && window.Echo) {
        const channel = window.Echo.private(`messages.${activeConvId}`);

        // ✅ FIX 2 — Punto al inicio del nombre del evento (broadcastAs)
        channel.listen('.message.sent', (msg) => {
            console.log('[Reverb] Mensaje recibido:', msg);
            appendReceivedBubble(msg);
            scrollToBottom(true);
            markConvAsUnread(msg.conversation_id);
        });

        channel.on('pusher:subscription_succeeded', () => {
            console.log('[Reverb] Suscrito OK a messages.' + activeConvId);
        });

        channel.on('pusher:subscription_error', (err) => {
            console.error('[Reverb] Error suscripción:', err);
        });

        console.log('[Reverb] Escuchando messages.' + activeConvId);
    }

    function appendReceivedBubble(msg) {
        if (!bubbleList) return;

        const wrap = document.createElement('div');
        wrap.className = 'msgs-bubble-wrap theirs';

        wrap.innerHTML = `
            <img src="${escapeHtml(msg.sender.avatar_url)}"
                 alt="" class="msgs-bubble-avatar"
                 onerror="this.src='/img/default-avatar.png'">
            <div class="msgs-bubble msgs-bubble-theirs">
                ${escapeHtml(msg.body)}
                <span class="msgs-bubble-time">${formatTime(msg.created_at)}</span>
            </div>
        `;

        bubbleList.appendChild(wrap);
    }

    function markConvAsUnread(convId) {
        const convItem = convList?.querySelector(`[href*="conv=${convId}"]`);
        if (!convItem) return;

        if (!convItem.querySelector('.msgs-unread-badge')) {
            const badge = document.createElement('span');
            badge.className = 'msgs-unread-badge';
            badge.textContent = '1';
            const rowBottom = convItem.querySelector('.msgs-conv-row-bottom');
            if (rowBottom) rowBottom.appendChild(badge);
        }
    }

    function formatTime(isoString) {
        const d = new Date(isoString);
        return d.toLocaleTimeString('es-CO', { hour: '2-digit', minute: '2-digit', hour12: false });
    }

    function escapeHtml(str) {
        const div = document.createElement('div');
        div.appendChild(document.createTextNode(String(str ?? '')));
        return div.innerHTML;
    }

});