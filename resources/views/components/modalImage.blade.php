<!-- Modal imagen -->
<div class="img-modal" id="imgModal">
    <div class="modal-wrap">
        <button class="modal-x" id="modalX">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
        <img
            src="{{ $profile->avatar ?? '' }}"
            id="modalImg"
            alt="Foto de perfil"
            width="100"
            height="100" />
    </div>
</div>