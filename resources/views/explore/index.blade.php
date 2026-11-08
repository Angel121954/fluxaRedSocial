@extends('layouts.app')
@section('content')
@section('title', isset($project) ? $project->title . ' · Fluxa' : (request()->get('q') ? 'Buscar: ' . request()->get('q') : 'Explorar'))
@if(isset($project))
@section('og_title', $project->title)
@section('og_description', Str::limit(strip_tags($project->content), 155))
@section('og_image', $project->cover_url ?? asset('img/favicon.png'))
@if($project->cover_url)
@section('og_image_width', '1200')
@section('og_image_height', '630')
@endif
@section('og_type', 'article')
@section('canonical', route('projects.show', $project))
@endif
<x-topbar :profile="$profile" />

<h1 class="sr-only">Explorar proyectos</h1>

<!-- ══════════════════════════════════════════
     FEED LAYOUT
══════════════════════════════════════════ -->
<div class="feed-layout">
  <!-- ──── FEED COLUMN ──── -->
  <div class="feed-main">
    @unless(request()->is('explore/map'))
    <!-- Tabs -->
    <x-explore-tabs :active="request()->routeIs('explore.recent') ? 'recent' : (request()->routeIs('explore.trending') || request()->routeIs('explore.index') ? 'trending' : null)" />
    @endunless

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

    @if(Auth::user()->role != 'guest' && $topTechnologies->count() > 0 && !request()->is('explore/map'))
    <div class="topics-bar">
      <div class="topics-grid" id="topicsGrid">
        @foreach($topTechnologies->take(5) as $tech)
        <a href="{{ route('explore.topic', $tech->slug) }}" class="topic-pill">#{{ $tech->name }}</a>
        @endforeach
        @foreach($topTechnologies->skip(5)->take(10) as $tech)
        <a href="{{ route('explore.topic', $tech->slug) }}" class="topic-pill more-topic" style="display: none">#{{ $tech->name }}</a>
        @endforeach
        @if($topTechnologies->count() > 5)
        <button class="topic-pill topic-pill-more" id="showMoreTopics">+{{ $topTechnologies->count() - 5 }}</button>
        @endif
      </div>
    </div>
    @endif

    <!-- Publications Container -->
    <div id="publications-container">
      @if(request()->is('explore/map'))
        <div id="dev-map" class="map-wrapper"></div>
        <div id="nearby-devs" class="nearby-devs"></div>
      @else
        @if(isset($technology) || request()->get('q'))
          <x-project-list :projects="$projects" empty-cta-label="Ver tendencias" empty-cta-href="{{ route('explore.trending') }}" />
        @else
          <x-project-list :projects="$projects" />
        @endif
      @endif
    </div>
  </div>
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
