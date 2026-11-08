@props(['active' => null])

<div class="feed-tabs">
    <a href="{{ route('explore.trending') }}"
        class="feed-tab {{ $active === 'trending' ? 'active' : '' }}"
        data-url="{{ route('explore.trending') }}"
        @if($active === 'trending') aria-current="page" @endif>
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 2.5z" />
        </svg>
        Tendencias
    </a>

    <a href="{{ route('explore.recent') }}"
        class="feed-tab {{ $active === 'recent' ? 'active' : '' }}"
        data-url="{{ route('explore.recent') }}"
        @if($active === 'recent') aria-current="page" @endif>
        Recientes
    </a>

    <a href="{{ route('explore.people') }}"
        class="feed-tab {{ $active === 'people' ? 'active' : '' }}"
        @if($active === 'people') aria-current="page" @endif>
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
            <circle cx="9" cy="7" r="4" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" />
        </svg>
        Personas
    </a>
</div>
