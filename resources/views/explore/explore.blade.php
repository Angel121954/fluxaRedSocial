@extends('layouts.app')
@section('content')
@section('title', 'Explorar')
@include('components.topbar')

<!-- ══════════════════════════════════════════
     FEED LAYOUT
══════════════════════════════════════════ -->
<div class="feed-layout">
    <!-- ──── FEED COLUMN ──── -->
    <div class="feed-main">
        <!-- Tabs -->
        <div class="feed-tabs">
            <button class="feed-tab active">🔥 Tendencias</button>
            <button class="feed-tab">Recientes</button>
            <button class="feed-tab">Siguiendo</button>
        </div>

        <!-- Post 1 -->
        <div class="post-card">
            <div style="display: flex; justify-content: space-between">
                <div class="post-header">
                    <img
                        src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=100"
                        alt="Angela Torres"
                        class="post-avatar" />
                    <div class="post-meta">
                        <div class="post-author-row">
                            <span class="post-author">Angela Torres</span>
                            <div class="verify-badge">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.375rem">
                            <span class="post-handle">@angelatorres</span>
                            <span style="color: var(--ink-200)">·</span>
                            <span class="post-time">Hace 2 horas</span>
                        </div>
                    </div>
                </div>
                <button class="post-menu">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 5v.01M12 12v.01M12 19v.01" />
                    </svg>
                </button>
            </div>

            <p class="post-content">
                Nueva aplicación lista y funcionando con Laravel y React! Ya
                disponible para testing. Feedback bienvenido 🚀
            </p>

            <div class="post-tags">
                <span class="post-tag">Laravel</span>
            </div>

            <div class="post-actions">
                <button class="post-action">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                    320
                </button>
                <button class="post-action">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    56
                </button>
            </div>
        </div>

        <!-- Post 2 -->
        <div class="post-card">
            <div style="display: flex; justify-content: space-between">
                <div class="post-header">
                    <img
                        src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=100"
                        alt="Diego Morales"
                        class="post-avatar" />
                    <div class="post-meta">
                        <div class="post-author-row">
                            <span class="post-author">Diego Morales</span>
                            <div class="verify-badge">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.375rem">
                            <span class="post-handle">@diegomorales</span>
                            <span style="color: var(--ink-200)">·</span>
                            <span class="post-time">Hace 3 horas</span>
                        </div>
                    </div>
                </div>
                <button class="post-menu">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 5v.01M12 12v.01M12 19v.01" />
                    </svg>
                </button>
            </div>

            <p class="post-content">
                5 consejos para mejorar tu productividad como desarrollador 💡
            </p>

            <div class="post-tags">
                <span class="post-tag">Productividad</span>
            </div>

            <div class="post-actions">
                <button class="post-action liked">
                    <svg fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                    </svg>
                    275
                </button>
                <button class="post-action">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    42
                </button>
            </div>
        </div>

        <!-- Post 3 con imagen -->
        <div class="post-card">
            <div style="display: flex; justify-content: space-between">
                <div class="post-header">
                    <img
                        src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=100"
                        alt="Andrea Pérez"
                        class="post-avatar" />
                    <div class="post-meta">
                        <div class="post-author-row">
                            <span class="post-author">Andrea Pérez</span>
                            <div class="verify-badge">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.375rem">
                            <span class="post-handle">@andreaperez</span>
                            <span style="color: var(--ink-200)">·</span>
                            <span class="post-time">Hace 1 hora</span>
                        </div>
                    </div>
                </div>
                <button class="post-menu">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 5v.01M12 12v.01M12 19v.01" />
                    </svg>
                </button>
            </div>

            <p class="post-content">
                Estoy probando una nueva herramienta de diseño UX en Figma, ¡está
                increíble!
            </p>

            <img
                src="https://images.unsplash.com/photo-1586717791821-3f44a563fa4c?w=600&h=300&fit=crop"
                alt="Design preview"
                class="post-image" />

            <div class="post-tags">
                <span class="post-tag">#UI/UX</span>
                <span class="post-tag">Figma</span>
            </div>

            <div class="post-actions">
                <button class="post-action">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                    250
                </button>
                <button class="post-action">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    38
                </button>
            </div>
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
    // Tab switching
    document.querySelectorAll(".feed-tab").forEach((tab) => {
        tab.addEventListener("click", () => {
            document
                .querySelectorAll(".feed-tab")
                .forEach((t) => t.classList.remove("active"));
            tab.classList.add("active");
        });
    });

    // Like actions
    document.querySelectorAll(".post-action").forEach((btn) => {
        if (btn.querySelector('path[d*="M4.318"]')) {
            btn.addEventListener("click", function() {
                this.classList.toggle("liked");
                const svg = this.querySelector("svg");
                const count = parseInt(this.textContent.trim());
                this.innerHTML =
                    svg.outerHTML +
                    " " +
                    (this.classList.contains("liked") ? count + 1 : count - 1);
            });
        }
    });

    // Follow buttons
    document.querySelectorAll(".btn-follow-mini").forEach((btn) => {
        btn.addEventListener("click", (e) => {
            e.stopPropagation();
            btn.textContent =
                btn.textContent === "Seguir" ? "Siguiendo" : "Seguir";
            btn.style.background =
                btn.textContent === "Siguiendo" ? "var(--ink-100)" : "";
            btn.style.color =
                btn.textContent === "Siguiendo" ? "var(--ink-700)" : "";
        });
    });
</script>
@endpush
@push('styles')
<!--Estilo Personalizado de explorar-->
<link rel="stylesheet" href="{{ asset('css/explore.css') }}" />
@endpush