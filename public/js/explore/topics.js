/**
 * Manejo de "Ver más" topics en el sidebar
 */
document.addEventListener('DOMContentLoaded', function() {
    const btn = document.getElementById('showMoreTopics');
    const hiddenTopics = document.querySelectorAll('.more-topic');
    if (btn && hiddenTopics.length > 0) {
        btn.addEventListener('click', function() {
            hiddenTopics.forEach(t => t.style.display = 'inline');
            btn.style.display = 'none';
        });
    }
});
