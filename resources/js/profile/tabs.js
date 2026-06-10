(function () {
    const tabs = document.querySelectorAll('.tab');

    function activateTab(tab) {
        tabs.forEach(function (t) { t.classList.remove('active'); });
        tab.classList.add('active');

        const target = tab.dataset.tab;
        document.querySelectorAll('[data-panel]').forEach(function (panel) {
            panel.style.display = panel.dataset.panel === target ? '' : 'none';
        });
    }

    tabs.forEach(function (t) {
        t.addEventListener('click', function () { activateTab(t); });
    });

    const params = new URLSearchParams(window.location.search);
    const tabParam = params.get('tab');
    if (tabParam) {
        const targetTab = document.querySelector('.tab[data-tab="' + tabParam + '"]');
        if (targetTab) activateTab(targetTab);
        history.replaceState(null, '', window.location.pathname);
    }
})();