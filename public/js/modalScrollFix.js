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

    document.querySelectorAll('.we-backdrop, .pwd-modal-backdrop, .img-modal, .comments-modal')
        .forEach((el) => {
            observer.observe(el, { attributes: true, attributeOldValue: true });
        });
})();
