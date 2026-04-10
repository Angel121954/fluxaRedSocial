/**
 * profile/index.js — Entrada para todas las vistas de perfil y configuración
 * @vite('resources/js/profile/index.js')
 *
 * Cada módulo se auto-guarda comprobando si sus elementos DOM existen,
 * por lo que es seguro cargarlo en cualquier vista de perfil/settings.
 */

import '../shared/modalScrollFix.js';
import '../shared/toast.js';

// ── Vista principal del perfil ─────────────────────────────────────────────
import './profileOptions.js';
import './dropdown.js';
import './avatar.js';
import './tabs.js';
import './filters.js';
import './shareProfile.js';
import './commentHandler.js';       // gestiona likes + apertura del modal de comentarios
import '../comments/modalComment.js'; // inicializa listeners de cierre del modal

// ── Páginas de configuración (se auto-desactivan si el DOM no aplica) ──────
import './account.js';        // toggle 2FA, sidebar nav
import './configuration.js';  // contador de caracteres en bio
import './destroyAccount.js'; // desactivar / eliminar cuenta
import './education.js';      // CRUD educación
import './workExperience.js'; // CRUD experiencia laboral
