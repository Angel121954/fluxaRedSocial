@extends('admin.layouts.admin')

@section('title', 'Usuarios — Admin Fluxa')

@push('styles')
{{-- DataTables CSS --}}
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
@vite('resources/css/shared/modal.css')
@vite('resources/css/admin/users.css')
@endpush

@section('admin-content')

{{-- ─── Page header ────────────────────────────────────── --}}
<div class="page-header">
    <div>
        <h1 class="page-title">Usuarios</h1>
        <p class="page-sub">Gestiona los usuarios registrados en Fluxa.</p>
    </div>

    <div class="adm-header-actions">
        {{-- Otorgar insignia Beta Tester --}}
        <button
            type="button"
            class="adm-btn adm-btn--accent"
            id="openBadgeModal"
            aria-haspopup="dialog">
            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
            </svg>
            Otorgar insignia Beta
        </button>

        {{-- Exportar --}}
        <!-- <button type="button" class="adm-btn adm-btn--secondary" id="exportCsv" title="Exportar como CSV">
            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
            Exportar
        </button> -->
    </div>
</div>

{{-- ─── Card principal ─────────────────────────────────── --}}
<div class="adm-card">

    {{-- ── Toolbar ── --}}
    <div class="adm-toolbar">
        {{-- Search (manejado por DataTables, este input es decorativo para layout) --}}
        <div class="adm-search-wrap">
            <svg class="adm-search-icon" width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input
                type="search"
                id="usersSearch"
                class="adm-search"
                placeholder="Buscar usuario, email..."
                aria-label="Buscar usuarios">
        </div>

        {{-- Filtros --}}
        <div class="adm-filters">

            {{-- Rol --}}
            <div class="adm-select-wrap">
                <select id="filterRole" class="adm-select" aria-label="Filtrar por rol">
                    <option value="">Todos los roles</option>
                    <option value="developer">Desarrollador</option>
                    <option value="designer">Diseñador</option>
                    <option value="student">Estudiante</option>
                    <option value="recruiter">Reclutador</option>
                </select>
                <svg class="adm-select-chevron" width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                </svg>
            </div>

            {{-- Estado --}}
            <div class="adm-select-wrap">
                <select id="filterStatus" class="adm-select" aria-label="Filtrar por estado">
                    <option value="">Todos los estados</option>
                    <option value="active">Activo</option>
                    <option value="banned">Baneado</option>
                </select>
                <svg class="adm-select-chevron" width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                </svg>
            </div>

            {{-- Verificación --}}
            <div class="adm-select-wrap">
                <select id="filterVerified" class="adm-select" aria-label="Filtrar por verificación">
                    <option value="">Verificación</option>
                    <option value="1">Verificados</option>
                    <option value="0">No verificados</option>
                </select>
                <svg class="adm-select-chevron" width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                </svg>
            </div>

            {{-- Limpiar filtros --}}
            <button type="button" class="adm-btn-icon" id="clearFilters" title="Limpiar filtros" aria-label="Limpiar filtros">
                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    {{-- ── Tabla de usuarios ── --}}
    <div class="adm-table-wrap">
        <table id="usersTable" class="adm-table {{ $users->isEmpty() ? 'adm-table--empty' : '' }}" role="table" aria-label="Tabla de usuarios">
            <thead>
                <tr>
                    <th class="adm-th" scope="col">Usuario</th>
                    <th class="adm-th" scope="col">Email</th>
                    <th class="adm-th adm-th--center" scope="col">Rol</th>
                    <th class="adm-th adm-th--center" scope="col">Estado</th>
                    <th class="adm-th adm-th--center" scope="col">Verificado</th>
                    <th class="adm-th adm-th--center" scope="col">Publicaciones</th>
                    <th class="adm-th adm-th--right" scope="col">Registro</th>
                    <th class="adm-th adm-th--center" scope="col" data-orderable="false">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr class="adm-tr {{ $user->is_banned ? 'adm-tr--banned' : '' }}"
                    data-user-id="{{ $user->id }}"
                    data-status="{{ $user->is_banned ? 'banned' : 'active' }}"
                    data-role="{{ $user->role ?? '' }}"
                    data-verified="{{ $user->is_verified ? '1' : '0' }}">

                    {{-- Usuario --}}
                    <td class="adm-td">
                        <div class="adm-user">
                            <div class="adm-avatar-wrap">
                                @if($user->avatar_url)
                                <img src="{{ $user->avatar_url }}"
                                    alt="{{ $user->name }}"
                                    class="adm-avatar"
                                    loading="lazy">
                                @else
                                <div class="adm-avatar adm-avatar--initials" aria-label="{{ $user->name }}">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                                @endif
                                @if($user->is_banned)
                                <span class="adm-avatar-banned-dot" title="Usuario baneado" aria-label="Usuario baneado"></span>
                                @endif
                            </div>
                            <div class="adm-user-info">
                                <span class="adm-user-name">{{ $user->name }}</span>
                                <span class="adm-user-handle">{{ '@' . $user->username }}</span>
                            </div>
                            @if($user->hasBadge('beta-tester'))
                            <span class="adm-badge-pill adm-badge-pill--beta" title="Beta Tester">
                                <svg width="10" height="10" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Beta
                            </span>
                            @endif
                        </div>
                    </td>

                    {{-- Email --}}
                    <td class="adm-td">
                        <span class="adm-email">{{ $user->email }}</span>
                    </td>

                    {{-- Rol --}}
                    <td class="adm-td adm-td--center">
                        @if($user->role)
                        <span class="adm-badge adm-badge--role">
                            {{ ucfirst($user->role) }}
                        </span>
                        @else
                        <span class="adm-empty-cell" aria-label="Sin rol">—</span>
                        @endif
                    </td>

                    {{-- Estado --}}
                    <td class="adm-td adm-td--center">
                        @if($user->is_banned)
                        <span class="adm-badge adm-badge--banned">
                            <span class="adm-badge-dot"></span>
                            Baneado
                        </span>
                        @else
                        <span class="adm-badge adm-badge--active">
                            <span class="adm-badge-dot"></span>
                            Activo
                        </span>
                        @endif
                    </td>

                    {{-- Verificado --}}
                    <td class="adm-td adm-td--center">
                        @if($user->is_verified)
                        <span class="adm-check adm-check--yes" title="Verificado" aria-label="Verificado">
                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                            </svg>
                        </span>
                        @else
                        <span class="adm-check adm-check--no" title="No verificado" aria-label="No verificado">
                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </span>
                        @endif
                    </td>

                    {{-- Publicaciones --}}
                    <td class="adm-td adm-td--center">
                        <span class="adm-count">{{ number_format($user->posts_count ?? 0) }}</span>
                    </td>

                    {{-- Fecha de registro --}}
                    <td class="adm-td adm-td--right">
                        <time class="adm-date" datetime="{{ $user->created_at->toDateString() }}">
                            {{ $user->created_at->translatedFormat('d M Y') }}
                        </time>
                    </td>

                    {{-- Acciones --}}
                    <td class="adm-td adm-td--center">
                        <div class="adm-actions-wrap">
                            <button
                                type="button"
                                class="adm-actions-btn"
                                data-dropdown-target="dropdown-user-{{ $user->id }}"
                                aria-label="Acciones para {{ $user->name }}"
                                aria-expanded="false">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                </svg>
                            </button>

                            <div class="adm-dropdown" id="dropdown-user-{{ $user->id }}" role="menu" aria-label="Opciones de usuario">

                                {{-- Ver perfil --}}
                                <a href="{{ route('profile.show', $user->username) }}"
                                    target="_blank"
                                    class="adm-dropdown-item"
                                    role="menuitem">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    Ver perfil
                                </a>

                                {{-- Banear / Desbanear --}}
                                @if($user->is_banned)
                                <button
                                    type="button"
                                    class="adm-dropdown-item adm-dropdown-item--success btn-unban"
                                    data-user-id="{{ $user->id }}"
                                    data-user-name="{{ $user->name }}"
                                    role="menuitem">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Desbanear usuario
                                </button>
                                <form method="POST"
                                    action="{{ route('admin.users.unban', $user) }}"
                                    id="unban-form-{{ $user->id }}"
                                    class="d-none">
                                    @csrf
                                    @method('PATCH')
                                </form>
                                @else
                                <button
                                    type="button"
                                    class="adm-dropdown-item adm-dropdown-item--danger btn-ban"
                                    data-user-id="{{ $user->id }}"
                                    data-user-name="{{ $user->name }}"
                                    role="menuitem">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                    </svg>
                                    Banear usuario
                                </button>
                                <form method="POST"
                                    action="{{ route('admin.users.ban', $user) }}"
                                    id="ban-form-{{ $user->id }}"
                                    class="d-none">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="reason" id="ban-reason-{{ $user->id }}">
                                </form>
                                @endif

                            </div>
                        </div>
                    </td>

                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>{{-- /adm-card --}}


