document.querySelectorAll('.tab').forEach((t) => {
    t.addEventListener('click', () => {
        // Tabs activo
        document.querySelectorAll('.tab')
            .forEach((x) => x.classList.remove('active'));
        t.classList.add('active');

        // Mostrar panel correspondiente
        const target = t.dataset.tab;
        document.querySelectorAll('[data-panel]').forEach((panel) => {
            panel.style.display = panel.dataset.panel === target ? '' : 'none';
        });
    });
});