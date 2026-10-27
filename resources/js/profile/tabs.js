(function () {
    const tabs = Array.from(document.querySelectorAll('.tab'));
    const panels = Array.from(document.querySelectorAll('[data-panel]'));

    if (!tabs.length || !panels.length) {
        return;
    }

    function activateTab(tab, { focus = false } = {}) {
        tabs.forEach(function (candidate) {
            const isActive = candidate === tab;

            candidate.classList.toggle('active', isActive);
            candidate.setAttribute('aria-selected', isActive ? 'true' : 'false');
            candidate.tabIndex = isActive ? 0 : -1;
        });

        panels.forEach(function (panel) {
            panel.hidden = panel.dataset.panel !== tab.dataset.tab;
        });

        if (focus) {
            tab.focus();
        }
    }

    tabs.forEach(function (tab, index) {
        tab.addEventListener('click', function () {
            activateTab(tab);
        });

        tab.addEventListener('keydown', function (event) {
            let nextIndex = null;

            switch (event.key) {
                case 'ArrowRight':
                    nextIndex = (index + 1) % tabs.length;
                    break;
                case 'ArrowLeft':
                    nextIndex = (index - 1 + tabs.length) % tabs.length;
                    break;
                case 'Home':
                    nextIndex = 0;
                    break;
                case 'End':
                    nextIndex = tabs.length - 1;
                    break;
                default:
                    return;
            }

            event.preventDefault();
            activateTab(tabs[nextIndex], { focus: true });
        });
    });

    const params = new URLSearchParams(window.location.search);
    const tabParam = params.get('tab');
    const requestedTab = tabParam ? tabs.find(function (tab) { return tab.dataset.tab === tabParam; }) : null;
    const initialTab = requestedTab
        || tabs.find(function (tab) { return tab.classList.contains('active'); })
        || tabs[0];

    activateTab(initialTab);

    if (tabParam) {
        history.replaceState(null, '', window.location.pathname);
    }
})();