{{-- ════════════════════════════════════════════════════════
     MODAL — Otorgar insignia Beta Tester
════════════════════════════════════════════════════════ --}}
<div class="modal-backdrop" id="badgeModalBackdrop" role="dialog" aria-modal="true" aria-labelledby="badgeModalTitle">
    <div class="modal-card" id="badgeModal">

        {{-- Header --}}
        <div class="modal-header">
            <div class="modal-header-icon modal-header-icon--accent">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                </svg>
            </div>
            <div>
                <div class="modal-title" id="badgeModalTitle">Otorgar insignia Beta Tester</div>
                <div class="modal-subtitle">Selecciona el usuario al que deseas otorgar la insignia.</div>
            </div>
            <button type="button" class="modal-close" id="closeBadgeModal" aria-label="Cerrar modal">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Body --}}
        <form method="POST" action="{{ route('admin.users.grantBadge') }}" id="badgeForm">
            @csrf
            <div class="modal-body">

                {{-- Búsqueda --}}
                <div class="adm-field">
                    <label class="adm-label" for="badgeUserSearch">Buscar usuario</label>
                    <div class="adm-search-wrap adm-search-wrap--full">
                        <svg class="adm-search-icon" width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input
                            type="search"
                            id="badgeUserSearch"
                            class="adm-search"
                            placeholder="Filtrar por nombre o @usuario..."
                            autocomplete="off"
                            aria-label="Buscar usuario para la insignia">
                    </div>
                </div>

                {{-- Lista de usuarios con checkbox --}}
                <div class="adm-badge-list" id="badgeUserList">
                    @foreach($users as $user)
                    <label class="adm-badge-item {{ $user->hasBadge('beta-tester') ? 'adm-badge-item--disabled' : '' }}"
                        data-name="{{ strtolower($user->name) }}"
                        data-handle="{{ strtolower($user->username) }}">
                        <input type="checkbox" name="user_ids[]" value="{{ $user->id }}"
                            class="adm-badge-checkbox"
                            {{ $user->hasBadge('beta-tester') ? 'disabled checked' : '' }}>
                        <img src="{{ $user->avatar_url }}" alt="" class="adm-badge-avatar" loading="lazy">
                        <div class="adm-badge-user">
                            <span class="adm-badge-username">{{ $user->name }}</span>
                            <span class="adm-badge-userhandle">{{ '@' . $user->username }}</span>
                        </div>
                        @if($user->hasBadge('beta-tester'))
                        <span class="adm-badge-pill adm-badge-pill--beta">Ya tiene</span>
                        @endif
                    </label>
                    @endforeach
                </div>
                <p class="adm-field-hint">Los usuarios que ya tienen la insignia aparecen marcados y no pueden seleccionarse.</p>

            </div>

            {{-- Footer --}}
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="cancelBadgeModal">Cancelar</button>
                <button type="submit" class="btn btn-primary" id="submitBadge" disabled>
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span id="submitBadgeText">Otorgar insignia</span>
                </button>
            </div>
        </form>
    </div>
