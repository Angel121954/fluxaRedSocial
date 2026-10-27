/**
 * globals.js — Punto central de funciones globales para toda la app
 * Importar este archivo desde shared/index.js para hacer disponibles
 * todas las funciones llamadas desde HTML (onclick, etc.)
 */

import '../shared/securePassword.js';
import '../shared/emailModalSend.js';
import '../shared/topbar.js';
import '../core/projects/newProject.js';
import '../projects/modalComment.js';
import '../profile/shareProfile.js';
import '../onboarding/suggestions.js';

document.addEventListener('click', (event) => {
    const trigger = event.target.closest('[data-create-project]');

    if (!trigger) {
        return;
    }

    event.preventDefault();

    if (trigger.closest('#mobileMenu')) {
        window.closeMobileMenu?.();
    }

    if (typeof window.abrirModal === 'function') {
        window.abrirModal();
    }
});