/**
 * Inicializa el manejo de "Cargar más proyectos"
 */
export function initLoadMore(container) {
    container.addEventListener('click', async (e) => {
        const createBtn = e.target.closest('[data-create-project]');
        if (createBtn) {
            e.preventDefault();

            if (typeof window.abrirModal === 'function') {
                window.abrirModal();
            }

            return;
        }

        const btn = e.target.closest('.btn-load-more');
        if (!btn) return;

        const url = btn.dataset.url;
        if (!url) return;

        e.preventDefault();

        btn.textContent = 'Cargando...';
        btn.disabled = true;

        try {
            const response = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html',
                },
                credentials: 'same-origin',
            });

            if (!response.ok) throw new Error('Error en la petición');

            const html = await response.text();

            const temp = document.createElement('div');
            temp.innerHTML = html;

            const newCards = temp.querySelectorAll('.post-card');
            const nextWrapper = temp.querySelector('.load-more-wrapper');

            container.querySelector('.load-more-wrapper')?.remove();

            newCards.forEach(card => container.appendChild(card));

            if (nextWrapper) {
                container.appendChild(nextWrapper);
            } else {
                const emptyDiv = document.createElement('div');
                emptyDiv.className = 'feed-empty';
                emptyDiv.innerHTML = `
                    <div class="feed-empty-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                    </div>
                    <h3 class="feed-empty-title">¡Has visto todo!</h3>
                    <p class="feed-empty-text">No hay más proyectos por aquí. ¿Por qué no crear el primero y compartirlo con la comunidad?</p>
                    <button type="button" class="feed-empty-cta" data-create-project>Crear proyecto</button>
                `;
                container.appendChild(emptyDiv);
            }

        } catch (error) {
            console.error('Error cargando más proyectos:', error);
            btn.textContent = 'Cargar más proyectos';
            btn.disabled = false;
        }
    });
}
