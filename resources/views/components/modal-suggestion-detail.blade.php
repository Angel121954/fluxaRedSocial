<div class="modal-backdrop" id="suggestionDetailModal">
    <div class="modal-card">
        {{-- Header --}}
        <div class="modal-header">
            <div>
                <div class="modal-title" id="suggestionDetailTitle">Detalle de sugerencia</div>
                <div class="modal-subtitle">Información completa de la sugerencia</div>
            </div>
            <button class="modal-close" data-close="suggestionDetailModal" aria-label="Cerrar">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                    <line x1="18" y1="6" x2="6" y2="18" />
                    <line x1="6" y1="6" x2="18" y2="18" />
                </svg>
            </button>
        </div>

        {{-- Body --}}
        <div class="modal-body">
            {{-- Estado --}}
            <div class="sdm-field">
                <span class="sdm-label">Estado</span>
                <span class="sdm-badge adm-badge" id="sdmStatus">
                    <span class="adm-badge-dot"></span>
                    <span id="sdmStatusText"></span>
                </span>
            </div>

            {{-- Usuario --}}
            <div class="sdm-field">
                <span class="sdm-label">Enviado por</span>
                <div class="sdm-user">
                    <img class="sdm-avatar" id="sdmAvatar" src="" alt="">
                    <div class="sdm-user-info">
                        <span class="sdm-user-name" id="sdmUserName"></span>
                        <span class="sdm-user-handle" id="sdmUserHandle"></span>
                    </div>
                </div>
            </div>

            {{-- Fecha --}}
            <div class="sdm-field">
                <span class="sdm-label">Fecha</span>
                <span class="sdm-value" id="sdmDate"></span>
            </div>

            {{-- Descripción --}}
            <div class="sdm-field">
                <span class="sdm-label">Descripción</span>
                <p class="sdm-description" id="sdmDescription"></p>
            </div>

            {{-- Imagen --}}
            <div class="sdm-field" id="sdmImageField" style="display:none;">
                <span class="sdm-label">Imagen adjunta</span>
                <div class="sdm-image-wrap">
                    <img class="sdm-image" id="sdmImage" src="" alt="Imagen de la sugerencia">
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-close="suggestionDetailModal">Cerrar</button>
        </div>
    </div>
</div>
