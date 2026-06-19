@extends('layouts.app')
@section('content')
@section('title', 'Feed')
<x-topbar :profile="$profile" />

<div class="feed-layout">
  <div class="feed-main">
    @if($topTechnologies->count() > 0)
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

    <div id="publications-container">
      <x-project-list :projects="$projects" />
    </div>
  </div>
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