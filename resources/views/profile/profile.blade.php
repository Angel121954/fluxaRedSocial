@extends('layouts.app')

@section('content')
@include('components.topbar')
<!-- ══════════════════════════════════════════
     PROFILE HEADER
══════════════════════════════════════════ -->
<div class="profile-band">
    <div class="profile-inner">
        <div class="profile-row">
            <!-- Avatar -->
            <div class="avatar-wrap" id="avatarWrap">
                <img
                    src="{{ str_replace('type=normal', 'type=large', Auth::user()->avatar) }}"
                    alt="Lucas Silva"
                    class="avatar-img"
                    id="avatarImg" />
                <div class="avatar-overlay">
                    <button
                        class="av-btn"
                        id="btnCam"
                        aria-label="Cambiar foto"
                        title="Cambiar foto">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </button>
                    <button
                        class="av-btn"
                        id="btnView"
                        aria-label="Ver foto"
                        title="Ver foto completa">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Meta -->
            <div class="profile-meta">
                <div class="meta-top">
                    <div class="meta-left">
                        <div class="name-row">
                            <h1 class="profile-name">{{ Auth::user()->name }}</h1>
                            <span class="badge-pro">PRO</span>
                        </div>
                        <p class="handle">@ {{ Auth::user()->username }}</p>
                        <p class="bio">
                            Full stack developer construyendo en público.<br />
                            Apasionado por TypeScript, APIs y el arte de lanzar productos.
                        </p>
                        <a
                            href="https://linkt.cons/lucassilva"
                            class="profile-url"
                            target="_blank"
                            rel="noopener">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                            linkt.cons/lucassilva
                        </a>
                    </div>

                    <!-- Actions -->
                    <div class="meta-actions">
                        <button class="btn-follow" id="btnFollow">Seguir</button>
                        <div class="drop-wrap">
                            <button
                                class="btn-icon"
                                id="btnMore"
                                aria-label="Más opciones">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 5v.01M12 12v.01M12 19v.01" />
                                </svg>
                            </button>
                            <div class="drop-menu" id="dropMenu">
                                <!-- Perfil propio -->
                                <div id="ownOpts">
                                    <a href="{{ route('configuration.index') }}" class="drop-item">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437 1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008Z" />
                                        </svg>
                                        Configuración
                                    </a>
                                    <div class="drop-sep"></div>
                                    <!-- Blade Component o sección del botón -->
                                    <button
                                        class="drop-item"
                                        onclick="compartirPerfil('{{ Auth::user()->username }}', '{{ Auth::user()->name }}')">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                                        </svg>
                                        Compartir perfil
                                    </button>

                                    <!-- Toast de confirmación -->
                                    <div id="share-toast" class="share-toast hidden">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        <span id="share-toast-msg">¡Enlace copiado!</span>
                                    </div>
                                </div>
                                <!-- Perfil ajeno -->
                                <div id="otherOpts" style="display: none">
                                    <button class="drop-item">
                                        <svg
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                                        </svg>
                                        Compartir perfil
                                    </button>
                                    <button class="drop-item">
                                        <svg
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                        </svg>
                                        Bloquear
                                    </button>
                                    <div class="drop-sep"></div>
                                    <button class="drop-item danger">
                                        <svg
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                        Reportar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stats -->
                <div class="stats-row">
                    <div class="stat">
                        <span class="stat-n">3</span><span class="stat-l">Proyectos</span>
                    </div>
                    <div class="stat">
                        <span class="stat-n">53</span><span class="stat-l">Siguiendo</span>
                    </div>
                    <div class="stat">
                        <span class="stat-n">220</span><span class="stat-l">Seguidores</span>
                    </div>
                    <div class="stat">
                        <span class="stat-n">46</span><span class="stat-l">Días activo</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════
     TABS
══════════════════════════════════════════ -->
<div class="tabs-bar">
    <div class="tabs-inner">
        <div class="tab active" data-tab="progress">Progreso</div>
        <div class="tab" data-tab="projects">
            Proyectos <span class="tab-count">3</span>
        </div>
        <div class="tab" data-tab="stack">
            Stack <span class="tab-count">8</span>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════
     CONTENT
