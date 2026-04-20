@extends('layouts.app')
@section('content')
@section('title', 'Feed')
<x-topbar :profile="$profile" />

<div class="feed-layout">
  <div class="feed-main">
    <div class="feed-tabs">
      <span class="feed-tab active">Tu Feed</span>
    </div>

    <div id="publications-container">
      <x-project-list :projects="$projects" />
    </div>
  </div>

  <aside class="sidebar">
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

    <div class="about-fluxa-card">
      <div class="about-header">
        <img src="{{ asset('img/logoFluxa.png') }}" alt="Fluxa Logo" class="about-logo" />
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