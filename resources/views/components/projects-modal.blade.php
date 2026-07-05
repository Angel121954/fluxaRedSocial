<x-modal id="projectsModal" maxWidth="sm">
    <x-slot:header>
        <div class="modal-header-text">
            <div class="modal-title" id="projectsModalTitle">Proyectos</div>
            <div class="modal-subtitle" id="projectsModalSubtitle">Proyectos publicados por este usuario</div>
        </div>
    </x-slot:header>

    <div class="projects-body" id="projectsModalBody">
        <div class="projects-loading">
            <div class="projects-skeleton"></div>
            <div class="projects-skeleton"></div>
            <div class="projects-skeleton"></div>
        </div>
    </div>
</x-modal>
