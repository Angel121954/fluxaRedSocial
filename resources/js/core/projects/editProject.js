/* ════════════════════════════════════════════════
   editProject.js  —  Modal Editar Proyecto
════════════════════════════════════════════════ */

(() => {

    /* ── Configuración ────────────────────────────── */
    const MAXIMO_ARCHIVOS = 6;
    const MAXIMO_TAMANO_MB = 10;
    const MAXIMO_BYTES = MAXIMO_TAMANO_MB * 1024 * 1024;

    /* ── Estado ───────────────────────────────────── */
    const estado = {
        tecnologiasDisponibles: [],
        tecnologiasSeleccionadas: [],
        archivos: [],
        paso: 1,
        tocado: false,
        projectId: null,
        mediaExistente: [],
    };

    /* ── Selectores DOM ───────────────────────────── */
    const el = id => document.getElementById(id);
    const $ = (id, fn) => { const e = el(id); return e && fn ? fn(e) : e; };
    const todos = selector => document.querySelectorAll(selector);

    /* ── Early exit if modal not present ──────────── */
    if (!el('edit-modal-overlay')) return;

    /* ════════════════════════════════════════════════
       TECNOLOGÍAS
    ════════════════════════════════════════════════ */
    const cargarTecnologias = async () => {
        try {
            const respuesta = await fetch('/technologies', {
                headers: { 'Accept': 'application/json' },
                credentials: 'same-origin',
            });
            const datos = await respuesta.json();
            estado.tecnologiasDisponibles = datos.map(t => t.name);
            renderizarEditTecnologiasDisponibles('');
        } catch (error) {
            console.error('Error al cargar tecnologias:', error);
        }
    };

    const renderizarEditTecnologiasDisponibles = (busqueda) => {
        const filtradas = estado.tecnologiasDisponibles.filter(
            t => !estado.tecnologiasSeleccionadas.includes(t) &&
                t.toLowerCase().includes(busqueda.toLowerCase())
        );

        el('edit-tech-tags').innerHTML = filtradas.length
            ? filtradas.map(t =>
                `<button class="chip" onclick="seleccionarEditTecnologia('${t}')" aria-label="Agregar ${t}">${t}</button>`
            ).join('')
            : '<span class="tech-empty">Sin resultados</span>';
    };

    const renderizarEditTecnologiasSeleccionadas = () => {
        el('edit-selected-tags').innerHTML = estado.tecnologiasSeleccionadas
            .map(t =>
                `<button class="chip on" onclick="quitarEditTecnologia('${t}')" aria-label="Quitar ${t}">
                ${t} <span class="chip-x" aria-hidden="true">✕</span>
             </button>`
            ).join('');
    };

    const seleccionarEditTecnologia = (nombre) => {
        estado.tecnologiasSeleccionadas.push(nombre);
        renderizarEditTecnologiasSeleccionadas();
        filtrarEditTecnologias();
    };

    const quitarEditTecnologia = (nombre) => {
        estado.tecnologiasSeleccionadas = estado.tecnologiasSeleccionadas.filter(t => t !== nombre);
        renderizarEditTecnologiasSeleccionadas();
        filtrarEditTecnologias();
    };

    const filtrarEditTecnologias = () =>
        renderizarEditTecnologiasDisponibles(el('edit-tech-search').value);

    /* ════════════════════════════════════════════════
       MODAL — ABRIR / CERRAR / RESETEAR
    ════════════════════════════════════════════════ */
    const abrirEditModal = async (projectId) => {
        estado.projectId = projectId;
        lockBodyScroll();
        el('edit-modal-overlay').classList.add('open');

        await cargarTecnologias();
        await cargarDatosProyecto(projectId);

        setTimeout(() => el('edit-input-title').focus(), 50);
    };

    const cerrarEditModal = () => {
        el('edit-modal-overlay').classList.remove('open');

        setTimeout(() => {
            unlockBodyScroll();
            resetearEditModal();
        }, 250);
    };

    const resetearEditModal = () => {
        Object.assign(estado, {
            tecnologiasSeleccionadas: [],
            archivos: [],
            paso: 1,
            tocado: false,
            projectId: null,
            mediaExistente: [],
        });

        ['edit-input-title', 'edit-input-content', 'edit-tech-search'].forEach(id => { const e = el(id); if (e) e.value = ''; });

        if (el('edit-char-n')) el('edit-char-n').textContent = '0';
        if (el('edit-selected-tags')) el('edit-selected-tags').innerHTML = '';
        if (el('edit-media-grid')) el('edit-media-grid').innerHTML = '';
        if (el('edit-media-grid-existing')) el('edit-media-grid-existing').innerHTML = '';
        if (el('edit-existing-media-section')) el('edit-existing-media-section').style.display = 'none';
        if (el('edit-success-state')) el('edit-success-state').style.display = 'none';
        if (el('edit-modal-footer')) el('edit-modal-footer').style.display = '';
        if (el('edit-modal-title')) el('edit-modal-title').textContent = 'Editar proyecto';
        if (el('edit-modal-subtitle')) el('edit-modal-subtitle').textContent = 'Paso 1 de 2 · Información básica';

        todos('#edit-modal-overlay .step-panel').forEach(p => p.classList.remove('active'));
        if (el('edit-step-1')) el('edit-step-1').classList.add('active');

        if (el('edit-btn-back')) el('edit-btn-back').style.display = 'none';
        if (el('edit-btn-next')) el('edit-btn-next').style.display = '';
        if (el('edit-btn-next')) el('edit-btn-next').disabled = true;
        if (el('edit-btn-submit')) {
            el('edit-btn-submit').style.display = 'none';
            el('edit-btn-submit').disabled = false;
            el('edit-btn-submit').textContent = 'Guardar cambios';
        }
        if (el('edit-btn-cancel')) el('edit-btn-cancel').style.display = '';
        if (el('edit-req-note')) el('edit-req-note').style.display = '';

        resetearEditTipoArchivo();
        limpiarEditError('edit-title-err', 'edit-input-title');
        limpiarEditError('edit-desc-err', 'edit-input-content');
        ocultarEditErrorServidor();
        ocultarEditErrorArchivo();
        renderizarEditTecnologiasDisponibles('');
    };

    /* ════════════════════════════════════════════════
       CARGAR DATOS DEL PROYECTO
    ════════════════════════════════════════════════ */
    const cargarDatosProyecto = async (projectId) => {
        try {
            const respuesta = await fetch(`/projects/${projectId}/edit`, {
                headers: { 'Accept': 'application/json' },
                credentials: 'same-origin',
            });

            if (!respuesta.ok) throw new Error('Error al cargar datos del proyecto');

            const proyecto = await respuesta.json();

            el('edit-input-title').value = proyecto.title;
            el('edit-input-content').value = proyecto.content;
            el('edit-char-n').textContent = proyecto.content.length;
            el('edit-input-privacy').value = proyecto.privacy;
            el('edit-project-id').value = proyecto.id;

            estado.tecnologiasSeleccionadas = proyecto.technologies.map(t => t.name);
            renderizarEditTecnologiasSeleccionadas();
            renderizarEditTecnologiasDisponibles('');

            if (proyecto.media && proyecto.media.length > 0) {
                estado.mediaExistente = proyecto.media;
                renderizarEditMediaExistente(proyecto.media);
            }

            el('edit-btn-next').disabled = false;

        } catch (error) {
            console.error('Error al cargar proyecto:', error);
            mostrarEditErrorServidor(['No se pudo cargar la información del proyecto.']);
        }
    };

    /* ════════════════════════════════════════════════
       MEDIA EXISTENTE
    ════════════════════════════════════════════════ */
    const renderizarEditMediaExistente = (mediaItems) => {
        const grid = el('edit-media-grid-existing');
        const section = el('edit-existing-media-section');

        section.style.display = '';

        grid.innerHTML = mediaItems.map((item, i) => {
            const isVideo = item.type === 'video';
            const src = isVideo ? item.media_url : item.media_url;

            return `
            <div class="thumb" data-media-id="${item.id}" role="listitem">
                ${isVideo
                    ? `<video src="${src}" muted loop></video>`
                    : `<img src="${src}" alt="Media ${i + 1}" />`}
                <button class="rm" onclick="eliminarEditMediaExistente(${item.id}, this)"
                    aria-label="Eliminar media ${i + 1}">✕</button>
            </div>`;
        }).join('');
    };

    const eliminarEditMediaExistente = (mediaId, btn) => {
        const thumb = btn.closest('.thumb');
        thumb.style.transition = 'opacity 0.3s, transform 0.3s';
        thumb.style.opacity = '0';
        thumb.style.transform = 'scale(0.8)';

        setTimeout(() => {
            thumb.remove();
            estado.mediaExistente = estado.mediaExistente.filter(m => m.id !== mediaId);

            const grid = el('edit-media-grid-existing');
            if (!grid.children.length) {
                el('edit-existing-media-section').style.display = 'none';
            }

            // Actually delete via API
            fetch(`/projects/${estado.projectId}/media/${mediaId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                credentials: 'same-origin',
            }).catch(() => {});
        }, 300);
    };

    /* ════════════════════════════════════════════════
       STEPPER
    ════════════════════════════════════════════════ */
    const irAlSiguienteEdit = () => {
        estado.tocado = true;
        if (!validarEditPaso1()) return;

        estado.paso = 2;
        el('edit-step-1').classList.remove('active');
        el('edit-step-2').classList.add('active');

        el('edit-modal-subtitle').textContent = 'Paso 2 de 2 · Archivos multimedia';
        el('edit-btn-back').style.display = '';
        el('edit-btn-next').style.display = 'none';
        el('edit-btn-submit').style.display = '';
        el('edit-req-note').style.display = 'none';
        el('edit-modal-body').scrollTop = 0;
    };

    const irAlAnteriorEdit = () => {
        estado.paso = 1;
        el('edit-step-2').classList.remove('active');
        el('edit-step-1').classList.add('active');

        el('edit-modal-subtitle').textContent = 'Paso 1 de 2 · Información básica';
        el('edit-btn-back').style.display = 'none';
        el('edit-btn-next').style.display = '';
        el('edit-btn-submit').style.display = 'none';
        el('edit-req-note').style.display = '';
        el('edit-btn-next').disabled = !editFormularioValido();
        el('edit-modal-body').scrollTop = 0;
    };

    /* ════════════════════════════════════════════════
       VALIDACIÓN
    ════════════════════════════════════════════════ */
    const editFormularioValido = () =>
        el('edit-input-title').value.trim().length >= 3 &&
        el('edit-input-content').value.trim().length >= 10;

    const validarEditPaso1 = () => {
        let valido = true;

        const titulo = el('edit-input-title').value.trim();
        titulo.length < 3
            ? (mostrarEditError('edit-title-err', 'edit-input-title'), (valido = false))
            : limpiarEditError('edit-title-err', 'edit-input-title');

        const descripcion = el('edit-input-content').value.trim();
        descripcion.length < 10
            ? (mostrarEditError('edit-desc-err', 'edit-input-content'), (valido = false))
            : limpiarEditError('edit-desc-err', 'edit-input-content');

        return valido;
    };

    const mostrarEditError = (idError, idInput) => {
        el(idError).classList.add('show');
        el(idInput).classList.add('invalid');
    };

    const limpiarEditError = (idError, idInput) => {
        el(idError).classList.remove('show');
        el(idInput).classList.remove('invalid');
    };

    /* ── Eventos de inputs ────────────────────────── */
    const alEscribirEditTitulo = () => {
        if (el('edit-input-title').value.trim().length >= 3) limpiarEditError('edit-title-err', 'edit-input-title');
        el('edit-btn-next').disabled = !editFormularioValido();
    };

    const alEscribirEditDescripcion = () => {
        const input = el('edit-input-content');
        if (input.value.length > 500) input.value = input.value.slice(0, 500);

        const caracteres = Math.min(input.value.length, 500);
        el('edit-char-n').textContent = caracteres;
        el('edit-char-count').classList.toggle('warn', caracteres >= 450);

        if (caracteres >= 10) limpiarEditError('edit-desc-err', 'edit-input-content');
        el('edit-btn-next').disabled = !editFormularioValido();
    };

    /* ════════════════════════════════════════════════
       TIPO DE ARCHIVO
    ════════════════════════════════════════════════ */
    const CONFIGURACION_TIPO = {
        image: { accept: 'image/*', hint: `PNG, JPG, WEBP · máx ${MAXIMO_TAMANO_MB} MB · hasta ${MAXIMO_ARCHIVOS} archivos` },
        video: { accept: 'video/*', hint: `MP4, MOV, WEBM · máx ${MAXIMO_TAMANO_MB} MB · hasta ${MAXIMO_ARCHIVOS} archivos` },
        gif: { accept: 'image/gif', hint: `GIF animado · máx ${MAXIMO_TAMANO_MB} MB · hasta ${MAXIMO_ARCHIVOS} archivos` },
    };

    const cambiarEditTipoArchivo = (tipo, boton) => {
        todos('#edit-media-types .mtype-btn').forEach(b => {
            b.classList.remove('on');
            b.setAttribute('aria-pressed', 'false');
        });
        boton.classList.add('on');
        boton.setAttribute('aria-pressed', 'true');

        el('edit-input-media-type').value = tipo;
        el('edit-file-input').accept = CONFIGURACION_TIPO[tipo].accept;
        el('edit-file-hint').textContent = CONFIGURACION_TIPO[tipo].hint;
    };

    const resetearEditTipoArchivo = () => {
        todos('#edit-media-types .mtype-btn').forEach(b => {
            b.classList.remove('on');
            b.setAttribute('aria-pressed', 'false');
        });
        const botonImagen = document.querySelector('#edit-media-types .mtype-btn[data-type="image"]');
        if (botonImagen) {
            botonImagen.classList.add('on');
            botonImagen.setAttribute('aria-pressed', 'true');
        }

        el('edit-input-media-type').value = 'image';
        el('edit-file-input').accept = CONFIGURACION_TIPO.image.accept;
        el('edit-file-hint').textContent = CONFIGURACION_TIPO.image.hint;
    };

    /* ════════════════════════════════════════════════
       MANEJO DE ARCHIVOS
    ════════════════════════════════════════════════ */
    const mostrarEditErrorArchivo = (mensaje) => {
        el('edit-media-err-text').textContent = mensaje;
        el('edit-media-err').removeAttribute('hidden');
    };

    const ocultarEditErrorArchivo = () =>
        el('edit-media-err').setAttribute('hidden', '');

    const alArrastrarSobreEdit = (e) => { e.preventDefault(); el('edit-drop-zone').classList.add('over'); };
    const alSalirArrastreEdit = () => el('edit-drop-zone').classList.remove('over');
    const alSoltarEdit = (e) => {
        e.preventDefault();
        el('edit-drop-zone').classList.remove('over');
        procesarEditArchivos(e.dataTransfer.files);
    };

    const procesarEditArchivos = (lista) => {
        ocultarEditErrorArchivo();
        const rechazados = [];

        Array.from(lista).forEach(archivo => {
            const totalActual = estado.archivos.length + estado.mediaExistente.length;
            if (totalActual >= MAXIMO_ARCHIVOS) {
                rechazados.push(`${archivo.name}: límite de ${MAXIMO_ARCHIVOS} archivos alcanzado`);
                return;
            }
            if (archivo.size > MAXIMO_BYTES) {
                rechazados.push(`${archivo.name}: supera el límite de ${MAXIMO_TAMANO_MB}MB`);
                return;
            }
            estado.archivos.push({
                file: archivo,
                url: URL.createObjectURL(archivo),
                type: archivo.type,
            });
        });

        if (rechazados.length) mostrarEditErrorArchivo(rechazados.join(' · '));
        renderizarEditGrilla();
    };

    const renderizarEditGrilla = () => {
        el('edit-media-grid').innerHTML = estado.archivos.map((item, i) => `
        <div class="thumb" role="listitem">
            ${item.type.startsWith('video')
                ? `<video src="${item.url}" muted loop></video>`
                : `<img src="${item.url}" alt="Archivo ${i + 1}" />`}
            <button class="rm" onclick="eliminarEditArchivo(${i})" aria-label="Eliminar archivo ${i + 1}">✕</button>
        </div>`
        ).join('');
    };

    const eliminarEditArchivo = (indice) => {
        URL.revokeObjectURL(estado.archivos[indice].url);
        estado.archivos.splice(indice, 1);
        ocultarEditErrorArchivo();
        renderizarEditGrilla();
    };

    /* ════════════════════════════════════════════════
       BANNER DE ERROR DEL SERVIDOR
    ════════════════════════════════════════════════ */
    const mostrarEditErrorServidor = (mensajes) => {
        el('edit-server-error-list').innerHTML = mensajes.map(m => `<li>${m}</li>`).join('');
        el('edit-server-error-banner').removeAttribute('hidden');
        el('edit-server-error-banner').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    };

    const ocultarEditErrorServidor = () => {
        el('edit-server-error-banner').setAttribute('hidden', '');
        el('edit-server-error-list').innerHTML = '';
    };

    /* ════════════════════════════════════════════════
       ENVÍO DEL FORMULARIO
    ════════════════════════════════════════════════ */
    const guardarEditProyecto = () => {
        ocultarEditErrorServidor();

        const boton = el('edit-btn-submit');
        boton.disabled = true;
        boton.textContent = 'Guardando...';

        const datos = new FormData();
        datos.append('_method', 'PUT');
        datos.append('title', el('edit-input-title').value.trim());
        datos.append('content', el('edit-input-content').value.trim());
        datos.append('privacy', el('edit-input-privacy').value);

        estado.tecnologiasSeleccionadas.forEach(tech => datos.append('techs[]', tech));
        estado.archivos.forEach((item, i) => datos.append(`media[${i}]`, item.file));

        fetch(`/projects/${estado.projectId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: datos,
        })
            .then(respuesta => respuesta.json().then(data => ({ status: respuesta.status, data })))
            .then(({ status, data }) => {
                if (status === 422) {
                    mostrarEditErrorServidor(Object.values(data.errors).flat());
                    boton.disabled = false;
                    boton.textContent = 'Guardar cambios';
                    return;
                }

                if (!data.success) throw new Error(data.message);

                actualizarCardEnDOM(data.project);
                mostrarEditExito();
            })
            .catch(error => {
                mostrarEditErrorServidor([error.message || 'Ocurrió un error inesperado. Inténtalo de nuevo.']);
                boton.disabled = false;
                boton.textContent = 'Guardar cambios';
            });
    };

    const mostrarEditExito = () => {
        todos('#edit-modal-overlay .step-panel').forEach(p => p.classList.remove('active'));
        const mostrar = el('edit-success-state'); if (mostrar) mostrar.style.display = 'flex';
        const ocultarFooter = el('edit-modal-footer'); if (ocultarFooter) ocultarFooter.style.display = 'none';
        const titulo = el('edit-modal-title'); if (titulo) titulo.textContent = 'Proyecto actualizado';
        const subtitulo = el('edit-modal-subtitle'); if (subtitulo) subtitulo.textContent = '';
        setTimeout(() => cerrarEditModal(), 2200);
    };

    /* ════════════════════════════════════════════════
       ACTUALIZAR CARD EN DOM
    ════════════════════════════════════════════════ */
    const actualizarCardEnDOM = (proyecto) => {
        const card = document.querySelector(`.post-card[data-project-id="${proyecto.id}"]`);
        if (!card) return;

        const titleEl = card.querySelector('.project-title');
        const contentEl = card.querySelector('.post-content');
        const tagsContainer = card.querySelector('.post-tags');

        if (titleEl) titleEl.textContent = proyecto.title;
        if (contentEl) contentEl.textContent = proyecto.content;

        if (tagsContainer && proyecto.technologies) {
            if (proyecto.technologies.length > 0) {
                tagsContainer.innerHTML = proyecto.technologies
                    .map(t => `<span class="post-tag">${t.name}</span>`)
                    .join('');
                tagsContainer.style.display = '';
            } else {
                tagsContainer.style.display = 'none';
            }
        }
    };

    /* ════════════════════════════════════════════════
       EVENTOS GLOBALES
    ════════════════════════════════════════════════ */
    document.addEventListener('keydown', ({ key }) => { if (key === 'Escape') cerrarEditModal(); });

    el('edit-modal-overlay').addEventListener('click', function (e) {
        if (e.target === this) cerrarEditModal();
    });

    /* ════════════════════════════════════════════════
       EXPORTS
    ════════════════════════════════════════════════ */
    window.abrirEditModal = abrirEditModal;
    window.cerrarEditModal = cerrarEditModal;
    window.irAlSiguienteEdit = irAlSiguienteEdit;
    window.irAlAnteriorEdit = irAlAnteriorEdit;
    window.guardarEditProyecto = guardarEditProyecto;
    window.eliminarEditArchivo = eliminarEditArchivo;
    window.seleccionarEditTecnologia = seleccionarEditTecnologia;
    window.quitarEditTecnologia = quitarEditTecnologia;
    window.filtrarEditTecnologias = filtrarEditTecnologias;
    window.cambiarEditTipoArchivo = cambiarEditTipoArchivo;
    window.alEscribirEditTitulo = alEscribirEditTitulo;
    window.alEscribirEditDescripcion = alEscribirEditDescripcion;
    window.alArrastrarSobreEdit = alArrastrarSobreEdit;
    window.alSalirArrastreEdit = alSalirArrastreEdit;
    window.alSoltarEdit = alSoltarEdit;
    window.procesarEditArchivos = procesarEditArchivos;
    window.eliminarEditMediaExistente = eliminarEditMediaExistente;

})();
