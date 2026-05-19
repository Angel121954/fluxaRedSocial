(function () {
    const userId = parseInt(document.body?.dataset?.userId || '0');
    if (!userId || !window.Echo) return;

    window.Echo.private('user.banned.' + userId)
        .listen('.user.banned', function () {
            window.location.href = '/login?banned=1';
        });
})();
