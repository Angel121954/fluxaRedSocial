<div class="msgs-conv-list" id="msgsConvList" role="list">
    @forelse($conversations as $conv)
    @php
    $isActive = isset($activeConversationId) && $activeConversationId == $conv['id'];
    $otherChat = $conv['user_a']['id'] == auth()->id() ? $conv['user_b'] : $conv['userA'];
    $lastMsg = data_get($conv, 'lastMessage');
    @endphp
    <div class="msgs-conv-item-wrapper{{ $isActive ? ' active' : '' }}">
        <a href="{{ route('messages.index', ['conv' => $conv['id']]) }}" class="msgs-conv-item" role="listitem">
            <div class="msgs-conv-avatar-wrap">
                <img src="{{ $otherChat['avatar_url'] ?? asset('img/default-avatar.png') }}"
                    alt="{{ $otherChat['name'] }}" class="msgs-conv-avatar">
            </div>
            <div class="msgs-conv-info">
                <div class="msgs-conv-row-top">
                    <span class="msgs-conv-name">{{ $otherChat['name'] }}</span>
                    <span class="msgs-conv-time">{{ $lastMsg ? \Carbon\Carbon::parse($lastMsg['created_at'])->diffForHumans() : '' }}</span>
                </div>
                <div class="msgs-conv-row-bottom">
                    <span class="msgs-conv-preview">
                        @if($lastMsg && $lastMsg['sender_id'] === auth()->id())
                        <span class="msgs-conv-preview-you">Tú: </span>
                        @endif
                        {{ $lastMsg ? Str::limit($lastMsg['body'], 40) : '' }}
                    </span>
                </div>
            </div>
        </a>
        <button class="msgs-conv-menu-btn" data-conv-id="{{ $conv['id'] }}" data-user-id="{{ $otherChat['id'] }}" aria-label="Opciones">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01" />
            </svg>
        </button>
    </div>
    @empty
    <div class="msgs-empty-sidebar">
        <p>No tienes conversaciones aún.</p>
    </div>
    @endforelse
</div>

@script
<script>
    // Escuchar eventos de Livewire
    document.querySelectorAll('.msgs-conv-item-wrapper a').forEach(link => {
        link.addEventListener('click', (e) => {
            const url = new URL(link.href);
            const convId = url.searchParams.get('conv');
            if (convId) {
                $wire.dispatch('conversationSelected', convId);
            }
        });
    });
</script>
@endscript