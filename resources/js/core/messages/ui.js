import { scrollToBottom, autosizeInput } from './messageUtils.js';

export function initUI({ input, sendBtn, bubbleList, convList, sidebarSearch, layout, backBtn }) {
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

export function initConversationSearch(sidebarSearch, convList) {
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

export function initMobileNavigation(convList, layout, backBtn, bubbleList) {
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

export function initModal({ modalOverlay, modalClose, modalSearch, modalResults }) {
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