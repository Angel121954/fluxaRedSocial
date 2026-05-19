@extends('admin.layouts.admin')

@section('title', 'Empresas — Admin Fluxa')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
@vite('resources/css/shared/modal.css')
@vite('resources/css/admin/users.css')
@endpush

@section('admin-content')

<div class="page-header">
    <div>
        <h1 class="page-title">Empresas</h1>
        <p class="page-sub">Cuentas registradas como empresa en Fluxa.</p>
    </div>
</div>

<div class="adm-card">

    <div class="adm-toolbar">
        <div class="adm-search-wrap">
            <svg class="adm-search-icon" width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input type="search" id="companiesSearch" class="adm-search" placeholder="Buscar empresa, email..." aria-label="Buscar empresas">
        </div>

        <div class="adm-filters">
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

            <button type="button" class="adm-btn-icon" id="clearFilters" title="Limpiar filtros" aria-label="Limpiar filtros">
                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    <div class="adm-table-wrap">
        <table id="companiesTable" class="adm-table" role="table" aria-label="Tabla de empresas">
            <thead>
                <tr>
                    <th class="adm-th" scope="col">Empresa</th>
                    <th class="adm-th" scope="col">Email</th>
                    <th class="adm-th adm-th--center" scope="col">Estado</th>
                    <th class="adm-th adm-th--center" scope="col">Verificado</th>
                    <th class="adm-th adm-th--center" scope="col">Proyectos</th>
                    <th class="adm-th adm-th--right" scope="col">Registro</th>
                    <th class="adm-th adm-th--center" scope="col" data-orderable="false">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($companies as $company)
                <tr class="adm-tr {{ $company->is_banned ? 'adm-tr--banned' : '' }}"
                    data-status="{{ $company->is_banned ? 'banned' : 'active' }}"
                    data-verified="{{ $company->is_verified ? '1' : '0' }}">

                    <td class="adm-td">
                        <div class="adm-user">
                            <div class="adm-avatar-wrap">
                                @if($company->avatar_url)
                                <img src="{{ $company->avatar_url }}"
                                    alt="{{ $company->name }}"
                                    class="adm-avatar"
                                    loading="lazy">
                                @else
                                <div class="adm-avatar adm-avatar--initials" aria-label="{{ $company->name }}">
                                    {{ strtoupper(substr($company->name, 0, 2)) }}
                                </div>
                                @endif
                                @if($company->is_banned)
                                <span class="adm-avatar-banned-dot" title="Empresa baneada" aria-label="Empresa baneada"></span>
                                @endif
                            </div>
                            <div class="adm-user-info">
                                <span class="adm-user-name">{{ $company->name }}</span>
                                <span class="adm-user-handle">{{ '@' . $company->username }}</span>
                            </div>
                        </div>
                    </td>

                    <td class="adm-td">
                        <span class="adm-email">{{ $company->email }}</span>
                    </td>

                    <td class="adm-td adm-td--center">
                        @if($company->is_banned)
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

                    <td class="adm-td adm-td--center">
                        @if($company->is_verified)
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

                    <td class="adm-td adm-td--center">
                        <span class="adm-count">{{ number_format($company->projects_count ?? 0) }}</span>
                    </td>

                    <td class="adm-td adm-td--right">
                        <time class="adm-date" datetime="{{ $company->created_at->toDateString() }}">
                            {{ $company->created_at->translatedFormat('d M Y') }}
                        </time>
                    </td>

                    {{-- Acciones --}}
                    <td class="adm-td adm-td--center">
                        <div class="adm-actions-wrap">
                            <button
                                type="button"
                                class="adm-actions-btn"
                                data-dropdown-target="dropdown-company-{{ $company->id }}"
                                aria-label="Acciones para {{ $company->name }}"
                                aria-expanded="false">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                </svg>
                            </button>

                            <div class="adm-dropdown" id="dropdown-company-{{ $company->id }}" role="menu" aria-label="Opciones de empresa">

                                {{-- Ver perfil --}}
                                <a href="{{ route('profile.show', $company->username) }}"
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
                                @if($company->is_banned)
                                <button
                                    type="button"
                                    class="adm-dropdown-item adm-dropdown-item--success btn-unban"
                                    data-user-id="{{ $company->id }}"
                                    data-user-name="{{ $company->name }}"
                                    role="menuitem">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Desbanear empresa
                                </button>
                                <form method="POST"
                                    action="{{ route('admin.companies.unban', $company) }}"
                                    id="unban-form-{{ $company->id }}"
                                    class="d-none">
                                    @csrf
                                    @method('PATCH')
                                </form>
                                @else
                                <button
                                    type="button"
                                    class="adm-dropdown-item adm-dropdown-item--danger btn-ban"
                                    data-user-id="{{ $company->id }}"
                                    data-user-name="{{ $company->name }}"
                                    role="menuitem">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                    </svg>
                                    Banear empresa
                                </button>
                                <form method="POST"
                                    action="{{ route('admin.companies.ban', $company) }}"
                                    id="ban-form-{{ $company->id }}"
                                    class="d-none">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="reason" id="ban-reason-{{ $company->id }}">
                                </form>
                                @endif

                            </div>
                        </div>
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="7" class="adm-empty">
                        <div class="adm-empty-state">
                            <svg width="40" height="40" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M3 21h18M3 10h18M3 7l9-4 9 4M4 10v11m16-11v11M8 14v.01M12 14v.01M16 14v.01M12 18v.01" />
                            </svg>
                            <p>No hay empresas registradas</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
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
                <div class="modal-title" id="banModalTitle">Banear empresa</div>
                <div class="modal-subtitle">Esta acción bloqueará el acceso de la empresa a la plataforma.</div>
            </div>
            <button type="button" class="modal-close" id="closeBanModal" aria-label="Cerrar modal">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="modal-body">

            {{-- Info de la empresa a banear --}}
            <div class="adm-ban-user-info" id="banUserInfo">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span id="banUserName">Empresa</span>
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
                La empresa perderá acceso inmediatamente. Podrás revertir esta acción desde la tabla.
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
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
@vite('resources/js/admin/companies/table.js')
@vite('resources/js/admin/shared/dropdown.js')
@vite('resources/js/admin/shared/ban-modal.js')
@endpush
