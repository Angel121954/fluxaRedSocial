@extends('layouts.app')
@section('title', 'Notificaciones')
@section('content')
<x-topbar :profile="$profile" />

<div class="feed-layout">
  <div class="feed-main" style="max-width: 680px; margin: 0 auto;">
    <div class="feed-header">
      <h1 class="feed-title">Notificaciones</h1>
      <p class="feed-subtitle" id="notificationCount">Cargando...</p>
      <button class="btn-accent" id="markAllRead">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
        </svg>
        Marcar todo leído
      </button>
    </div>

    <div class="filter-bar">
      <button class="filter-chip active" data-filter="all">Todas</button>
      <button class="filter-chip" data-filter="mention">@Menciones</button>
      <button class="filter-chip" data-filter="comment"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg><span>Comentarios</span></button>
      <button class="filter-chip" data-filter="follow"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><path d="M20 8v6m-3-3h6"/></svg><span>Seguidos</span></button>
      <button class="filter-chip" data-filter="like"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67 10.94 4.61a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg><span>Likes</span></button>
    </div>

    <div class="notif-container" id="notificationList">
      <div class="loading-box">
        <div class="spinner"></div>
        <p>Cargando...</p>
      </div>
    </div>
  </div>
</div>
@endsection

@push('styles')
@vite('resources/css/notifications.css')
@endpush

@push('scripts')
@endpush