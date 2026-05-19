<div class="card">
    <div class="card-header">
        <span class="card-title">Actividad reciente</span>
    </div>
    <div class="activity-list">
        @forelse ($activity as $item)
        <div class="activity-item">
            <div class="act-icon
                    @switch($item['type'])
                        @case('user') act-teal @break
                        @case('suggestion') act-amber @break
                        @case('contact') act-blue @break
                        @default act-teal
                    @endswitch">
                @switch($item['type'])
                @case('user')
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2" />
                    <circle cx="9" cy="7" r="4" />
                </svg>
                @break
                @case('suggestion')
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z" />
                </svg>
                @break
                @case('contact')
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                    <polyline points="22,6 12,13 2,6" />
                </svg>
                @break
                @endswitch
            </div>
            <div class="act-body">
                <div class="act-title">{{ $item['title'] }}</div>
                <div class="act-sub">{{ Str::limit($item['description'], 50) }}</div>
            </div>
            <div class="act-meta">
                <span class="act-time">{{ $item['time']->diffForHumans() }}</span>
            </div>
        </div>
        @empty
        <div class="activity-item">
            <div class="act-body">
                <div class="act-sub">Sin actividad reciente</div>
            </div>
        </div>
        @endforelse
    </div>
</div>