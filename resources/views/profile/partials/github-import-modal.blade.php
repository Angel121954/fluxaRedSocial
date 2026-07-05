<x-modal id="githubImportModal" title="Importar desde GitHub" hideClose>
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
</x-modal>
