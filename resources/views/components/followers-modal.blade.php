<x-modal id="followersModal" maxWidth="sm">
    <x-slot:header>
        <div class="modal-header-text">
            <div class="modal-title" id="followersModalTitle">Seguidores</div>
            <div class="modal-subtitle" id="followersModalSubtitle"></div>
        </div>
    </x-slot:header>

    <div class="followers-body" id="followersModalBody">
        <div class="followers-loading">
            <div class="followers-skeleton"></div>
            <div class="followers-skeleton"></div>
            <div class="followers-skeleton"></div>
        </div>
    </div>
</x-modal>
