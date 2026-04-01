@extends('layouts.app')
@section('content')
@section('title', 'Explorar')
<x-topbar :profile="$profile" />

<!-- ══════════════════════════════════════════
     FEED LAYOUT
══════════════════════════════════════════ -->
<div class="feed-layout">
    <!-- ──── FEED COLUMN ──── -->
    <div class="feed-main">
        <!-- Tabs -->
        <div class="feed-tabs">
            <button class="feed-tab active" data-tab="trending" data-url="{{ route('explore.trending') }}">🔥 Tendencias</button>
            <button class="feed-tab" data-tab="recent" data-url="{{ route('explore.recent') }}">Recientes</button>
            <button class="feed-tab" data-tab="following" data-url="{{ route('explore.following') }}">Siguiendo</button>
        </div>

        <!-- Publications Container -->
        <div id="publications-container">
            <x-project-list :projects="$projects" />
        </div>
    </div>

    <!-- ──── SIDEBAR ──── -->
    <aside class="sidebar">
        <!-- Personas recomendadas -->
        <!--  <div class="widget">
          <div class="widget-header">
            <h3 class="widget-title">Personas recomendadas</h3>
          </div>

          <div class="person-item">
            <div class="person-av-wrap">
              <img
                src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=100"
                alt="Daniel Ruiz"
                class="person-av"
              />
              <div class="person-verify">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M5 13l4 4L19 7"
                  />
                </svg>
              </div>
            </div>
            <div class="person-info">
              <div class="person-name">Daniel Ruiz</div>
              <p class="person-bio">
                Desarrollador Full Stack, apasionado de JS y React
              </p>
            </div>
            <button class="btn-follow-mini">Seguir</button>
          </div>

          <div class="person-item">
            <div class="person-av-wrap">
              <img
                src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=100"
                alt="Marta Castillo"
                class="person-av"
              />
              <div class="person-verify">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M5 13l4 4L19 7"
                  />
                </svg>
              </div>
            </div>
            <div class="person-info">
              <div class="person-name">Marta Castillo</div>
              <p class="person-bio">
                Diseñadora UX/UI | Amante del diseño y de Figma
              </p>
            </div>
            <button class="btn-follow-mini">Seguir</button>
          </div>

          <div class="person-item">
            <div class="person-av-wrap">
              <img
                src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=100"
                alt="Carlos Méndez"
                class="person-av"
              />
              <div class="person-verify">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M5 13l4 4L19 7"
                  />
                </svg>
              </div>
            </div>
            <div class="person-info">
              <div class="person-name">Carlos Méndez</div>
              <p class="person-bio">
                Emprendedor y creador de contenido tecnológico
              </p>
            </div>
            <button class="btn-follow-mini">Seguir</button>
          </div>
        </div> -->

        <!-- Temas populares -->
        <div class="widget">
            <div class="widget-header">
                <h3 class="widget-title">Temas populares</h3>
                <a href="#" class="widget-link">Ver más →</a>
            </div>
            <div class="topics-grid">
                <span class="topic-pill">#Desarrollo</span>
                <span class="topic-pill">#UX</span>
                <span class="topic-pill">#Freelance</span>
                <span class="topic-pill">#JavaScript</span>
            </div>
        </div>

        <!-- Sobre Fluxa -->
        <div class="about-fluxa-card">
            <div class="about-header">
                <img
                    src="{{ asset('img/logoFluxa.png') }}"
                    alt="Fluxa Logo"
                    class="about-logo" />
                <h4>Sobre Fluxa</h4>
            </div>

            <p class="about-description">
                Fluxa es una red social enfocada en compartir proyectos,
                conocimiento y crecimiento profesional de forma segura y
                transparente.
            </p>

            <ul class="about-list">
                <li>✔ Perfiles verificados</li>
                <li>✔ Normas claras de comunidad</li>
                <li>✔ Protección de datos</li>
                <li>✔ Moderación responsable</li>
            </ul>

            <a href="{{ route('about-fluxa') }}" class="about-link"> Conocer más → </a>
        </div>
    </aside>
