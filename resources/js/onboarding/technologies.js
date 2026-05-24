/**
 * Filtrado de tecnologías en onboarding
 */
export function initTechFilter() {
    const input = document.getElementById('techSearch');
    if (!input) return;
    
    input.addEventListener('input', (e) => {
        filterTech(e.target.value);
    });
}

function filterTech(query) {
    const q = query.toLowerCase().trim();

    document.querySelectorAll('#techGrid .tech-item').forEach(item => {
        const name = item.dataset.name;
        const isFeatured = item.classList.contains('featured');
        const matches = name.includes(q);

        if (!q) {
            // Sin búsqueda: solo mostrar featured
            item.style.display = isFeatured ? '' : 'none';
        } else {
            // Con búsqueda: mostrar los que coincidan
            item.style.display = matches ? '' : 'none';
        }
    });
}
