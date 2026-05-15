(function () {
    let openCount = 0;

    function updateBodyScroll() {
        const sbw = window.innerWidth - document.documentElement.clientWidth;
        if (openCount > 0) {
            document.body.style.overflow = 'hidden';
            document.body.style.marginRight = sbw > 0 ? sbw + 'px' : '';
        } else {
            document.body.style.overflow = '';
            document.body.style.marginRight = '';
        }
    }

    function checkAndUpdate() {
        const modals = document.querySelectorAll(
            '.img-modal.show, .we-backdrop.is-open, .pwd-modal-backdrop.is-open, .comments-modal.show, .modal-backdrop.show, .modal-backdrop.is-open'
        );
        
        let newCount = 0;
        modals.forEach(() => newCount++);

        if (newCount !== openCount) {
            openCount = newCount;
            updateBodyScroll();
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        setInterval(checkAndUpdate, 50);
    });

    window.addEventListener('resize', () => {
        if (openCount > 0) updateBodyScroll();
    });
})();