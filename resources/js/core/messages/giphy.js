import { scrollToBottom, autosizeInput } from './messageUtils.js';
import { createOwnMediaBubble, ensureDateSeparator } from './messageRenderer.js';
import { updateConvPreview } from './sender.js';
import { sendGif } from './messageService.js';

/* ─── GIPHY Picker ─── */
export function initGiphyPicker() {
    let searchTimeout = null;
    let currentOffset = 0;
    let currentQuery = '';
    let isLoading = false;
    let hasMore = true;

    const overlay = buildOverlay();
    const grid = document.getElementById('giphyGrid');
    const search = document.getElementById('giphySearch');
    const closeBtn = document.getElementById('giphyClose');
    const loader = document.getElementById('giphyLoader');
    const empty = document.getElementById('giphyEmpty');
    const body = document.querySelector('.giphy-body');

    document.getElementById('msgsGifBtn')?.addEventListener('click', open);

    closeBtn?.addEventListener('click', close);

    const modal = overlay.querySelector('.giphy-modal');
    if (modal) {
        modal.addEventListener('click', (e) => e.stopPropagation());
        modal.addEventListener('touchend', (e) => e.stopPropagation());
    }

    overlay.addEventListener('click', close);
    overlay.addEventListener('touchend', close);

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && overlay.classList.contains('active')) close();
    });

    search?.addEventListener('input', () => {
        clearTimeout(searchTimeout);
        const query = search.value.trim();
        currentQuery = query;

        if (!query) {
            resetGrid();
            loadTrending();
            return;
        }

        currentOffset = 0;
        hasMore = true;
        searchTimeout = setTimeout(() => searchGifs(query), 400);
    });

    grid?.addEventListener('click', (e) => {
        const item = e.target.closest('.giphy-item');
        if (item) selectGif(item.dataset.url);
    });

    body?.addEventListener('scroll', () => {
        if (isLoading || !hasMore) return;
        if (body.scrollHeight - body.scrollTop - body.clientHeight < 200) {
            currentQuery ? searchGifs(currentQuery) : loadTrending();
        }
    });

    function open() {
        overlay.setAttribute('aria-hidden', 'false');
        overlay.classList.add('active');
        window.lockBodyScroll?.();
        currentQuery = '';
        currentOffset = 0;
        hasMore = true;
        grid.innerHTML = '';
        loadTrending();
        search?.focus();
    }

    function close() {
        overlay.classList.remove('active');
        overlay.setAttribute('aria-hidden', 'true');
        window.unlockBodyScroll?.();
        if (search) search.value = '';
    }

    function resetGrid() {
        currentOffset = 0;
        hasMore = true;
        grid.innerHTML = '';
        empty.style.display = 'none';
    }

    async function loadTrending() {
        if (isLoading) return;
        isLoading = true;
        loader.style.display = 'flex';

        try {
            const res = await fetch('/api/giphy/trending?offset=' + currentOffset);
            const data = await res.json();
            appendGifs(data.gifs);
            hasMore = data.gifs.length > 0;
            currentOffset += data.gifs.length;
        } catch {
            empty.style.display = 'block';
            empty.innerHTML = '<p>Error al cargar GIFs</p>';
        }

        isLoading = false;
        loader.style.display = 'none';
    }

    async function searchGifs(query) {
        if (isLoading) return;
        isLoading = true;
        loader.style.display = 'flex';
        empty.style.display = 'none';

        try {
            const res = await fetch('/api/giphy/search?q=' + encodeURIComponent(query) + '&offset=' + currentOffset);
            const data = await res.json();
            if (currentOffset === 0) grid.innerHTML = '';
            appendGifs(data.gifs);
            hasMore = data.gifs.length > 0;
            currentOffset += data.gifs.length;
        } catch {
            empty.style.display = 'block';
            empty.innerHTML = '<p>Error al buscar GIFs</p>';
        }

        isLoading = false;
        loader.style.display = 'none';
    }

    function appendGifs(gifs) {
        if (!gifs.length && currentOffset === 0) {
            empty.style.display = 'block';
            return;
        }

        gifs.forEach((gif) => {
            const item = document.createElement('button');
            item.className = 'giphy-item';
            item.type = 'button';
            item.setAttribute('aria-label', gif.title || 'GIF');
            item.dataset.url = gif.original_url;

            const img = document.createElement('img');
            img.className = 'giphy-img';
            img.src = gif.preview_url;
            img.alt = gif.title || 'GIF';
            img.loading = 'lazy';

            item.appendChild(img);
            grid.appendChild(item);
        });
    }

    function selectGif(gifUrl) {
        const sendBtn = document.getElementById('msgsSendBtn');
        const convId = sendBtn?.dataset.convId;
        if (!convId) {
            if (window.showToast) window.showToast('Selecciona una conversación primero', 'error');
            return;
        }

        const input = document.getElementById('msgsInput');
        const body = input?.value.trim() || '';

        const formData = new FormData();
        formData.append('gif_url', gifUrl);
        formData.append('media_type', 'gif');
        if (body) formData.append('body', body);

        const bubbleList = document.getElementById('msgsBubbleList');
        updateConvPreview(convId, 'Tú: GIF');

        const now = new Date();
        const dateKey = now.toLocaleDateString('en-CA', { timeZone: 'America/Bogota' });
        const tempBubble = createOwnMediaBubble(
            { id: null, media_type: 'gif', media_url: gifUrl, media_name: 'GIF', media_size: 0 },
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

        close();

        sendGif(formData, convId)
            .then((data) => {
                tempBubble.dataset.msgId = data.id ?? '';
                const time = tempBubble.querySelector('.msgs-bubble-time');
                if (time) time.classList.remove('sending');
                if (data.media_url) {
                    const img = tempBubble.querySelector('.msgs-media-img');
                    if (img) img.src = data.media_url;
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
                if (window.showToast) window.showToast('Error al enviar el GIF', 'error');
            });
    }
}

function buildOverlay() {
    const overlay = document.createElement('div');
    overlay.id = 'giphyOverlay';
    overlay.className = 'giphy-overlay';
    overlay.setAttribute('aria-hidden', 'true');
    overlay.innerHTML = `
        <div class="giphy-modal" role="dialog" aria-modal="true" aria-label="Selector de GIFs">
            <div class="giphy-header">
                <div class="giphy-search-wrap">
                    <svg class="giphy-search-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="search" id="giphySearch" class="giphy-search" placeholder="Buscar GIFs..." autocomplete="off" aria-label="Buscar GIFs">
                </div>
                <button id="giphyClose" class="giphy-close" aria-label="Cerrar">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>
            <div class="giphy-body">
                <div id="giphyGrid" class="giphy-grid"></div>
                <div id="giphyLoader" class="giphy-loader" style="display:none">
                    <div class="giphy-spinner"></div>
                </div>
                <div id="giphyEmpty" class="giphy-empty" style="display:none">
                    <p>No se encontraron GIFs</p>
                </div>
            </div>
            <div class="giphy-footer">
                <span class="giphy-attribution">Powered by GIPHY</span>
            </div>
        </div>
    `;
    document.body.appendChild(overlay);
    return overlay;
}
