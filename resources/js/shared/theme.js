const STORAGE_KEY = 'fluxa-theme';

function isDark() {
    return document.documentElement.classList.contains('dark');
}

function syncToggleButtons() {
    const dark = isDark();
    document.querySelectorAll('[data-theme-toggle]').forEach((btn) => {
        btn.setAttribute('aria-pressed', String(dark));
    });
}

function setTheme(dark) {
    document.documentElement.classList.toggle('dark', dark);
    localStorage.setItem(STORAGE_KEY, dark ? 'dark' : 'light');
    syncToggleButtons();
}

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-theme-toggle]').forEach((btn) => {
        btn.addEventListener('click', function () {
            setTheme(!isDark());

            const menu = document.getElementById('mobileMenu');
            const overlay = document.getElementById('mobileOverlay');
            if (menu && menu.classList.contains('active') && btn.closest('#mobileMenu')) {
                menu.classList.remove('active');
                overlay?.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    });

    syncToggleButtons();
});

window.fluxaTheme = {
    toggle() {
        setTheme(!isDark());
    },
};
