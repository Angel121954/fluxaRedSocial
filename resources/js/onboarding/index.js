/**
 * onboarding/index.js — Entrada para las vistas de onboarding
 * @vite('resources/js/onboarding/index.js')
 */

// suggestions.js expone toggleFollow y skipOnboarding en window (usados desde Blade)
import './suggestions.js';

// technologies.js exporta initTechFilter; lo inicializamos aquí
import { initTechFilter } from './technologies.js';

document.addEventListener('DOMContentLoaded', () => {
    initTechFilter();
});
