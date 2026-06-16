@extends('layouts.app')
@section('content')
@section('title', request()->get('q') ? 'Buscar: ' . request()->get('q') : 'Explorar')
<x-topbar :profile="$profile" />

<!-- ══════════════════════════════════════════
     FEED LAYOUT
══════════════════════════════════════════ -->
<div class="feed-layout">
  <!-- ──── FEED COLUMN ──── -->
  <div class="feed-main">
    <!-- Tabs -->
    <div class="feed-tabs">
      <a href="{{ route('explore.trending') }}" class="feed-tab {{ request()->is('explore/trending') || request()->is('explore') && !request()->get('q') && !request()->is('explore/map') ? 'active' : '' }}" data-url="{{ route('explore.trending') }}"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 2.5z" />
        </svg> Tendencias</a>
      <a href="{{ route('explore.recent') }}" class="feed-tab {{ request()->is('explore/recent') ? 'active' : '' }}" data-url="{{ route('explore.recent') }}">Recientes</a>
      <a href="{{ route('explore.map') }}" class="feed-tab {{ request()->is('explore/map') ? 'active' : '' }}" data-navigate="true">Mapa</a>
    </div>

    @if(isset($technology))
    <div class="topic-header">
      <span class="topic-label">Filtrando por:</span>
      <span class="topic-current">#{{ $technology->name }}</span>
      <a href="{{ route('explore.trending') }}" class="topic-clear">✕</a>
    </div>
    @endif

    @if(request()->get('q'))
    <div class="topic-header">
      <span class="topic-label">Buscando:</span>
      <span class="topic-current">"{{ request()->get('q') }}"</span>
      <a href="{{ route('explore.trending') }}" class="topic-clear">✕</a>
    </div>
    @endif

    <!-- Publications Container -->
    <div id="publications-container">
      @if(request()->is('explore/map'))
        <div id="dev-map" class="map-wrapper"></div>
      @else
        <x-project-list :projects="$projects" />
      @endif
    </div>
  </div>

  <!-- ──── SIDEBAR ──── -->
  <aside class="sidebar">
    @if(request()->is('explore/map'))
    <!-- Widget: Devs en el mapa -->
    <div class="widget map-sidebar-widget">
      <div class="widget-header">
        <h3 class="widget-title">Devs en el mapa</h3>
      </div>
      <div id="nearby-devs" class="nearby-devs">
        <p class="text-sm" style="color: var(--ink-400); padding: 0.75rem 0;">
          Cargando...
        </p>
      </div>
    </div>
    @endif

    @if(Auth::user()->role != 'guest')
    <!-- Temas populares -->
    <div class="widget">
      <div class="widget-header">
        <h3 class="widget-title">Temas populares</h3>
        @if($topTechnologies->count() > 5)
        <button class="widget-link" id="showMoreTopics">Ver más →</button>
        @endif
      </div>
      <div class="topics-grid" id="topicsGrid">
        @foreach($topTechnologies->take(5) as $tech)
        <a href="{{ route('explore.topic', $tech->slug) }}" class="topic-pill">#{{ $tech->name }}</a>
        @endforeach
        @foreach($topTechnologies->skip(5)->take(10) as $tech)
        <a href="{{ route('explore.topic', $tech->slug) }}" class="topic-pill more-topic" style="display: none">#{{ $tech->name }}</a>
        @endforeach
      </div>
    </div>
    @endif

  </aside>
</div>

<x-modal-comments />
<x-modal-report />

@if(request()->is('explore/map'))
@push('scripts')
@vite('resources/js/core/explore/map.js')
@endpush
@push('styles')
@vite('resources/css/core/explore/map.css')
@endpush
@endif
@endsection

@push('scripts')
@vite('resources/js/projects/modalComment.js')
@vite('resources/js/core/explore/index.js')
@endpush
@push('styles')
@vite('resources/css/shared/modal.css')
@vite('resources/css/projects/projectMedia.css')
@vite('resources/css/core/explore.css')
@endpush
