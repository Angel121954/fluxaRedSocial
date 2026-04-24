/**
 * onboarding/index.js — Entrada para las vistas de onboarding
 * @vite('resources/js/onboarding/index.js')
 */

import './suggestions.js';
import { initTechFilter } from './technologies.js';

document.addEventListener('DOMContentLoaded', function() {
    initTechFilter();
    
    if (document.getElementById('notificationList') && typeof initNotificationsList === 'function') {
        initNotificationsList();
    }
});