══════════════════════════════════════════ -->
<div class="content">
    <!-- Filter pills -->
    <div class="filter-row">
        <div class="f-pill active">
            <svg viewBox="0 0 24 24" fill="currentColor">
                <path
                    d="M14.23 12.004a2.236 2.236 0 0 1-2.235 2.236 2.236 2.236 0 0 1-2.236-2.236 2.236 2.236 0 0 1 2.235-2.236 2.236 2.236 0 0 1 2.236 2.236zm2.648-10.69c-1.346 0-3.107.96-4.888 2.622-1.78-1.653-3.542-2.602-4.887-2.602-.41 0-.783.093-1.106.278-1.375.793-1.683 3.264-.973 6.365C1.98 8.917 0 10.42 0 12.004c0 1.59 1.99 3.097 5.043 4.03-.704 3.113-.39 5.588.988 6.38.32.187.69.275 1.102.275 1.345 0 3.107-.96 4.888-2.624 1.78 1.654 3.542 2.603 4.887 2.603.41 0 .783-.09 1.106-.275 1.374-.792 1.683-3.263.973-6.365C22.02 15.096 24 13.59 24 12.004c0-1.59-1.99-3.097-5.043-4.032.704-3.11.39-5.587-.988-6.38-.318-.184-.688-.277-1.092-.278zm-.005 1.09v.006c.225 0 .406.044.558.127.666.382.955 1.835.73 3.704-.054.46-.142.945-.25 1.44-.96-.236-2.006-.417-3.107-.534-.66-.905-1.345-1.727-2.035-2.447 1.592-1.48 3.087-2.292 4.105-2.295zm-9.77.02c1.012 0 2.514.808 4.11 2.28-.686.72-1.37 1.537-2.02 2.442-1.107.117-2.154.298-3.113.538-.112-.49-.195-.964-.254-1.42-.23-1.868.054-3.32.714-3.707.19-.09.4-.127.563-.132zm4.882 3.05c.455.468.91.992 1.36 1.564-.44-.02-.89-.034-1.345-.034-.46 0-.915.01-1.36.034.44-.572.895-1.096 1.345-1.565zM12 8.1c.74 0 1.477.034 2.202.093.406.582.802 1.203 1.183 1.86.372.64.71 1.29 1.018 1.946-.308.655-.646 1.31-1.013 1.95-.38.66-.773 1.288-1.18 1.87-.728.063-1.466.098-2.21.098-.74 0-1.477-.035-2.202-.093-.406-.582-.802-1.204-1.183-1.86-.372-.64-.71-1.29-1.018-1.946.303-.657.646-1.313 1.013-1.954.38-.66.773-1.286 1.18-1.868.728-.064 1.466-.098 2.21-.098zm-3.635.254c-.24.377-.48.763-.704 1.16-.225.39-.435.782-.635 1.174-.265-.656-.49-1.31-.676-1.947.64-.15 1.315-.283 2.015-.386zm7.26 0c.695.103 1.365.23 2.006.387-.18.632-.405 1.282-.66 1.933-.2-.39-.41-.783-.64-1.174-.225-.392-.465-.774-.705-1.146zm3.063.675c.484.15.944.317 1.375.498 1.732.74 2.852 1.708 2.852 2.476-.005.768-1.125 1.74-2.857 2.475-.42.18-.88.342-1.355.493-.28-.958-.646-1.956-1.1-2.98.45-1.017.81-2.01 1.085-2.964zm-13.395.004c.278.96.645 1.957 1.1 2.98-.45 1.017-.812 2.01-1.086 2.964-.484-.15-.944-.318-1.37-.5-1.732-.737-2.852-1.706-2.852-2.474 0-.768 1.12-1.742 2.852-2.476.42-.18.88-.342 1.356-.494zm11.678 4.28c.265.657.49 1.312.676 1.948-.64.157-1.316.29-2.016.39.24-.375.48-.762.705-1.158.225-.39.435-.788.636-1.18zm-9.945.02c.2.392.41.783.64 1.175.23.39.465.772.705 1.143-.695-.102-1.365-.23-2.006-.386.18-.63.406-1.282.66-1.933z" />
            </svg>
            React
        </div>
        <div class="f-pill">
            <svg viewBox="0 0 24 24" fill="currentColor">
                <path
                    d="M11.572 0c-.176 0-.31.001-.358.007a19.76 19.76 0 0 1-.364.033C7.443.346 4.25 2.185 2.228 5.012a11.875 11.875 0 0 0-2.119 5.243c-.096.659-.108.854-.108 1.747s.012 1.089.108 1.748c.652 4.506 3.86 8.292 8.209 9.695.779.25 1.6.422 2.534.525.363.04 1.935.04 2.299 0 1.611-.178 2.977-.577 4.323-1.264.207-.106.247-.134.219-.158-.02-.013-.9-1.193-1.955-2.62l-1.919-2.592-2.404-3.558a338.739 338.739 0 0 0-2.422-3.556c-.009-.002-.018 1.579-.023 3.51-.007 3.38-.01 3.515-.052 3.595a.426.426 0 0 1-.206.214c-.075.037-.14.044-.495.044H7.81l-.108-.068a.438.438 0 0 1-.157-.171l-.049-.106.005-4.703.007-4.705.072-.092a.645.645 0 0 1 .174-.143c.096-.047.134-.051.54-.051.478 0 .558.018.682.154.035.038 1.337 1.999 2.895 4.361a10760.433 10760.433 0 0 0 4.735 7.17l1.9 2.879.096-.063a12.317 12.317 0 0 0 2.466-2.163 11.944 11.944 0 0 0 2.824-6.134c.096-.66.108-.854.108-1.748 0-.893-.012-1.088-.108-1.747-.652-4.506-3.859-8.292-8.208-9.695a12.597 12.597 0 0 0-2.499-.523A33.119 33.119 0 0 0 11.573 0zm4.069 7.217c.347 0 .408.005.486.047a.473.473 0 0 1 .237.277c.018.06.023 1.365.018 4.304l-.006 4.218-.744-1.14-.746-1.14v-3.066c0-1.982.01-3.097.023-3.15a.478.478 0 0 1 .233-.296c.096-.05.13-.054.5-.054z" />
            </svg>
            Next.js
        </div>
        <div class="f-pill">
            <svg viewBox="0 0 24 24" fill="#68A063">
                <path
                    d="M12 1.85c-.27 0-.55.07-.78.2l-7.44 4.3c-.48.28-.78.8-.78 1.36v8.58c0 .56.3 1.08.78 1.36l1.95 1.12c.95.46 1.27.47 1.71.47 1.4 0 2.21-.85 2.21-2.33V8.44c0-.12-.1-.22-.22-.22H8.5c-.13 0-.23.1-.23.22v8.47c0 .66-.68 1.31-1.77.76L4.45 16.5a.26.26 0 0 1-.11-.21V7.71c0-.09.04-.17.11-.21l7.44-4.29c.06-.04.16-.04.22 0l7.44 4.29c.07.04.11.12.11.21v8.58c0 .08-.04.16-.11.21l-7.44 4.29c-.06.04-.16.04-.23 0L9.9 19.75c-.07-.03-.15-.03-.21-.01-.53.3-.63.36-1.12.51-.12.04-.31.11.07.32l2.48 1.47c.24.14.5.21.77.21s.54-.07.78-.21l7.44-4.29c.48-.28.78-.8.78-1.36V7.71c0-.56-.3-1.08-.78-1.36l-7.44-4.3c-.23-.13-.5-.2-.78-.2z" />
            </svg>
            Node.js
        </div>
    </div>

    <!-- ──────────── PROJECT 1 ──────────── -->
    <div class="p-card">
        <div class="card-head">
            <div>
                <h3 class="card-title">Fluxa</h3>
                <div class="card-badges">
                    <span class="badge-day">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2.5"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        DÍA 46
                    </span>
                    <span class="badge-streak">🔥 12d racha</span>
                </div>
            </div>
            <button class="card-menu" aria-label="Opciones del proyecto">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 5v.01M12 12v.01M12 19v.01" />
                </svg>
            </button>
        </div>

        <p class="card-desc">
            Plataforma para builders que construyen en público. Documentación de
            progreso, stack técnico y comunidad de desarrolladores con foco en
            transparencia.
        </p>

        <div class="progress-block">
            <div class="progress-hd">
                <span class="progress-label">PROGRESO</span>
                <span class="progress-pct">65%</span>
            </div>
            <div class="progress-track">
                <div class="progress-fill" style="width: 65%"></div>
            </div>
            <div class="progress-meta">
                <span class="pmeta">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <strong>18/28</strong>&nbsp;tareas
                </span>
                <span class="pmeta">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Est.&nbsp;<strong>14 días</strong>
                </span>
            </div>
        </div>

        <div class="tech-row">
            <span class="t-tag t-laravel">
                <svg viewBox="0 0 24 24" width="11" height="11" fill="currentColor">
                    <path
                        d="M23.642 5.43a.364.364 0 01.014.1v5.149c0 .135-.073.26-.189.326l-4.323 2.49v4.934a.378.378 0 01-.188.326L9.93 23.949a.316.316 0 01-.066.027c-.008.002-.016.008-.024.01a.348.348 0 01-.192 0c-.011-.002-.02-.008-.03-.012-.02-.008-.042-.014-.062-.025L.533 18.755a.376.376 0 01-.189-.326V2.974c0-.033.005-.066.014-.098.003-.012.01-.02.014-.032a.369.369 0 01.023-.058c.004-.013.015-.022.023-.033l.033-.045c.012-.01.025-.018.037-.027.014-.012.027-.024.041-.034H.53L5.043.05a.375.375 0 01.375 0L9.93 2.647h.002c.015.01.027.021.04.033l.038.027c.013.014.02.03.033.045.008.011.02.021.025.033.01.02.017.038.024.058.003.011.01.021.013.032.01.031.014.064.014.098v9.652l3.76-2.164V5.527c0-.033.004-.066.013-.098.003-.01.01-.02.013-.032a.487.487 0 01.024-.059c.007-.012.018-.02.025-.033.012-.015.021-.03.033-.043.012-.012.025-.02.037-.028.014-.01.026-.023.041-.032h.001l4.513-2.598a.375.375 0 01.375 0l4.513 2.598c.016.01.027.021.042.031.012.01.025.018.036.028.013.014.022.03.034.044.008.012.019.021.024.033.011.02.018.04.024.06.006.01.012.021.015.032z" />
                </svg>
                Laravel
            </span>
            <span class="t-tag t-next">
                <svg viewBox="0 0 24 24" width="11" height="11" fill="currentColor">
                    <path
                        d="M11.572 0c-.176 0-.31.001-.358.007a19.76 19.76 0 0 1-.364.033C7.443.346 4.25 2.185 2.228 5.012a11.875 11.875 0 0 0-2.119 5.243c-.096.659-.108.854-.108 1.747s.012 1.089.108 1.748c.652 4.506 3.86 8.292 8.209 9.695.779.25 1.6.422 2.534.525.363.04 1.935.04 2.299 0 1.611-.178 2.977-.577 4.323-1.264.207-.106.247-.134.219-.158-.02-.013-.9-1.193-1.955-2.62l-1.919-2.592-2.404-3.558a338.739 338.739 0 0 0-2.422-3.556c-.009-.002-.018 1.579-.023 3.51-.007 3.38-.01 3.515-.052 3.595a.426.426 0 0 1-.206.214c-.075.037-.14.044-.495.044H7.81l-.108-.068a.438.438 0 0 1-.157-.171l-.049-.106.005-4.703.007-4.705.072-.092a.645.645 0 0 1 .174-.143c.096-.047.134-.051.54-.051.478 0 .558.018.682.154.035.038 1.337 1.999 2.895 4.361a10760.433 10760.433 0 0 0 4.735 7.17l1.9 2.879.096-.063a12.317 12.317 0 0 0 2.466-2.163 11.944 11.944 0 0 0 2.824-6.134c.096-.66.108-.854.108-1.748 0-.893-.012-1.088-.108-1.747-.652-4.506-3.859-8.292-8.208-9.695a12.597 12.597 0 0 0-2.499-.523A33.119 33.119 0 0 0 11.573 0zm4.069 7.217c.347 0 .408.005.486.047a.473.473 0 0 1 .237.277c.018.06.023 1.365.018 4.304l-.006 4.218-.744-1.14-.746-1.14v-3.066c0-1.982.01-3.097.023-3.15a.478.478 0 0 1 .233-.296c.096-.05.13-.054.5-.054z" />
                </svg>
                Next.js
            </span>
            <span class="t-tag t-supa">
                <svg viewBox="0 0 24 24" width="11" height="11" fill="currentColor">
                    <path
                        d="M12.443.004a10.572 10.572 0 00-10.572 10.572v2.614c0 .35.155.68.424.902l8.022 6.617a1.06 1.06 0 001.65-.877v-8.157l-5.834-4.81a.794.794 0 01.509-1.41h5.325a6.914 6.914 0 016.914 6.914v9.614a1.06 1.06 0 01-1.65.877l-8.022-6.617v-6.914z" />
                </svg>
                Supabase
            </span>
        </div>

        <div class="update-block">
            <img
                src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100"
                alt="Lucas"
                class="upd-av" />
            <div class="upd-body">
                <p class="upd-text">
                    MVP terminado y en producción. Primer milestone completado,
                    iterando con feedback real de usuarios.
                </p>
                <div class="upd-foot">
                    <span class="upd-time">Hace 1 día</span>
                    <div class="upd-actions">
                        <span class="upd-act">
                            <svg fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                            </svg>
                            12
                        </span>
                        <span class="upd-act">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            5
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ──────────── PROJECT 2 ──────────── -->
    <div class="p-card">
        <div class="card-head">
            <div>
                <h3 class="card-title">API-Boost</h3>
                <div class="card-badges">
                    <span class="badge-day">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2.5"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        DÍA 28
                    </span>
                    <span class="badge-streak">🔥 8d racha</span>
                </div>
            </div>
            <button class="card-menu" aria-label="Opciones del proyecto">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 5v.01M12 12v.01M12 19v.01" />
                </svg>
            </button>
        </div>

        <p class="card-desc">
            Servicio de caché inteligente y monitoring en tiempo real para
            optimizar APIs REST a escala. Reducción de latencia documentada en
            producción.
        </p>

        <div class="progress-block">
            <div class="progress-hd">
                <span class="progress-label">PROGRESO</span>
                <span class="progress-pct">45%</span>
            </div>
            <div class="progress-track">
                <div class="progress-fill" style="width: 45%"></div>
            </div>
            <div class="progress-meta">
                <span class="pmeta">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <strong>9/20</strong>&nbsp;tareas
                </span>
                <span class="pmeta">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Est.&nbsp;<strong>22 días</strong>
                </span>
            </div>
        </div>

        <div class="tech-row">
            <span class="t-tag t-node">
                <svg viewBox="0 0 24 24" width="11" height="11" fill="#68A063">
                    <path
                        d="M12 1.85c-.27 0-.55.07-.78.2l-7.44 4.3c-.48.28-.78.8-.78 1.36v8.58c0 .56.3 1.08.78 1.36l1.95 1.12c.95.46 1.27.47 1.71.47 1.4 0 2.21-.85 2.21-2.33V8.44c0-.12-.1-.22-.22-.22H8.5c-.13 0-.23.1-.23.22v8.47c0 .66-.68 1.31-1.77.76L4.45 16.5a.26.26 0 0 1-.11-.21V7.71c0-.09.04-.17.11-.21l7.44-4.29c.06-.04.16-.04.22 0l7.44 4.29c.07.04.11.12.11.21v8.58c0 .08-.04.16-.11.21l-7.44 4.29c-.06.04-.16.04-.23 0L9.9 19.75c-.07-.03-.15-.03-.21-.01-.53.3-.63.36-1.12.51-.12.04-.31.11.07.32l2.48 1.47c.24.14.5.21.77.21s.54-.07.78-.21l7.44-4.29c.48-.28.78-.8.78-1.36V7.71c0-.56-.3-1.08-.78-1.36l-7.44-4.3c-.23-.13-.5-.2-.78-.2z" />
                </svg>
                Node.js
            </span>
            <span class="t-tag t-ts">
                <svg viewBox="0 0 24 24" width="11" height="11" fill="#3178C6">
                    <path
                        d="M0 12v12h24V0H0zm19.341-.956c.61.152 1.074.423 1.501.865.221.236.549.666.575.77.008.03-1.036.73-1.668 1.123-.023.015-.115-.084-.217-.236-.31-.45-.633-.644-1.128-.678-.728-.05-1.196.331-1.192.967a.88.88 0 00.102.45c.16.331.458.53 1.39.933 1.719.74 2.454 1.227 2.911 1.92.51.773.625 2.008.278 2.926-.38.998-1.325 1.676-2.655 1.9-.411.073-1.386.062-1.828-.018-.964-.172-1.878-.648-2.442-1.273-.221-.244-.651-.88-.625-.925.011-.016.11-.077.22-.141.108-.061.511-.294.892-.515l.69-.4.145.214c.202.308.643.731.91.872.766.404 1.817.347 2.335-.118a.883.883 0 00.313-.72c0-.278-.035-.4-.18-.61-.186-.266-.567-.49-1.65-.96-1.238-.535-1.767-.873-2.194-1.402a3.165 3.165 0 01-.614-1.567 3.296 3.296 0 01.002-.415c.187-1.444 1.183-2.388 2.726-2.583.409-.05 1.36-.01 1.754.074zm-5.341 1.023v1.062H11.05v6.815H9.025v-6.815H6v-1.075c0-.593.01-1.08.022-1.083.012-.004 1.778-.005 3.924-.004h3.904z" />
                </svg>
                TypeScript
            </span>
            <span class="t-tag t-fast">
                <svg viewBox="0 0 24 24" width="11" height="11" fill="white">
                    <path
                        d="M23.245 6.49L13.967.524a1.99 1.99 0 00-1.934 0L2.755 6.49a1.99 1.99 0 00-.991 1.723v11.954a1.99 1.99 0 00.991 1.723l9.278 5.966a1.99 1.99 0 001.934 0l9.278-5.966a1.99 1.99 0 00.991-1.723V8.213a1.99 1.99 0 00-.991-1.723z" />
                </svg>
                Fastify
            </span>
        </div>

        <div class="update-block">
            <img
                src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100"
                alt="Lucas"
                class="upd-av" />
            <div class="upd-body">
                <p class="upd-text">
                    Construyendo el dashboard de monitoreo en tiempo real.
                    Arquitectura definida, implementando la capa de datos.
                </p>
                <div class="upd-foot">
                    <span class="upd-time">Hace 8 días</span>
                    <div class="upd-actions">
                        <span class="upd-act">
                            <svg fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                            </svg>
                            4
                        </span>
                        <span class="upd-act">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            7
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- end content -->

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
            src="{{ Auth::user()->avatar }}"
            id="modalImg"
            alt="Foto de perfil" />
    </div>
