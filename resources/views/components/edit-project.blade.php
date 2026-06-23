<div id="edit-modal-overlay" role="dialog" aria-modal="true" aria-labelledby="edit-modal-title">
    <div id="edit-modal-card">

        {{-- Header --}}
        <div id="edit-modal-header">
            <div>
                <div id="edit-modal-title">Editar proyecto</div>
                <div id="edit-modal-subtitle">Paso 1 de 2 · Información básica</div>
            </div>
            <button type="button" class="close-btn" onclick="cerrarEditModal()" aria-label="Cerrar">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                    <line x1="18" y1="6" x2="6" y2="18" />
                    <line x1="6" y1="6" x2="18" y2="18" />
                </svg>
            </button>
        </div>

        {{-- Body --}}
        <div id="edit-modal-body">

            {{-- Success state --}}
            <div id="edit-success-state" role="status" aria-live="polite">
                <div class="success-icon" aria-hidden="true">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                        <polyline points="20 6 9 17 4 12" />
                    </svg>
                </div>
                <h3>Proyecto actualizado</h3>
                <p>Los cambios han sido guardados</p>
            </div>

            {{-- Server error banner --}}
            <div id="edit-server-error-banner" class="server-error-banner" role="alert" aria-live="assertive" hidden>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true">
                    <circle cx="12" cy="12" r="10" />
                    <line x1="12" y1="8" x2="12" y2="12" />
                    <line x1="12" y1="16" x2="12.01" y2="16" />
                </svg>
                <ul id="edit-server-error-list" class="server-error-list"></ul>
            </div>

            <form id="edit-project-form" method="POST" enctype="multipart/form-data" action="">
                @csrf
                @method('PUT')

                <input type="hidden" id="edit-input-privacy" name="privacy" value="public" />
                <input type="hidden" id="edit-project-id" value="" />

                {{-- STEP 1 --}}
                <div id="edit-step-1" class="step-panel active">

                    <div class="field">
                        <label for="edit-input-title">Título <span class="req">*</span></label>
                        <input id="edit-input-title" name="title" type="text" class="np-input"
                            placeholder="Ej: App de gestión de tareas con IA"
                            oninput="alEscribirEditTitulo()" aria-required="true"
                            aria-describedby="edit-title-err" autocomplete="off" maxlength="100" />
                        <span id="edit-title-err" class="field-error" role="alert">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                                <circle cx="12" cy="12" r="10" />
                                <line x1="12" y1="8" x2="12" y2="12" />
                                <line x1="12" y1="16" x2="12.01" y2="16" />
                            </svg>
                            <span id="edit-title-err-text">Mínimo 3 caracteres</span>
                        </span>
                    </div>

                    <div class="field">
                        <label for="edit-input-content">Descripción <span class="req">*</span></label>
                        <span class="field-hint">¿Qué hace el proyecto? ¿Qué aprendiste? ¿Cuáles fueron los retos?</span>
                        <textarea id="edit-input-content" name="content" class="np-input"
                            placeholder="Describe tu proyecto..."
                            oninput="alEscribirEditDescripcion()" aria-required="true"
                            aria-describedby="edit-desc-err edit-char-count"
                            rows="4" maxlength="500"></textarea>
                        <div style="display:flex;justify-content:space-between;align-items:center;min-height:1.125rem;">
                            <span id="edit-desc-err" class="field-error" role="alert">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                                    <circle cx="12" cy="12" r="10" />
                                    <line x1="12" y1="8" x2="12" y2="12" />
                                    <line x1="12" y1="16" x2="12.01" y2="16" />
                                </svg>
                                <span id="edit-desc-err-text">Mínimo 10 caracteres</span>
                            </span>
                            <span id="edit-char-count" class="char-row" style="margin-left:auto;"><span id="edit-char-n">0</span>/500</span>
                        </div>
                    </div>

                    <div class="field">
                        <label>Tecnologías <span class="label-opt">(opcional)</span></label>
                        <span class="field-hint">Selecciona las herramientas y lenguajes usados.</span>
                        <div id="edit-selected-tags" aria-label="Tecnologías seleccionadas" aria-live="polite"></div>
                        <input id="edit-tech-search" type="search" class="np-input"
                            placeholder="Buscar tecnología..."
                            oninput="filtrarEditTecnologias()" aria-label="Buscar tecnología" autocomplete="off" />
                        <div id="edit-tech-tags" role="group" aria-label="Tecnologías disponibles"></div>
                        <span id="edit-techs-err" class="field-error" role="alert" hidden>
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                                <circle cx="12" cy="12" r="10" />
                                <line x1="12" y1="8" x2="12" y2="12" />
                                <line x1="12" y1="16" x2="12.01" y2="16" />
                            </svg>
                            <span id="edit-techs-err-text"></span>
                        </span>
                    </div>

                </div>

                {{-- STEP 2 --}}
                <div id="edit-step-2" class="step-panel">

                    {{-- Existing media --}}
                    <div id="edit-existing-media-section" class="field" style="display:none;">
                        <label>Multimedia actual</label>
                        <div id="edit-media-grid-existing" class="edit-media-grid" role="list" aria-label="Multimedia existente"></div>
                    </div>

                    <div class="field">
                        <label>Tipo de archivo <span class="label-opt">(opcional)</span></label>
                        <input type="hidden" id="edit-input-media-type" name="media_type" value="image" />
                        <div id="edit-media-types" role="group" aria-label="Tipo de archivo">
                            <button type="button" class="mtype-btn on" data-type="image"
                                onclick="cambiarEditTipoArchivo('image', this)" aria-pressed="true">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                    <rect x="3" y="3" width="18" height="18" rx="2" />
                                    <circle cx="8.5" cy="8.5" r="1.5" />
                                    <polyline points="21 15 16 10 5 21" />
                                </svg>
                                Imagen
                            </button>
                            <button type="button" class="mtype-btn" data-type="video"
                                onclick="cambiarEditTipoArchivo('video', this)" aria-pressed="false">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                    <polygon points="23 7 16 12 23 17 23 7" />
                                    <rect x="1" y="5" width="15" height="14" rx="2" />
                                </svg>
                                Video
                            </button>
                            <button type="button" class="mtype-btn" data-type="gif"
                                onclick="cambiarEditTipoArchivo('gif', this)" aria-pressed="false">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                    <rect x="2" y="2" width="20" height="20" rx="3" />
                                    <text x="5.5" y="16" font-size="7.5" fill="currentColor" stroke="none" font-weight="700" font-family="Inter,sans-serif">GIF</text>
                                </svg>
                                GIF
                            </button>
                        </div>
                    </div>

                    <div class="field">
                        <div id="edit-drop-zone" tabindex="0" role="button"
                            aria-label="Área de carga. Haz clic o arrastra archivos"
                            onclick="document.getElementById('edit-file-input').click()"
                            onkeydown="if(event.key==='Enter'||event.key===' ')document.getElementById('edit-file-input').click()"
                            ondragover="alArrastrarSobreEdit(event)"
                            ondragleave="alSalirArrastreEdit()"
                            ondrop="alSoltarEdit(event)">
                            <div class="dz-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round">
                                    <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4" />
                                    <polyline points="17 8 12 3 7 8" />
                                    <line x1="12" y1="3" x2="12" y2="15" />
                                </svg>
                            </div>
                            <p class="dz-title"><span class="dz-link">Selecciona archivos</span> o arrastra aquí</p>
                            <p id="edit-file-hint" class="dz-sub">PNG, JPG, WEBP · máx 10 MB · hasta 6 archivos</p>
                            <input id="edit-file-input" type="file" name="media[]" hidden multiple
                                accept="image/*" onchange="procesarEditArchivos(this.files)" />
                        </div>
                        <span id="edit-media-err" class="field-error" role="alert" hidden>
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                                <circle cx="12" cy="12" r="10" />
                                <line x1="12" y1="8" x2="12" y2="12" />
                                <line x1="12" y1="16" x2="12.01" y2="16" />
                            </svg>
                            <span id="edit-media-err-text"></span>
                        </span>
                    </div>

                    <div id="edit-media-grid" role="list" aria-label="Archivos cargados"></div>

                </div>

            </form>

        </div>

        {{-- Footer --}}
        <div id="edit-modal-footer">
            <div class="f-left">
                <button type="button" id="edit-btn-back" class="btn btn-secondary" onclick="irAlAnteriorEdit()" style="display:none;">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                        <polyline points="15 18 9 12 15 6" />
                    </svg>
                    Atrás
                </button>
                <span id="edit-req-note" class="req-note"><span class="req">*</span> Obligatorio</span>
            </div>
            <div class="f-right">
                <button type="button" id="edit-btn-cancel" class="btn btn-secondary" onclick="cerrarEditModal()">Cancelar</button>
                <button type="button" id="edit-btn-next" class="btn btn-primary" onclick="irAlSiguienteEdit()" disabled>
                    Continuar
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                        <polyline points="9 18 15 12 9 6" />
                    </svg>
                </button>
                <button type="button" id="edit-btn-submit" class="btn btn-primary" onclick="guardarEditProyecto()" style="display:none;">
                    Guardar cambios
                </button>
            </div>
        </div>

    </div>
</div>

@push('styles')
@vite('resources/css/projects/editProject.css')
@endpush
@push('scripts')
@vite('resources/js/core/projects/editProject.js')
@endpush
