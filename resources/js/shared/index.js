/**
 * shared/index.js — Entrada para el topbar y páginas de autenticación
 * @vite('resources/js/shared/index.js')
 *
 * Úsalo en las vistas que incluyen el topbar (layout principal)
 * y en las vistas de auth (login, register, forgot-password, etc.)
 */

import '../core/globals.js';
import './modalScrollFix.js';
import './passwordVisibility.js';
import './security.js';
import './toast.js';
import '../notifications/index.js';
import '../notifications/realtime.js';