</div>

<input type="file" id="fileIn" accept="image/*" style="display: none" />
@endsection

@push('scripts')
<script>
    /* ── Config ──────────────────────────────────────── */
    const IS_OWN = true;

    /* ── Refs ───────────────────────────────────────── */
    const avatarWrap = document.getElementById("avatarWrap");
    const avatarImg = document.getElementById("avatarImg");
    const btnCam = document.getElementById("btnCam");
    const btnView = document.getElementById("btnView");
    const fileIn = document.getElementById("fileIn");
    const imgModal = document.getElementById("imgModal");
    const modalImg = document.getElementById("modalImg");
    const modalX = document.getElementById("modalX");
    const btnMore = document.getElementById("btnMore");
    const dropMenu = document.getElementById("dropMenu");
    const ownOpts = document.getElementById("ownOpts");
    const otherOpts = document.getElementById("otherOpts");
    const btnFollow = document.getElementById("btnFollow");

    /* ── Dropdown según tipo de perfil ──────────────── */
    if (ownOpts) ownOpts.style.display = IS_OWN ? "" : "none";
    if (otherOpts) otherOpts.style.display = IS_OWN ? "none" : "";
    if (btnFollow) btnFollow.style.display = IS_OWN ? "none" : "";

    if (btnMore && dropMenu) {
        btnMore.addEventListener("click", (e) => {
            e.stopPropagation();
            const isOpen = dropMenu.classList.toggle("open");
            btnMore.classList.toggle("is-open", isOpen);
        });
    }

    document.addEventListener("click", (e) => {
        if (dropMenu && btnMore && !dropMenu.contains(e.target) && e.target !== btnMore) {
            dropMenu.classList.remove("open");
            btnMore.classList.remove("is-open");
        }
    });

    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape" && dropMenu) {
            dropMenu.classList.remove("open");
            btnMore?.classList.remove("is-open");
        }
    });

    /* ── Seguir toggle ──────────────────────────────── */
    if (btnFollow) {
        btnFollow.addEventListener("click", () => {
            const following = btnFollow.classList.toggle("is-following");
            btnFollow.textContent = following ? "Siguiendo" : "Seguir";
        });
    }

    /* ── Avatar: móvil touch toggle ─────────────────── */
    const isMobile = () => window.innerWidth <= 680;

    if (avatarWrap && avatarImg) {
        avatarWrap.addEventListener("click", (e) => {
            if (isMobile() && (e.target === avatarWrap || e.target === avatarImg)) {
                avatarWrap.classList.toggle("active");
            }
        });
        document.addEventListener("click", (e) => {
            if (isMobile() && !avatarWrap.contains(e.target))
                avatarWrap.classList.remove("active");
        });
    }

    /* ── Cambiar foto ───────────────────────────────── */
    if (btnCam && fileIn) {
        btnCam.addEventListener("click", (e) => {
            e.stopPropagation();
            avatarWrap?.classList.remove("active");
            fileIn.click();
        });

        fileIn.addEventListener("change", (e) => {
            const f = e.target.files[0];
            if (!f) return;
            const r = new FileReader();
            r.onload = (ev) => {
                if (avatarImg) avatarImg.src = ev.target.result;
                if (modalImg) modalImg.src = ev.target.result;
            };
            r.readAsDataURL(f);
        });
    }

    /* ── Ver imagen ─────────────────────────────────── */
    const closeModal = () => {
        imgModal?.classList.remove("show");
        document.body.style.overflow = "";
    };

    if (btnView && imgModal) {
        btnView.addEventListener("click", (e) => {
            e.stopPropagation();
            avatarWrap?.classList.remove("active");
            imgModal.classList.add("show");
            document.body.style.overflow = "hidden";
        });
        imgModal.addEventListener("click", (e) => {
            if (e.target === imgModal) closeModal();
        });
    }

    if (modalX) modalX.addEventListener("click", closeModal);

    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape") closeModal();
    });

    /* ── Tabs ───────────────────────────────────────── */
    document.querySelectorAll(".tab").forEach((t) => {
        t.addEventListener("click", () => {
            document.querySelectorAll(".tab").forEach((x) => x.classList.remove("active"));
            t.classList.add("active");
        });
    });

    /* ── Filter pills ───────────────────────────────── */
    document.querySelectorAll(".f-pill").forEach((p) => {
        p.addEventListener("click", () => {
            document.querySelectorAll(".f-pill").forEach((x) => x.classList.remove("active"));
            p.classList.add("active");
        });
    });
</script>
<script src="{{ asset('js/shareProfile.js') }}"></script>
@endpush
@push('styles')
<!--Estilo Personalizado de perfil-->
<link rel="stylesheet" href="{{ asset('css/profile.css') }}" />
@endpush