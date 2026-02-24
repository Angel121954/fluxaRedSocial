@extends('layouts.app')
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

        <form id="editForm">
            <!-- Nombre -->
            <div class="form-group">
                <label class="form-label" for="inputName">Nombre completo</label>
                <input
                    type="text"
                    class="form-input"
                    id="inputName"
                    value="{{ Auth::user()->name }}"
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
                    value="{{ Auth::user()->username }}"
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
                    placeholder="Full stack developer...">
Full stack developer construyendo en público.
Apasionado por TypeScript, APIs y el arte de lanzar productos.</textarea>
                <div class="char-count" id="charCount">120/160</div>
            </div>

            <!-- Ubicación -->
            <div class="form-group">
                <label class="form-label" for="inputLocation">Ubicación</label>
                <select class="form-input" id="inputLocation">
                    <option>📍 Ciudad de México, México</option>
                    <option>📍 Buenos Aires, Argentina</option>
                    <option>📍 Madrid, España</option>
                    <option>📍 Bogotá, Colombia</option>
                    <option>📍 Lima, Perú</option>
                    <option>📍 Santiago, Chile</option>
                    <option selected>📍 Pereira, Colombia</option>
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

    function updateCharCount() {
        const current = bioInput.value.length;
        const max = bioInput.getAttribute("maxlength");
        charCount.textContent = `${current}/${max}`;
    }

    bioInput.addEventListener("input", updateCharCount);
    updateCharCount();

    // ── Avatar actions ──────────────────────────────────
    const fileInput = document.getElementById("fileInput");
    const avatarImg = document.getElementById("avatarImg");
    const btnUpload = document.getElementById("btnUpload");
    const btnChange = document.getElementById("btnChange");
    const btnView = document.getElementById("btnView");
    const btnDelete = document.getElementById("btnDelete");

    btnUpload.addEventListener("click", (e) => {
        e.stopPropagation();
        fileInput.click();
    });

    btnChange.addEventListener("click", () => {
        fileInput.click();
    });

    fileInput.addEventListener("change", (e) => {
        const file = e.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = (ev) => {
            avatarImg.src = ev.target.result;
            // Aquí enviarías la imagen al servidor
            console.log("Nueva foto cargada");
        };
        reader.readAsDataURL(file);
    });

    btnDelete.addEventListener("click", () => {
        if (
            confirm("¿Estás seguro de que quieres eliminar tu foto de perfil?")
        ) {
            avatarImg.src =
                "https://ui-avatars.com/api/?name=Lucas+Silva&size=400&background=12b3b6&color=fff";
            console.log("Foto eliminada");
        }
    });

    btnView.addEventListener("click", (e) => {
        e.stopPropagation();
        window.open(avatarImg.src, "_blank");
    });

    // ── Form submit ─────────────────────────────────────
    const editForm = document.getElementById("editForm");
    const btnSaveTop = document.getElementById("btnSaveTop");

    function saveChanges(e) {
        e.preventDefault();

        const formData = {
            name: document.getElementById("inputName").value,
            handle: document.getElementById("inputHandle").value,
            bio: document.getElementById("inputBio").value,
            location: document.getElementById("inputLocation").value,
            website: document.getElementById("inputWebsite").value,
        };

        console.log("Guardando cambios:", formData);

        // Aquí harías el POST al servidor
        // fetch('/api/profile/update', { ... })

        // Simular guardado exitoso
        alert("✅ Perfil actualizado correctamente");

        // Opcional: redirigir al perfil
        // window.location.href = 'perfil.html';
    }

    editForm.addEventListener("submit", saveChanges);
    btnSaveTop.addEventListener("click", (e) => {
        e.preventDefault();
        editForm.dispatchEvent(new Event("submit"));
    });

    // ── Sidebar navigation ──────────────────────────────
    document.querySelectorAll(".sidebar-item").forEach((item) => {
        item.addEventListener("click", function() {
            document
                .querySelectorAll(".sidebar-item")
                .forEach((i) => i.classList.remove("active"));
            this.classList.add("active");

            // Aquí cambiarías el contenido según la sección
            console.log("Navegando a:", this.textContent);
        });
    });

    console.log("✅ Edit profile page loaded");
</script>
@endpush