/**
 * resources/js/diary/index.js — Entrada para la sección Diario
 * @vite('resources/js/diary/index.js')
 *
 * Módulos:
 *   countdown.js     — cuenta regresiva al cierre del diario
 *   reply.js         — publicar respuesta + cargar más
 *   interactions.js  — like, bookmark, menú contextual, eliminar
 *   liveTime.js      — tiempos relativos (hace X minutos)
 */

import { initDiaryCountdown }    from './countdown.js';
import { initDiaryReply, initLoadMore } from './reply.js';
import { initDiaryInteractions } from './interactions.js';
import '../shared/liveTime.js';

document.addEventListener('DOMContentLoaded', () => {
    initDiaryCountdown();
    initDiaryReply();
    initLoadMore();
    initDiaryInteractions();
});
