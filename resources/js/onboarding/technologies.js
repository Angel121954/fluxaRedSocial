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
    document.querySelectorAll('#techGrid .tech-item').forEach(item => {
        item.style.display = item.dataset.name.includes(query.toLowerCase()) ? '' : 'none';
    });
}
