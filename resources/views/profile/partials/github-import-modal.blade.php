<div class="modal-backdrop" id="githubImportModal">
    <div class="modal-card github-import-card">
        <div class="modal-header">
            <div class="modal-header-text">
                <h2 class="modal-title">Importar desde GitHub</h2>
            </div>
            <button class="modal-close" id="githubModalClose">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div class="modal-body">
            <div id="githubImportStatus" class="github-import-status hidden">
                <div class="github-spinner"></div>
                <span>Cargando repositorios...</span>
            </div>
            <div id="githubImportError" class="github-import-error hidden"></div>
            <div id="githubImportList" class="github-repo-list hidden"></div>
            <div id="githubImportEmpty" class="github-import-empty hidden">
                <p>No se encontraron repositorios en tu cuenta de GitHub.</p>
            </div>
            <div id="githubImportNoToken" class="github-import-no-token hidden">
                <p>Necesitas conectar tu cuenta de GitHub para importar proyectos.</p>
                <a href="{{ route('github.connect') }}" class="btn-primary">Conectar GitHub</a>
            </div>
        </div>
    </div>
</div>