</div>

<!-- ══════════════════════════════════════════
     MODAL DE COMENTARIOS
══════════════════════════════════════════ -->
<div class="comments-modal" id="commentsModal">
    <div class="modal-content">
        <!-- Header -->
        <div class="modal-header">
            <h3 class="modal-title">Comentarios</h3>
            <button
                class="modal-close"
                id="closeCommentsModal"
                aria-label="Cerrar">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Original post -->
        <div class="modal-original-post">
            <div class="modal-post-header">
                <img src="" alt="" class="modal-post-avatar" id="modalPostAvatar" />
                <div class="modal-post-meta">
                    <div class="modal-post-author">
                        <span id="modalPostAuthor"></span>
                        <div class="verify-badge">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                    </div>
                    <div
                        class="modal-post-handle-time"
                        id="modalPostHandleTime"></div>
                </div>
            </div>
            <p class="modal-post-content" id="modalPostContent"></p>
        </div>

        <!-- Comments list -->
        <div class="modal-comments" id="modalCommentsList">
            <!-- Los comentarios se cargan dinámicamente aquí -->
        </div>

        <!-- Comment input -->
        <div class="modal-footer">
            <div class="comment-input-wrap">
                <img
                    src="{{ Auth::user()->profile->avatar ?? '' }}"
                    alt="Tú"
                    class="comment-input-avatar" />
                <div class="comment-input-form">
                    <textarea
                        class="comment-textarea"
                        id="commentTextarea"
                        placeholder="Escribe un comentario..."
                        rows="1"></textarea>
                    <div
                        class="comment-input-actions"
                        id="commentActions"
                        style="display: none">
                        <button class="btn-comment-cancel" id="btnCancelComment">
                            Cancelar
                        </button>
                        <button
                            class="btn-comment-submit"
                            id="btnSubmitComment"
                            disabled>
                            Comentar
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script src="{{ asset('js/modalComment.js') }}"></script>
<script>
    // Tab switching with AJAX
    document.querySelectorAll(".feed-tab").forEach((tab) => {
        tab.addEventListener("click", function() {
            document.querySelectorAll(".feed-tab").forEach((t) => t.classList.remove("active"));
            this.classList.add("active");

            const url = this.dataset.url;
            const container = document.getElementById("publications-container");

            fetch(url)
                .then(response => response.text())
                .then(html => {
                    container.innerHTML = html;
                })
                .catch(error => console.error("Error:", error));
        });
    });

    // Like button handling
    document.addEventListener("click", function(e) {
        const likeBtn = e.target.closest(".like-btn");
        if (likeBtn) {
            e.preventDefault();
            const projectId = likeBtn.dataset.projectId;
            
            fetch(`/projects/${projectId}/like`, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Content-Type": "application/json"
                }
            })
            .then(response => response.json())
            .then(data => {
                const countSpan = likeBtn.querySelector(".like-count");
                const svg = likeBtn.querySelector("svg");
                
                countSpan.textContent = data.likes_count;
                likeBtn.classList.toggle("liked");
                svg.setAttribute("fill", likeBtn.classList.contains("liked") ? "currentColor" : "none");
            })
            .catch(error => console.error("Error:", error));
        }
    });

    // Comment button handling
    document.addEventListener("click", function(e) {
        const commentBtn = e.target.closest(".comment-btn");
        if (commentBtn) {
            const projectId = commentBtn.dataset.projectId;
            // Open comments modal
            document.getElementById("commentsModal").classList.add("active");
            // Load comments via AJAX
        }
    });
</script>
@endpush
@push('styles')
<!--Estilo Personalizado de explorar-->
<link rel="stylesheet" href="{{ asset('css/explore.css') }}" />
@endpush