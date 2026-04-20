/* ════════════════════════════════════════════════
   newProject.js  —  Modal Nuevo Proyecto
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
    };

    /* ── Selectores DOM ───────────────────────────── */
    const el = id => document.getElementById(id);
    const todos = selector => document.querySelectorAll(selector);

    /* ════════════════════════════════════════════════
       INICIALIZACIÓN
    ════════════════════════════════════════════════ */
    const cargarTecnologias = async () => {
        try {
            const respuesta = await fetch('/technologies', {
                headers: { 'Accept': 'application/json' },
            });
            const datos = await respuesta.json();
            estado.tecnologiasDisponibles = datos.map(t => t.name);
            renderizarTecnologiasDisponibles('');
        } catch (error) {
            console.error('Error al cargar tecnologias:', error);
        }
    };

    /* ════════════════════════════════════════════════
       MODAL — ABRIR / CERRAR / RESETEAR
    ════════════════════════════════════════════════ */
    const abrirModal = () => {
        const scrollbarWidth = window.innerWidth - document.documentElement.clientWidth;
        document.body.style.overflow = 'hidden';
        document.body.style.paddingRight = `${scrollbarWidth}px`;

        el('modal-overlay').classList.add('open');
        setTimeout(() => el('input-title').focus(), 50);
        cargarTecnologias();
    };

    const cerrarModal = () => {
        el('modal-overlay').classList.remove('open');

        setTimeout(() => {
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
            resetearModal();
        }, 250); // mismo tiempo que la transición del overlay
    };

    const resetearModal = () => {
        Object.assign(estado, {
            tecnologiasSeleccionadas: [],
            archivos: [],
            paso: 1,
            tocado: false,
        });

        ['input-title', 'input-content', 'tech-search'].forEach(id => (el(id).value = ''));

        el('char-n').textContent = '0';
        el('selected-tags').innerHTML = '';
        el('media-grid').innerHTML = '';
        el('success-state').style.display = 'none';
        el('modal-footer').style.display = '';
        el('stepper').style.display = '';
        el('modal-title').textContent = 'Nuevo proyecto';
        el('modal-subtitle').textContent = 'Paso 1 de 2 · Información básica';

        todos('.step-panel').forEach(p => p.classList.remove('active'));
        el('step-1').classList.add('active');
        actualizarStepper(1);

        el('btn-back').style.display = 'none';
        el('btn-next').style.display = '';
        el('btn-next').disabled = true;
        el('btn-submit').style.display = 'none';
        el('btn-cancel').style.display = '';
        el('req-note').style.display = '';

        resetearTipoArchivo();
        limpiarError('title-err', 'input-title');
        limpiarError('desc-err', 'input-content');
        ocultarErrorServidor();
        ocultarErrorArchivo();
        renderizarTecnologiasDisponibles('');
    };

    /* ════════════════════════════════════════════════
       STEPPER
    ════════════════════════════════════════════════ */
    const actualizarStepper = (pasoActual) => {
        for (let i = 1; i <= 2; i++) {
            const elemento = el(`st-${i}`);
            elemento.className = `step-item${i === pasoActual ? ' active' : i < pasoActual ? ' done' : ''}`;
            elemento.querySelector('.step-num').textContent = i < pasoActual ? '✓' : String(i);
            i === pasoActual
                ? elemento.setAttribute('aria-current', 'step')
                : elemento.removeAttribute('aria-current');
        }
    };

    /* ════════════════════════════════════════════════
       NAVEGACIÓN ENTRE PASOS
    ════════════════════════════════════════════════ */
    const irAlSiguiente = () => {
        estado.tocado = true;
        if (!validarPaso1()) return;

        estado.paso = 2;
        el('step-1').classList.remove('active');
        el('step-2').classList.add('active');
        actualizarStepper(2);

        el('modal-subtitle').textContent = 'Paso 2 de 2 · Archivos multimedia';
        el('btn-back').style.display = '';
        el('btn-next').style.display = 'none';
        el('btn-submit').style.display = '';
        el('req-note').style.display = 'none';
        el('modal-body').scrollTop = 0;
    };

    const irAlAnterior = () => {
        estado.paso = 1;
        el('step-2').classList.remove('active');
        el('step-1').classList.add('active');
        actualizarStepper(1);

        el('modal-subtitle').textContent = 'Paso 1 de 2 · Información básica';
        el('btn-back').style.display = 'none';
        el('btn-next').style.display = '';
        el('btn-submit').style.display = 'none';
        el('req-note').style.display = '';
        el('btn-next').disabled = !formularioValido();
        el('modal-body').scrollTop = 0;
    };

    /* ════════════════════════════════════════════════
       VALIDACIÓN
    ════════════════════════════════════════════════ */
    const formularioValido = () =>
        el('input-title').value.trim().length >= 3 &&
        el('input-content').value.trim().length >= 10;

    const validarPaso1 = () => {
        let valido = true;

        const titulo = el('input-title').value.trim();
        titulo.length < 3
            ? (mostrarError('title-err', 'input-title'), (valido = false))
            : limpiarError('title-err', 'input-title');

        const descripcion = el('input-content').value.trim();
        descripcion.length < 10
            ? (mostrarError('desc-err', 'input-content'), (valido = false))
            : limpiarError('desc-err', 'input-content');

        return valido;
    };

    const mostrarError = (idError, idInput) => {
        el(idError).classList.add('show');
        el(idInput).classList.add('invalid');
    };

    const limpiarError = (idError, idInput) => {
        el(idError).classList.remove('show');
        el(idInput).classList.remove('invalid');
    };

    /* ── Eventos de inputs ────────────────────────── */
    const alEscribirTitulo = () => {
        if (el('input-title').value.trim().length >= 3) limpiarError('title-err', 'input-title');
        el('btn-next').disabled = !formularioValido();
    };

    const alEscribirDescripcion = () => {
        const input = el('input-content');
        if (input.value.length > 500) input.value = input.value.slice(0, 500);

        const caracteres = Math.min(input.value.length, 500);
        el('char-n').textContent = caracteres;
        el('char-count').classList.toggle('warn', caracteres >= 450);

        if (caracteres >= 10) limpiarError('desc-err', 'input-content');
        el('btn-next').disabled = !formularioValido();
    };

    /* ════════════════════════════════════════════════
       TECNOLOGÍAS
    ════════════════════════════════════════════════ */
    const renderizarTecnologiasDisponibles = (busqueda) => {
        const filtradas = estado.tecnologiasDisponibles.filter(
            t => !estado.tecnologiasSeleccionadas.includes(t) &&
                t.toLowerCase().includes(busqueda.toLowerCase())
        );

        el('tech-tags').innerHTML = filtradas.length
            ? filtradas.map(t =>
                `<button class="chip" onclick="seleccionarTecnologia('${t}')" aria-label="Agregar ${t}">${t}</button>`
            ).join('')
            : '<span class="tech-empty">Sin resultados</span>';
    };

    const renderizarTecnologiasSeleccionadas = () => {
        el('selected-tags').innerHTML = estado.tecnologiasSeleccionadas
            .map(t =>
                `<button class="chip on" onclick="quitarTecnologia('${t}')" aria-label="Quitar ${t}">
                ${t} <span class="chip-x" aria-hidden="true">✕</span>
             </button>`
            ).join('');
    };

    const seleccionarTecnologia = (nombre) => {
        estado.tecnologiasSeleccionadas.push(nombre);
        renderizarTecnologiasSeleccionadas();
        filtrarTecnologias();
    };

    const quitarTecnologia = (nombre) => {
        estado.tecnologiasSeleccionadas = estado.tecnologiasSeleccionadas.filter(t => t !== nombre);
        renderizarTecnologiasSeleccionadas();
        filtrarTecnologias();
    };

    const filtrarTecnologias = () =>
        renderizarTecnologiasDisponibles(el('tech-search').value);

    /* ════════════════════════════════════════════════
       TIPO DE ARCHIVO
    ════════════════════════════════════════════════ */
    const CONFIGURACION_TIPO = {
        image: { accept: 'image/*', hint: `PNG, JPG, WEBP · máx ${MAXIMO_TAMANO_MB} MB · hasta ${MAXIMO_ARCHIVOS} archivos` },
        video: { accept: 'video/*', hint: `MP4, MOV, WEBM · máx ${MAXIMO_TAMANO_MB} MB · hasta ${MAXIMO_ARCHIVOS} archivos` },
        gif: { accept: 'image/gif', hint: `GIF animado · máx ${MAXIMO_TAMANO_MB} MB · hasta ${MAXIMO_ARCHIVOS} archivos` },
    };

    const cambiarTipoArchivo = (tipo, boton) => {
        todos('.mtype-btn').forEach(b => {
            b.classList.remove('on');
            b.setAttribute('aria-pressed', 'false');
        });
        boton.classList.add('on');
        boton.setAttribute('aria-pressed', 'true');

        el('input-media-type').value = tipo;
        el('file-input').accept = CONFIGURACION_TIPO[tipo].accept;
        el('file-hint').textContent = CONFIGURACION_TIPO[tipo].hint;
    };

    const resetearTipoArchivo = () => {
        todos('.mtype-btn').forEach(b => {
            b.classList.remove('on');
            b.setAttribute('aria-pressed', 'false');
        });
        const botonImagen = document.querySelector('.mtype-btn[data-type="image"]');
        botonImagen.classList.add('on');
        botonImagen.setAttribute('aria-pressed', 'true');

        el('input-media-type').value = 'image';
        el('file-input').accept = CONFIGURACION_TIPO.image.accept;
        el('file-hint').textContent = CONFIGURACION_TIPO.image.hint;
    };

    /* ════════════════════════════════════════════════
       MANEJO DE ARCHIVOS
    ════════════════════════════════════════════════ */
    const mostrarErrorArchivo = (mensaje) => {
        el('media-err-text').textContent = mensaje;
        el('media-err').removeAttribute('hidden');
    };

    const ocultarErrorArchivo = () =>
        el('media-err').setAttribute('hidden', '');

    const alArrastrarSobre = (e) => { e.preventDefault(); el('drop-zone').classList.add('over'); };
    const alSalirArrastre = () => el('drop-zone').classList.remove('over');
    const alSoltar = (e) => {
        e.preventDefault();
        el('drop-zone').classList.remove('over');
        procesarArchivos(e.dataTransfer.files);
    };

    const procesarArchivos = (lista) => {
        ocultarErrorArchivo();
        const rechazados = [];

        Array.from(lista).forEach(archivo => {
            if (estado.archivos.length >= MAXIMO_ARCHIVOS) {
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

        if (rechazados.length) mostrarErrorArchivo(rechazados.join(' · '));
        renderizarGrilla();
    };

    const renderizarGrilla = () => {
        el('media-grid').innerHTML = estado.archivos.map((item, i) => `
        <div class="thumb" role="listitem">
            ${item.type.startsWith('video')
                ? `<video src="${item.url}" muted loop></video>`
                : `<img src="${item.url}" alt="Archivo ${i + 1}" />`}
            <button class="rm" onclick="eliminarArchivo(${i})" aria-label="Eliminar archivo ${i + 1}">✕</button>
        </div>`
        ).join('');
    };

    const eliminarArchivo = (indice) => {
        URL.revokeObjectURL(estado.archivos[indice].url);
        estado.archivos.splice(indice, 1);
        ocultarErrorArchivo();
        renderizarGrilla();
    };

    /* ════════════════════════════════════════════════
       BANNER DE ERROR DEL SERVIDOR
    ════════════════════════════════════════════════ */
    const mostrarErrorServidor = (mensajes) => {
        el('server-error-list').innerHTML = mensajes.map(m => `<li>${m}</li>`).join('');
        el('server-error-banner').removeAttribute('hidden');
        el('server-error-banner').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    };

    const ocultarErrorServidor = () => {
        el('server-error-banner').setAttribute('hidden', '');
        el('server-error-list').innerHTML = '';
    };

    /* ════════════════════════════════════════════════
       ENVÍO DEL FORMULARIO
    ════════════════════════════════════════════════ */
    const publicarProyecto = () => {
        ocultarErrorServidor();

        const boton = el('btn-submit');
        boton.disabled = true;
        boton.textContent = 'Publicando...';

        const datos = new FormData();
        datos.append('title', el('input-title').value.trim());
        datos.append('content', el('input-content').value.trim());
        datos.append('privacy', el('input-privacy').value);
        datos.append('media_type', el('input-media-type').value);

        estado.tecnologiasSeleccionadas.forEach(tech => datos.append('techs[]', tech));
        estado.archivos.forEach((item, i) => datos.append(`media[${i}]`, item.file));

        fetch('/projects', {
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
                    mostrarErrorServidor(Object.values(data.errors).flat());
                    boton.disabled = false;
                    boton.textContent = 'Publicar proyecto';
                    return;
                }

                if (!data.success) throw new Error(data.message);

                todos('.step-panel').forEach(p => p.classList.remove('active'));
                el('success-state').style.display = 'flex';
                el('modal-footer').style.display = 'none';
                el('stepper').style.display = 'none';
                el('modal-title').textContent = 'Proyecto publicado';
                el('modal-subtitle').textContent = '';
                setTimeout(() => cerrarModal(), 2200);
            })
            .catch(error => {
                mostrarErrorServidor([error.message || 'Ocurrió un error inesperado. Inténtalo de nuevo.']);
                boton.disabled = false;
                boton.textContent = 'Publicar proyecto';
            });
    };

    /* ════════════════════════════════════════════════
       EVENTOS GLOBALES
    ════════════════════════════════════════════════ */
    document.addEventListener('keydown', ({ key }) => { if (key === 'Escape') cerrarModal(); });

    el('modal-overlay').addEventListener('click', function (e) {
        if (e.target === this) cerrarModal();
    });

    /* ════════════════════════════════════════════════
       EXPORTS — funciones llamadas desde el HTML
       (onclick="...", etc.) necesitan vivir en window
    ════════════════════════════════════════════════ */
    window.abrirModal = abrirModal;
    window.cerrarModal = cerrarModal;
    window.irAlSiguiente = irAlSiguiente;
    window.irAlAnterior = irAlAnterior;
    window.publicarProyecto = publicarProyecto;
    window.eliminarArchivo = eliminarArchivo;
    window.seleccionarTecnologia = seleccionarTecnologia;
    window.quitarTecnologia = quitarTecnologia;
    window.filtrarTecnologias = filtrarTecnologias;
    window.cambiarTipoArchivo = cambiarTipoArchivo;
    window.alEscribirTitulo = alEscribirTitulo;
    window.alEscribirDescripcion = alEscribirDescripcion;
    window.alArrastrarSobre = alArrastrarSobre;
    window.alSalirArrastre = alSalirArrastre;
    window.alSoltar = alSoltar;
    window.procesarArchivos = procesarArchivos;

})();