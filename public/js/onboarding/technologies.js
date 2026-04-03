/**
 * Filtrado de tecnologías en onboarding
 */
function filterTech(query) {
    document.querySelectorAll('#techGrid .tech-item').forEach(item => {
        item.style.display = item.dataset.name.includes(query.toLowerCase()) ? '' : 'none';
    });
}
