export function initProfileStats() {
    const stats = document.querySelectorAll('[data-stat-action]');

    if (!stats.length) {
        return;
    }

    stats.forEach(function (stat) {
        stat.addEventListener('click', function () {
            const userId = stat.dataset.userId;

            if (!userId) {
                return;
            }

            switch (stat.dataset.statAction) {
                case 'projects':
                    window.openProjectsModal?.(userId);
                    break;
                case 'following':
                    window.openFollowersModal?.(userId, 'following');
                    break;
                case 'followers':
                    window.openFollowersModal?.(userId, 'followers');
                    break;
            }
        });
    });
}
