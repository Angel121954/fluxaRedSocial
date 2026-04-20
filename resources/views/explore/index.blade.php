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
      <a href="{{ route('explore.trending') }}" class="feed-tab {{ request()->is('explore/trending') || request()->is('explore') && !request()->get('q') ? 'active' : '' }}" data-url="{{ route('explore.trending') }}">🔥 Tendencias</a>
      <a href="{{ route('explore.recent') }}" class="feed-tab {{ request()->is('explore/recent') ? 'active' : '' }}" data-url="{{ route('explore.recent') }}">Recientes</a>
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

<x-modal-comments />
<x-modal-report />
@endsection

@push('scripts')
@vite('resources/js/core/explore/index.js')
@endpush
@push('styles')
@vite('resources/css/core/explore.css')
@endpush