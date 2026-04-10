(function () {
    const scrollbarWidth = window.innerWidth - document.documentElement.clientWidth;
    let openCount = 0;

    function updateBodyScroll() {
        if (openCount > 0) {
            document.body.style.overflow = 'hidden';
            document.body.style.paddingRight = scrollbarWidth + 'px';
        } else {
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
        }
    }

    function handleClassChange(el) {
        const hasShow = el.classList.contains('show');
        const wasShow = el.getAttribute('data-was-open') === 'true';
        
        if (hasShow && !wasShow) {
            openCount++;
            updateBodyScroll();
        } else if (!hasShow && wasShow) {
            openCount = Math.max(0, openCount - 1);
            updateBodyScroll();
        }
        
        el.setAttribute('data-was-open', hasShow);
    }

    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                const target = mutation.target;
                const hasIsOpen = target.classList.contains('is-open');
                const wasOpen = mutation.oldValue?.includes('is-open');

                if (hasIsOpen && !wasOpen) {
                    openCount++;
                    updateBodyScroll();
                } else if (!hasIsOpen && wasOpen) {
                    openCount = Math.max(0, openCount - 1);
                    updateBodyScroll();
                }
            }
        });
    });

    document.querySelectorAll('.we-backdrop, .pwd-modal-backdrop, .img-modal, modal-comments')
        .forEach((el) => {
            observer.observe(el, { attributes: true, attributeOldValue: true, attributeFilter: ['class'] });
        });

    document.querySelectorAll('.comments-modal').forEach((el) => {
        const obs = new MutationObserver(() => handleClassChange(el));
        obs.observe(el, { attributes: true, attributeFilter: ['class'] });
    });
})();
