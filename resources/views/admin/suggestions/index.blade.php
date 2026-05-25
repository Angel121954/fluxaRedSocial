@extends('admin.layouts.admin')

@section('title', 'Sugerencias — Admin Fluxa')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
@vite('resources/css/shared/modal.css')
@vite('resources/css/admin/users.css')
@vite('resources/css/admin/suggestions.css')
@endpush

@section('admin-content')

<div class="page-header">
    <div>
        <h1 class="page-title">Sugerencias</h1>
        <p class="page-sub">Gestiona y revisa las sugerencias enviadas por los usuarios.</p>
    </div>
</div>

<div class="adm-card">

    <div class="adm-toolbar">
        <div class="adm-search-wrap">
            <svg class="adm-search-icon" width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input type="search" id="suggestionsSearch" class="adm-search" placeholder="Buscar sugerencia..." aria-label="Buscar sugerencias">
        </div>

        <div class="adm-filters">
            <div class="adm-select-wrap">
                <select id="filterStatus" class="adm-select" aria-label="Filtrar por estado">
                    <option value="">Todos los estados</option>
                    <option value="pending">Pendiente</option>
                    <option value="approved">Aprobado</option>
                    <option value="reviewing">En revisión</option>
                    <option value="rejected">Rechazado</option>
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
        <table id="suggestionsTable" class="adm-table {{ $suggestions->isEmpty() ? 'adm-table--empty' : '' }}" role="table" aria-label="Tabla de sugerencias">
            <thead>
                <tr>
                    <th class="adm-th" scope="col">Estado</th>
                    <th class="adm-th" scope="col">Usuario</th>
                    <th class="adm-th" scope="col">Descripción</th>
                    <th class="adm-th adm-th--center" scope="col">Imagen</th>
                    <th class="adm-th adm-th--right" scope="col">Fecha</th>
                    <th class="adm-th adm-th--center" scope="col" data-orderable="false">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($suggestions as $suggestion)
                <tr class="adm-tr" data-status="{{ $suggestion->status }}">

                    <td class="adm-td">
                        <span class="adm-badge adm-badge--{{ $suggestion->status }}">
                            <span class="adm-badge-dot"></span>
                            @switch($suggestion->status)
                            @case('pending') Pendiente @break
                            @case('approved') Aprobado @break
                            @case('reviewing') En revisión @break
                            @case('rejected') Rechazado @break
                            @default {{ ucfirst($suggestion->status) }}
                            @endswitch
                        </span>
                    </td>

                    <td class="adm-td">
                        <div class="adm-user">
                            <div class="adm-user-info">
                                <span class="adm-user-name">{{ $suggestion->user->name }}</span>
                                <span class="adm-user-handle">{{ '@' . $suggestion->user->username }}</span>
                            </div>
                        </div>
                    </td>

                    <td class="adm-td">
                        <span class="adm-desc">{{ $suggestion->description }}</span>
                    </td>

                    <td class="adm-td adm-td--center">
                        @if($suggestion->image_url)
                        <div class="adm-thumb-wrap">
                            <img src="{{ $suggestion->image_url }}"
                                alt="Imagen adjunta"
                                class="adm-thumb"
                                loading="lazy">
                        </div>
                        @else
                        <span class="adm-empty-cell" aria-label="Sin imagen">—</span>
                        @endif
                    </td>

                    <td class="adm-td adm-td--right">
                        <time class="adm-date" datetime="{{ $suggestion->created_at->toDateString() }}">
                            {{ $suggestion->created_at->translatedFormat('d M Y') }}
                        </time>
                    </td>

                    <td class="adm-td adm-td--center">
                        <div class="adm-actions-wrap">
                            <button
                                type="button"
                                class="adm-actions-btn"
                                data-dropdown-target="dropdown-{{ $suggestion->id }}"
                                aria-label="Acciones para esta sugerencia"
                                aria-expanded="false">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                </svg>
                            </button>

                            <div class="adm-dropdown" id="dropdown-{{ $suggestion->id }}" role="menu" aria-label="Opciones">

                                <button type="button"
                                    class="adm-dropdown-item btn-view-suggestion"
                                    role="menuitem"
                                    data-id="{{ $suggestion->id }}"
                                    data-status="{{ $suggestion->status }}"
                                    data-user-name="{{ $suggestion->user->name }}"
                                    data-user-handle="{{ '@' . $suggestion->user->username }}"
                                    data-user-avatar="{{ $suggestion->user->avatar_url }}"
                                    data-description="{{ $suggestion->description }}"
                                    data-image="{{ $suggestion->image_url ?? '' }}"
                                    data-date="{{ $suggestion->created_at->translatedFormat('d M Y \\a \\l\\a\\s H:i') }}">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    Ver detalle
                                </button>

                                @unless($suggestion->status === 'approved')
                                <div class="adm-dropdown-divider" role="separator"></div>

                                <form method="POST" action="{{ route('admin.suggestions.approve', $suggestion) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="adm-dropdown-item" role="menuitem">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Aprobar
                                    </button>
                                </form>
                                @endunless

                                <form method="POST" action="{{ route('admin.suggestions.markRead', $suggestion) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="adm-dropdown-item" role="menuitem">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                        Marcar como leído
                                    </button>
                                </form>

                                <div class="adm-dropdown-divider" role="separator"></div>

                                <form method="POST" action="{{ route('admin.suggestions.destroy', $suggestion) }}" class="form-delete-suggestion">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="adm-dropdown-item adm-dropdown-item--danger" role="menuitem">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Eliminar
                                    </button>
                                </form>

                            </div>
                        </div>
                    </td>

                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<x-modal-suggestion-detail />

@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
@vite('resources/js/admin/shared/dropdown.js')
@vite('resources/js/admin/suggestions/delete.js')
@vite('resources/js/admin/suggestions/table.js')
@vite('resources/js/admin/suggestions/detailModal.js')
@endpush