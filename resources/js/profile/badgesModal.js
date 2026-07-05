(function () {
    'use strict';

    const backdrop = document.getElementById('badgesModal');
    if (!backdrop) return;

    window.openBadgesModal = function () {
        window.openModal('badgesModal');
    };
})();
