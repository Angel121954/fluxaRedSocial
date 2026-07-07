<x-modal id="stackModal" title="Tecnologías" subtitle="Selecciona las tecnologías con las que trabajas">
    <div class="stack-modal-search">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="8" />
            <path d="m21 21-4.35-4.35" />
        </svg>
        <input type="text" placeholder="Buscar tecnología..." id="stackModalSearch">
    </div>

    @php
    $categoryLabels = App\Models\Technology::categoryLabels();
    $grouped = $allTechnologies->groupBy(fn($t) => $t->category ?? 'other');
    $categoryOrder = App\Models\Technology::categoryOrder();
    @endphp

    <div class="stack-modal-grid" id="stackModalGrid">
        @forelse($categoryOrder as $cat)
            @php $items = $grouped->get($cat); @endphp
            @if($items && $items->isNotEmpty())
            <div class="stack-modal-category" data-category="{{ $cat }}">
                <h4 class="stack-modal-category-title">{{ $categoryLabels[$cat] ?? 'Otros' }}</h4>
                <div class="stack-modal-category-items">
                    @foreach($items as $tech)
                    <div class="stack-modal-item" data-name="{{ strtolower($tech->name) }}">
                        <input type="checkbox" name="technologies[]" value="{{ $tech->id }}"
                            id="st_{{ $tech->id }}"
                            {{ $userTechnologies->contains($tech->id) ? 'checked' : '' }}>
                        <label for="st_{{ $tech->id }}">
                            <div class="stack-modal-check">
                                <svg viewBox="0 0 10 10" fill="none">
                                    <path d="M2 5l2.5 2.5L8 3" stroke="#fff" stroke-width="1.5" stroke-linecap="round" />
                                </svg>
                            </div>
                            <img src="{{ $tech->iconUrl() }}" alt="{{ $tech->name }}" class="" loading="lazy" width="16" height="16">
                            {{ $tech->name }}
                        </label>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        @empty
        <p class="stack-modal-empty">No se encontraron tecnologías.</p>
        @endforelse
    </div>

    <x-slot:footer>
        <button class="stack-modal-btn-cancel" id="stackModalCancel" data-close="stackModal">Cancelar</button>
        <button class="stack-modal-btn-save" id="stackModalSave">Guardar cambios</button>
    </x-slot:footer>
</x-modal>
