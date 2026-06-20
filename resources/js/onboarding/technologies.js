const PER_PAGE = 4;

export function initTechFilter() {
    const input = document.getElementById('techSearch');
    if (!input) return;

    initCategoryLimits();

    input.addEventListener('input', (e) => {
        filterTech(e.target.value.trim());
    });
}

function initCategoryLimits() {
    document.querySelectorAll('.tech-grid').forEach(grid => {
        const items = grid.querySelectorAll('.tech-item');
        if (items.length <= PER_PAGE) return;

        items.forEach((item, i) => {
            if (i >= PER_PAGE) {
                item.classList.add('limited');
                item.dataset.limited = 'true';
            }
        });

        const remaining = items.length - PER_PAGE;
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'tech-show-more';
        btn.textContent = `Ver más (${remaining})`;
        btn.addEventListener('click', () => {
            grid.querySelectorAll('.tech-item.limited').forEach(el => {
                el.classList.remove('limited');
                delete el.dataset.limited;
            });
            btn.remove();
        });
        grid.after(btn);
    });
}

function filterTech(query) {
    const q = query.toLowerCase().trim();

    document.querySelectorAll('.tech-item').forEach(item => {
        const matches = item.dataset.name.includes(q);

        if (!q) {
            item.classList.remove('search-hidden');
            if (item.dataset.limited) {
                item.classList.add('limited');
            }
        } else {
            item.classList.remove('limited');
            item.classList.toggle('search-hidden', !matches);
        }
    });

    document.querySelectorAll('.tech-show-more').forEach(btn => {
        btn.style.display = q ? 'none' : '';
    });
}
