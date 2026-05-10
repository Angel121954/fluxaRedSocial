(function () {
    const tabs = document.querySelectorAll('.tab');

    function activateTab(tab) {
        tabs.forEach(function (t) { t.classList.remove('active'); });
        tab.classList.add('active');

        var target = tab.dataset.tab;
        document.querySelectorAll('[data-panel]').forEach(function (panel) {
            panel.style.display = panel.dataset.panel === target ? '' : 'none';
        });
    }

    tabs.forEach(function (t) {
        t.addEventListener('click', function () { activateTab(t); });
    });

    var params = new URLSearchParams(window.location.search);
    var tabParam = params.get('tab');
    if (tabParam) {
        var targetTab = document.querySelector('.tab[data-tab="' + tabParam + '"]');
        if (targetTab) activateTab(targetTab);
        history.replaceState(null, '', window.location.pathname);
    }
})();