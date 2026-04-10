/**
 * comments/index.js — Re-exporta la API pública del módulo de comentarios
 *
 * No es un entry point de Vite; es un barrel usado por explore e profile:
 *   import { openCommentsModal } from '../comments/index.js';
 */

export { openCommentsModal } from './modalComment.js';
