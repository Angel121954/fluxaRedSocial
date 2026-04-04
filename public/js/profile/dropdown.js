document.querySelectorAll('.drop-wrap').forEach(wrap => {
    const btnMore = wrap.querySelector('.btn-icon');
    const dropMenu = wrap.querySelector('.drop-menu');

    if (btnMore && dropMenu) {
        btnMore.addEventListener('click', (e) => {
            e.stopPropagation();
            const isOpen = dropMenu.classList.toggle('open');
            btnMore.classList.toggle('is-open', isOpen);
        });
    }
});

document.addEventListener('click', (e) => {
    document.querySelectorAll('.drop-wrap').forEach(wrap => {
        const btnMore = wrap.querySelector('.btn-icon');
        const dropMenu = wrap.querySelector('.drop-menu');
        if (dropMenu && btnMore &&
            !dropMenu.contains(e.target) &&
            e.target !== btnMore) {
            dropMenu.classList.remove('open');
            btnMore.classList.remove('is-open');
        }
    });
});

document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        document.querySelectorAll('.drop-menu.open').forEach(menu => {
            menu.classList.remove('open');
            menu.closest('.drop-wrap')?.querySelector('.btn-icon')?.classList.remove('is-open');
        });
    }
});
