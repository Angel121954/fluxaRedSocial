@extends('layouts.app')
@section('title', 'Configuración')
@section('content')
@include('components.topbar')
<!-- ══════════════════════════════════════════
     EDIT LAYOUT
══════════════════════════════════════════ -->
<div class="edit-layout">
    @include('components.sidebar')

    <!-- ──── MAIN CONTENT ──── -->
    <main class="main-content">
        <h1 class="page-title">Editar perfil</h1>
        <p class="page-subtitle">
            Actualiza tu información personal y cómo apareces en Fluxa
        </p>

        <form id="editForm" method="post" action="#">
            <!-- Nombre -->
            <div class="form-group">
                <label class="form-label" for="inputName">Nombre completo</label>
                <input
                    type="text"
                    class="form-input"
                    id="inputName"
                    value="{{ old('name', Auth::user()->name ?? '') }}"
                    placeholder="Tu nombre completo" />
            </div>

            <!-- Handle -->
            <div class="form-group">
                <label class="form-label" for="inputHandle">Nombre de usuario</label>
                <span class="form-hint">Tu URL será fluxa.com/@tunombre</span>
                <input
                    type="text"
                    class="form-input"
                    id="inputHandle"
                    value="{{ old('username', Auth::user()->username ?? '') }}"
                    placeholder="tunombre" />
            </div>

            <!-- Bio -->
            <div class="form-group">
                <label class="form-label" for="inputBio">Biografía</label>
                <span class="form-hint">Cuéntanos sobre ti en pocas palabras</span>
                <textarea
                    class="form-input"
                    id="inputBio"
                    maxlength="160"
                    placeholder="Full stack developer...">{{ Auth()->user()->profile->bio ?? '' }}</textarea>
                <div class="char-count" id="charCount">120/160</div>
            </div>

            <!-- Ubicación -->
            <div class="form-group">
                <label class="form-label" for="inputLocation">Ubicación</label>
                <select class="form-input" id="inputLocation">
                    @php
                    $currentLocation = old('location', Auth()->user()->profile->location ?? 'pereira')
                    @endphp
                    <option value="bogotá" {{ $currentLocation == 'bogotá' ? 'selected' : '' }}>Bogotá</option>
                    <option value="medellín" {{ $currentLocation == 'medellín' ? 'selected' : '' }}>Medellín</option>
                    <option value="cali" {{ $currentLocation == 'cali' ? 'selected' : '' }}>Cali</option>
                    <option value="barranquilla" {{ $currentLocation == 'barranquilla' ? 'selected' : '' }}>Barranquilla</option>
                    <option value="cartagena" {{ $currentLocation == 'cartagena' ? 'selected' : '' }}>Cartagena</option>
                    <option value="cúcuta" {{ $currentLocation == 'cúcuta' ? 'selected' : '' }}>Cúcuta</option>
                    <option value="bucaramanga" {{ $currentLocation == 'bucaramanga' ? 'selected' : '' }}>Bucaramanga</option>
                    <option value="pereira" {{ $currentLocation == 'pereira' ? 'selected' : '' }}>Pereira</option>
                    <option value="manizales" {{ $currentLocation == 'manizales' ? 'selected' : '' }}>Manizales</option>
                    <option value="armenia" {{ $currentLocation == 'armenia' ? 'selected' : '' }}>Armenia</option>
                    <option value="ibagué" {{ $currentLocation == 'ibagué' ? 'selected' : '' }}>Ibagué</option>
                    <option value="villavicencio" {{ $currentLocation == 'villavicencio' ? 'selected' : '' }}>Villavicencio</option>
                    <option value="santa marta" {{ $currentLocation == 'santa marta' ? 'selected' : '' }}>Santa Marta</option>
                    <option value="neiva" {{ $currentLocation == 'neiva' ? 'selected' : '' }}>Neiva</option>
                    <option value="popayán" {{ $currentLocation == 'popayán' ? 'selected' : '' }}>Popayán</option>
                    <option value="sincelejo" {{ $currentLocation == 'sincelejo' ? 'selected' : '' }}>Sincelejo</option>
                    <option value="riohacha" {{ $currentLocation == 'riohacha' ? 'selected' : '' }}>Riohacha</option>
                    <option value="quibdó" {{ $currentLocation == 'quibdó' ? 'selected' : '' }}>Quibdó</option>
                    <option value="yopal" {{ $currentLocation == 'yopal' ? 'selected' : '' }}>Yopal</option>
                    <option value="leticia" {{ $currentLocation == 'leticia' ? 'selected' : '' }}>Leticia</option>
                </select>
            </div>

            <!-- Website -->
            <div class="form-group">
                <label class="form-label" for="inputWebsite">Sitio web</label>
                <div class="input-with-button">
                    <input
                        type="url"
                        class="form-input"
                        id="inputWebsite"
                        value="https://linkt.cons/lucassilva"
                        placeholder="https://tusitio.com" />
                    <a
                        href="https://linkt.cons/lucassilva"
                        target="_blank"
                        class="btn-link">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                        Visitar
                    </a>
                </div>
            </div>

            <!-- Actions -->
            <div class="form-actions">
                <button
                    type="button"
                    class="btn-cancel"
                    onclick="window.location.href = 'perfil.html'">
                    Cancelar
                </button>
                <button type="submit" class="btn-submit">Guardar cambios</button>
            </div>
        </form>
    </main>