</div>


{{-- ════════════════════════════════════════════════════════
     MODAL — Confirmar baneo
════════════════════════════════════════════════════════ --}}
<div class="modal-backdrop" id="banModalBackdrop" role="dialog" aria-modal="true" aria-labelledby="banModalTitle">
    <div class="modal-card" id="banModal">

        {{-- Header --}}
        <div class="modal-header">
            <div class="modal-header-icon modal-header-icon--danger">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                </svg>
            </div>
            <div>
                <div class="modal-title" id="banModalTitle">Banear usuario</div>
                <div class="modal-subtitle">Esta acción bloqueará el acceso del usuario a la plataforma.</div>
            </div>
            <button type="button" class="modal-close" id="closeBanModal" aria-label="Cerrar modal">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="modal-body">

            {{-- Info del usuario a banear --}}
            <div class="adm-ban-user-info" id="banUserInfo">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span id="banUserName">Usuario</span>
            </div>

            {{-- Razón del baneo --}}
            <div class="adm-field">
                <label class="adm-label" for="banReason">
                    Razón del baneo
                    <span class="adm-label-optional">(opcional)</span>
                </label>
                <textarea
                    id="banReason"
                    class="adm-textarea"
                    placeholder="Describe brevemente el motivo del baneo..."
                    rows="3"
                    maxlength="500"
                    aria-label="Razón del baneo"></textarea>
                <p class="adm-field-hint">Esta razón quedará registrada en el historial del sistema.</p>
            </div>

            {{-- Advertencia --}}
            <div class="adm-alert adm-alert--warning">
                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                El usuario perderá acceso inmediatamente. Podrás revertir esta acción desde la tabla.
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" id="cancelBanModal">Cancelar</button>
            <button type="button" class="btn btn-primary" style="background:#ef4444;" id="confirmBanBtn">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2"
                        d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                </svg>
                Confirmar baneo
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
{{-- jQuery (requerido por DataTables) --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
{{-- DataTables --}}
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
@vite('resources/js/admin/users/table.js')
@vite('resources/js/admin/shared/dropdown.js')
@vite('resources/js/admin/shared/ban-modal.js')
@vite('resources/js/admin/users/badge-modal.js')
@endpush