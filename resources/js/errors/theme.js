/* Aplica el tema guardado en las páginas de error, que no usan el layout. */
(function () {
    if (localStorage.getItem('fluxa-theme') === 'dark') {
        document.documentElement.classList.add('dark');
    }
})();
