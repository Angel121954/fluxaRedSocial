document.addEventListener('DOMContentLoaded', function () {
    initSearch(document.getElementById('globalSearch'), document.getElementById('searchResults'));

    const mobileInput = document.getElementById('mobileSearch');
    if (mobileInput) {
        mobileInput.addEventListener('input', function () {
            const q = this.value.trim();
            const desktopInput = document.getElementById('globalSearch');
            if (desktopInput) desktopInput.value = q;
            if (q.length >= 2) performSearch(q, document.getElementById('searchResults'));
        });
        mobileInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                if (this.value.trim().length >= 2) {
                    window.location.href = `/explore?q=${encodeURIComponent(this.value.trim())}`;
                }
            }
        });
    }
});

export function initSearch(input, resultsEl) {
    if (!input || !resultsEl) return;
    let timeout;

    input.addEventListener('input', function () {
        clearTimeout(timeout);
        const q = this.value.trim();
        if (q.length < 2) {
            resultsEl.classList.remove('active');
            resultsEl.innerHTML = '';
            return;
        }
        timeout = setTimeout(() => performSearch(q, resultsEl), 300);
    });

    input.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            if (this.value.trim().length >= 2) {
                window.location.href = `/explore?q=${encodeURIComponent(this.value.trim())}`;
            }
        }
    });

    input.addEventListener('focus', function () {
        if (this.value.trim().length >= 2) resultsEl.classList.add('active');
    });
}

function performSearch(query, resultsEl) {
    resultsEl.innerHTML = '<div class="search-results-empty">Buscando…</div>';
    resultsEl.classList.add('active');

    fetch(`/api/search?q=${encodeURIComponent(query)}`)
        .then(res => res.json())
        .then(data => {
            if (data.users.length === 0 && data.projects.length === 0) {
                resultsEl.innerHTML = '<div class="search-results-empty">Sin resultados</div>';
                return;
            }

            let html = '';
            data.users.forEach(user => {
                html += `
                    <a href="/profile/${user.username}" class="search-result-item">
                        <img src="${user.avatar || '/img/default-avatar.png'}" alt="${user.name}" class="search-result-avatar">
                        <div class="search-result-info">
                            <div class="search-result-name">${user.name}</div>
                            <div class="search-result-type">${user.username}</div>
                        </div>
                        <svg class="search-result-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </a>
                `;
            });
            data.projects.forEach(project => {
                html += `
                    <a href="/project/${project.id}" class="search-result-item">
                        ${project.thumbnail ? `<img src="${project.thumbnail}" alt="${project.name}" class="search-result-avatar">` : `<div class="search-result-avatar search-result-avatar--placeholder"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" /></svg></div>`}
                        <div class="search-result-info">
                            <div class="search-result-name">${project.name}</div>
                            <div class="search-result-type">Proyecto</div>
                        </div>
                        <svg class="search-result-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                        </svg>
                    </a>
                `;
            });
            resultsEl.innerHTML = html;
        })
        .catch(() => {
            resultsEl.innerHTML = '<div class="search-results-empty">Error</div>';
        });
}

document.addEventListener('click', function (e) {
    const input = document.getElementById('globalSearch');
    const results = document.getElementById('searchResults');
    if (results && input && !input.contains(e.target) && !results.contains(e.target)) {
        results.classList.remove('active');
    }
});

function toggleMobileMenu() {
    const menu = document.getElementById('mobileMenu');
    const btn = document.getElementById('mobileMenuBtn');
    const open = menu.classList.toggle('active');

    btn.setAttribute('aria-expanded', open);
    menu.setAttribute('aria-hidden', !open);

    if (open) {
        setTimeout(() => {
            const ms = document.getElementById('mobileSearch');
            if (ms) ms.focus();
        }, 150);
    }
}

function closeMobileMenuAndOpen() {
    const menu = document.getElementById('mobileMenu');
    const btn = document.getElementById('mobileMenuBtn');
    menu.classList.remove('active');
    btn.setAttribute('aria-expanded', 'false');
    menu.setAttribute('aria-hidden', 'true');
    if (typeof abrirModal === 'function') abrirModal();
}

document.addEventListener('click', function (e) {
    const menu = document.getElementById('mobileMenu');
    const btn = document.getElementById('mobileMenuBtn');
    if (menu && menu.classList.contains('active') &&
        !menu.contains(e.target) && !btn.contains(e.target)) {
        menu.classList.remove('active');
        btn.setAttribute('aria-expanded', 'false');
        menu.setAttribute('aria-hidden', 'true');
    }
});

document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        const menu = document.getElementById('mobileMenu');
        const btn = document.getElementById('mobileMenuBtn');
        if (menu && menu.classList.contains('active')) {
            menu.classList.remove('active');
            btn.setAttribute('aria-expanded', 'false');
            menu.setAttribute('aria-hidden', 'true');
            btn.focus();
        }
    }
});

window.toggleMobileMenu = toggleMobileMenu;
window.closeMobileMenuAndOpen = closeMobileMenuAndOpen;
