@extends('layouts.app')
@section('content')
@include('components.topbar')

<div class="notif-page">
    <div class="notif-wrapper">

        <!-- ── Top bar ── -->
        <div class="notif-topbar">
            <div class="filter-tabs">
                <button class="filter-tab active">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path d="M9 12l2 2 4-4" />
                        <circle cx="12" cy="12" r="9" />
                    </svg>
                    Todas
                </button>
                <button class="filter-tab">Menciones</button>
                <button class="filter-tab">Comentarios</button>
                <button class="filter-tab">Seguidos</button>
            </div>
            <div class="topbar-right">
                <button class="btn-mark-read">Marcar todo como leído</button>
                <button class="btn-settings">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="3" />
                        <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- ── Notification list ── -->
        <div class="notif-list">

            <!-- 1 · Like -->
            <a href="">
                <div class="notif-card">
                    <div class="notif-header">
                        <div class="notif-user">
                            <div class="avatar av-jg">JG</div>
                            <div class="notif-user-info">
                                <div class="notif-name-line">
                                    <strong>Julia Garcia</strong><span class="verified-badge"><svg viewBox="0 0 10 10" fill="none">
                                            <path d="M2 5l2 2 4-4" stroke="#fff" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg></span> le gustó tu publicación
                                </div>
                                <div class="notif-meta">@juliagarcia · Hace 2h</div>
                            </div>
                        </div>
                        <div class="notif-time">Ahora 2h <span class="more-btn">···</span></div>
                    </div>
                    <div class="inner-preview">
                        <div class="post-preview-row">
                            <div class="avatar-sm av-dm">DM</div>
                            <span class="post-preview-title">5 consejos para mejorar tu productividad como desarrollador 💡</span>
                            <span class="badge badge-accent">+5 Nuevo ✓</span>
                        </div>
                        <div class="post-preview-stats">
                            <span class="stat"><span class="stat-heart">♥</span> 280</span>
                            <span class="stat">💬 280 Me gusta</span>
                            <span>· Hace 2h</span>
                        </div>
                    </div>
                </div>
            </a>

            <!-- 2 · Comment -->
            <div class="notif-card">
                <div class="notif-header">
                    <div class="notif-user">
                        <div class="avatar av-at">AT</div>
                        <div class="notif-user-info">
                            <div class="notif-name-line">
                                <strong>Angela Torres</strong><span class="verified-badge"><svg viewBox="0 0 10 10" fill="none">
                                        <path d="M2 5l2 2 4-4" stroke="#fff" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg></span> comentó en tu publicación
                            </div>
                            <div class="notif-meta">@angelatorres · Hace 3h</div>
                        </div>
                    </div>
                    <div class="notif-time">Hace 3h <span class="more-btn">···</span></div>
                </div>
                <div class="inner-preview">
                    <div class="comment-row">
                        <div class="avatar-sm av-at">AT</div>
                        <p class="comment-text">Genial! Me va a ser muy útil, gracias por compartir estos consejos 😊!</p>
                    </div>
                    <div class="comment-actions">
                        <button class="btn-cmt secondary">Seguir a él</button>
                        <button class="btn-cmt primary">Diego Morales</button>
                    </div>
                </div>
            </div>

            <!-- 3 · Mention + Follow -->
            <div class="notif-card">
                <div class="notif-header">
                    <div class="notif-user">
                        <div class="avatar av-cm">CM</div>
                        <div class="notif-user-info">
                            <div class="notif-name-line">
                                <strong>Carlos Mendez</strong><span class="verified-badge"><svg viewBox="0 0 10 10" fill="none">
                                        <path d="M2 5l2 2 4-4" stroke="#fff" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg></span> te mencionó en una publicación: "He seguido el tutorial por...
                            </div>
                            <div class="notif-meta">@carlosmendez · Hace 3h</div>
                        </div>
                    </div>
                    <div class="notif-time">Hace 5h <span class="more-btn">···</span></div>
                </div>
                <div class="inner-preview">
                    <div class="follow-row">
                        <div class="follow-info">
                            <div class="avatar-sm av-ap">AP</div>
                            <strong>Andrea Pérez</strong> te ha comenzado a seguir
                            <div class="avatar-stack">
                                <div class="avatar-sm av-dm">DM</div>
                                <div class="avatar-sm av-cm">++</div>
                            </div>
                            Diego Morales · ++ +2
                        </div>
                        <span class="badge badge-accent">+ Nuevo: 2</span>
                    </div>
                </div>
            </div>

            <!-- 4 · Multi-comment -->
            <div class="notif-card">
                <div class="notif-header">
                    <div class="notif-user">
                        <div style="display:flex;margin-right:6px;">
                            <div class="avatar av-jg" style="z-index:2;">JG</div>
                            <div class="avatar av-nd" style="width:34px;height:34px;font-size:11px;margin-left:-10px;border:2.5px solid var(--surface);border-radius:50%;z-index:1;">ND</div>
                        </div>
                        <div class="notif-user-info">
                            <div class="notif-name-line">
                                <strong>Julia Garcia y Nicólas Díaz</strong> comentaron en tu publicación:
                            </div>
                            <div class="notif-meta">
                                <div class="avatar-stack">
                                    <div class="avatar-sm av-jg">JG</div>
                                    <div class="avatar-sm av-nd">ND</div>
                                    <div class="avatar-sm av-dm">DM</div>
                                </div>
                                Diego Morales · Hace 10h
                            </div>
                        </div>
                    </div>
                    <div class="notif-time">Hace 10h <span class="more-btn">···</span></div>
                </div>
                <div class="inner-preview">
                    <div class="multi-title">
                        <span>Julia Garcia y Nicólas Díaz comentaron en tu publicación:</span>
                        <div class="multi-badges">
                            <span style="font-size:.8rem;color:var(--ink-300);font-weight:500;">+2</span>
                            <span class="badge badge-accent">Nuevo: 2</span>
                        </div>
                    </div>
                    <p class="multi-body">Estoy probando una nueva herramienta de diseño UX en Figma, ya, está increíble!</p>
                    <div class="multi-footer">
                        <div class="avatar-stack">
                            <div class="avatar-sm av-jg">JG</div>
                            <div class="avatar-sm av-nd">ND</div>
                            <div class="avatar-sm av-dm">DM</div>
                        </div>
                        Diego Morales · Hace 10h
                    </div>
                </div>
            </div>

        </div><!-- /notif-list -->
    </div>
</div>
@endsection

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/notifications.css') }}">
@endpush