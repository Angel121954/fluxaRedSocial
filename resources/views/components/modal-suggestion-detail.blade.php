<x-modal id="suggestionDetailModal" title="Detalle de sugerencia" subtitle="Información completa de la sugerencia">
    <div class="sdm-field">
        <span class="sdm-label">Estado</span>
        <span class="sdm-badge adm-badge" id="sdmStatus">
            <span class="adm-badge-dot"></span>
            <span id="sdmStatusText"></span>
        </span>
    </div>

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

    <div class="sdm-field">
        <span class="sdm-label">Fecha</span>
        <span class="sdm-value" id="sdmDate"></span>
    </div>

    <div class="sdm-field">
        <span class="sdm-label">Descripción</span>
        <p class="sdm-description" id="sdmDescription"></p>
    </div>

    <div class="sdm-field" id="sdmImageField" style="display:none;">
        <span class="sdm-label">Imagen adjunta</span>
        <div class="sdm-image-wrap">
            <img class="sdm-image" id="sdmImage" src="" alt="Imagen de la sugerencia">
        </div>
    </div>

    <x-slot:footer>
        <button type="button" class="btn btn-secondary" data-close="suggestionDetailModal">Cerrar</button>
    </x-slot:footer>
</x-modal>
