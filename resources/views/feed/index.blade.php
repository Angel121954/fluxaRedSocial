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

  </aside>
</div>

<x-modal-comments />
<x-modal-report />
@endsection

@push('scripts')
@vite('resources/js/projects/modalComment.js')
@vite('resources/js/core/explore/index.js')
@endpush
@push('styles')
@vite('resources/css/shared/modal.css')
@vite('resources/css/core/explore.css')
@endpush