</div>

<input type="file" id="fileInput" accept="image/*" style="display: none" />
@endsection
@push('styles')
<!--Estilo personalizado de configuración-->
<link rel="stylesheet" href="{{ asset('css/configuration.css') }}" />
@endpush
@push('scripts')
<script>
    // ── Char counter ────────────────────────────────────
    const bioInput = document.getElementById("inputBio");
    const charCount = document.getElementById("charCount");

    if (bioInput && charCount) {
        function updateCharCount() {
            const current = bioInput.value.length;
            const max = bioInput.getAttribute("maxlength");
            charCount.textContent = `${current}/${max}`;
        }
        bioInput.addEventListener("input", updateCharCount);
        updateCharCount();
    }

    // ── Avatar actions ──────────────────────────────────
    const fileInput = document.getElementById("fileInput");
    const avatarImg = document.getElementById("avatarImg");
    const btnUpload = document.getElementById("btnUpload");
    const btnChange = document.getElementById("btnChange");
    const btnView = document.getElementById("btnView");
    const btnDelete = document.getElementById("btnDelete");

    if (btnUpload && fileInput) {
        btnUpload.addEventListener("click", (e) => {
            e.stopPropagation();
            fileInput.click();
        });
    }

    if (btnChange && fileInput) {
        btnChange.addEventListener("click", () => {
            fileInput.click();
        });
    }

    if (fileInput && avatarImg) {
        fileInput.addEventListener("change", (e) => {
            const file = e.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = (ev) => {
                avatarImg.src = ev.target.result;
                console.log("Nueva foto cargada");
            };
            reader.readAsDataURL(file);
        });
    }

    if (btnDelete && avatarImg) {
        btnDelete.addEventListener("click", () => {
            if (confirm("¿Estás seguro de que quieres eliminar tu foto de perfil?")) {
                avatarImg.src = "https://ui-avatars.com/api/?name=Lucas+Silva&size=400&background=12b3b6&color=fff";
                console.log("Foto eliminada");
            }
        });
    }

    if (btnView && avatarImg) {
        btnView.addEventListener("click", (e) => {
            e.stopPropagation();
            window.open(avatarImg.src, "_blank");
        });
    }

    // ── Form submit ─────────────────────────────────────
    const editForm = document.getElementById("editForm");
    const btnSaveTop = document.getElementById("btnSaveTop");

    function saveChanges(e) {
        e.preventDefault();
        const formData = {
            name: document.getElementById("inputName")?.value,
            handle: document.getElementById("inputHandle")?.value,
            bio: document.getElementById("inputBio")?.value,
            location: document.getElementById("inputLocation")?.value,
            website: document.getElementById("inputWebsite")?.value,
        };
        console.log("Guardando cambios:", formData);
        alert("✅ Perfil actualizado correctamente");
    }

    if (editForm) {
        editForm.addEventListener("submit", saveChanges);
    }

    if (btnSaveTop && editForm) {
        btnSaveTop.addEventListener("click", (e) => {
            e.preventDefault();
            editForm.dispatchEvent(new Event("submit"));
        });
    }

    // ── Sidebar navigation ──────────────────────────────
    document.querySelectorAll(".sidebar-item").forEach((item) => {
        item.addEventListener("click", function() {
            document.querySelectorAll(".sidebar-item").forEach((i) => i.classList.remove("active"));
            this.classList.add("active");
            console.log("Navegando a:", this.textContent);
        });
    });

    console.log("✅ Edit profile page loaded");
</script>
@endpush