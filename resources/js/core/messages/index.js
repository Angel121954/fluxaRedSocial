/* ─────────────────────────────────────────────────────────────
   resources/js/core/messages/index.js
   Sistema de mensajería de Fluxa — lógica de UI
   Maneja: autosize del textarea, envío de mensajes,
   búsqueda de conversaciones, modal de nueva conversación,
   scroll al último mensaje, y navegación móvil.
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

        /* Deshabilitar mientras se envía */
        sendBtn.disabled = true;
        input.disabled = true;

        /* Burbuja optimista (se agrega al instante) */
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
                body: JSON.stringify({ body }),
            });

            if (!res.ok) throw new Error('Error al enviar el mensaje');

            const data = await res.json();

            /* Actualizar burbuja temporal con id y check enviado */
            tempBubble.dataset.msgId = data.id ?? '';
            tempBubble.querySelector('.msgs-bubble-time')?.classList.remove('sending');

        } catch (err) {
            console.error('[Fluxa Messages]', err);
            /* Marcar burbuja como fallida */
            tempBubble.classList.add('msgs-bubble-failed');
            const time = tempBubble.querySelector('.msgs-bubble-time');
            if (time) time.textContent = 'Error al enviar';
        }

        input.disabled = false;
        input.focus();
        syncSendBtn();
    }

    /**
     * Crea una burbuja propia para inserción optimista.
     * @param {string} text
     * @param {string} [status] - 'sending'
     * @returns {HTMLElement}
     */
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
                const matches = name.includes(query) || preview.includes(query);
                item.style.display = matches ? '' : 'none';
            });
        });
    }

    /* ══════════════════════════════════════════
       NAVEGACIÓN MÓVIL — sidebar ↔ chat
    ══════════════════════════════════════════ */
    /* Si hay una conversación activa al cargar, activar el modo chat en móvil */
    if (bubbleList && layout) {
        layout.classList.add('chat-active');
    }

    if (backBtn && layout) {
        backBtn.addEventListener('click', () => {
            layout.classList.remove('chat-active');
        });
    }

    /* Al hacer clic en una conversación en móvil, navegar y activar chat-active */
    convList?.addEventListener('click', (e) => {
        const item = e.target.closest('.msgs-conv-item');
        if (!item) return;
        /* La navegación real la hace el href del link,
           pero marcamos chat-active para la animación móvil */
        if (window.innerWidth <= 680 && layout) {
            layout.classList.add('chat-active');
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

    /* ── Búsqueda de usuarios en el modal ── */
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

});