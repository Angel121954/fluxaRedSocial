/* ═══════════════════════════════════════════════════════════
   resources/js/jobs/newJobOffer.js
   Modal: Publicar oferta de empleo
   Sigue el patrón de newProject.js (abrirModal/cerrarModal)
════════════════════════════════════════════════════════════ */

function el(id) { return document.getElementById(id); }

function abrirJobOffer() {
    const scrollbarWidth = window.innerWidth - document.documentElement.clientWidth;
    document.body.style.overflow = 'hidden';
    document.body.style.paddingRight = scrollbarWidth + 'px';

    el('jobOfferOverlay').classList.add('open');
    setTimeout(function () {
        var titleInput = el('jo-title');
        if (titleInput) titleInput.focus();
    }, 50);
}

function cerrarJobOffer() {
    el('jobOfferOverlay').classList.remove('open');

    setTimeout(function () {
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
    }, 250);
}

document.addEventListener('DOMContentLoaded', function () {
    var overlay = el('jobOfferOverlay');
    if (!overlay) return;

    overlay.addEventListener('click', function (e) {
        if (e.target === this) cerrarJobOffer();
    });
});

document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        var overlay = el('jobOfferOverlay');
        if (overlay && overlay.classList.contains('open')) {
            cerrarJobOffer();
        }
    }
});

window.abrirJobOffer = abrirJobOffer;
window.cerrarJobOffer = cerrarJobOffer;
