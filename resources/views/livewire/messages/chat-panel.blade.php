@if($conversation && $otherUser)
<div class="msgs-chat" id="msgsChat" aria-label="Chat activo">
    <div class="msgs-chat-header">
        <div class="msgs-chat-header-user">
            <div class="msgs-chat-header-avatar-wrap">
                <img src="{{ $otherUser->avatar_url }}" alt="{{ $otherUser->name }}" class="msgs-chat-header-avatar" onerror="this.src='/img/default-avatar.png'">
            </div>
            <div class="msgs-chat-header-info">
                <span class="msgs-chat-header-name">{{ $otherUser->name }}</span>
                <span class="msgs-chat-header-sub">&#64;{{ $otherUser->username }}</span>
            </div>
        </div>
        <button class="msgs-back-btn" id="msgsBackBtn" aria-label="Volver">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </button>
    </div>

    <div class="msgs-bubble-list" id="msgsBubbleList" role="log" aria-live="polite">
        @php $lastDate = null; @endphp
        @foreach($messages as $message)
        @php
        $isMine = $message['sender_id'] === auth()->id();
        $msgDate = \Carbon\Carbon::parse($message['created_at'])->toDateString();
        $today = now()->toDateString();
        $yesterday = now()->subDay()->toDateString();

        if ($msgDate === $today) {
            $dateLabel = 'Hoy';
        } elseif ($msgDate === $yesterday) {
            $dateLabel = 'Ayer';
        } else {
            $dateLabel = \Carbon\Carbon::parse($message['created_at'])->translatedFormat('d MMM, Y');
        }

        $showDateSeparator = $lastDate !== $msgDate;
        $lastDate = $msgDate;
        @endphp

        @if($showDateSeparator)
        <div class="msgs-date-separator">
            <span>{{ $dateLabel }}</span>
        </div>
        @endif

        <div class="msgs-bubble-wrap{{ $isMine ? ' mine' : ' theirs' }}">
            @if(!$isMine)
            <img src="{{ $message['sender']['avatar_url'] ?? asset('img/default-avatar.png') }}" alt="" class="msgs-bubble-avatar" onerror="this.src='/img/default-avatar.png'">
            @endif
            <div class="msgs-bubble{{ $isMine ? ' msgs-bubble-mine' : ' msgs-bubble-theirs' }}">
                {{ $message['body'] }}
                <span class="msgs-bubble-time">
                    {{ \Carbon\Carbon::parse($message['created_at'])->format('H:i') }}
                    @if($isMine)
                    @if($message['read_at'])
                    <span class="msgs-double-check">
                        <svg class="msgs-read-check" width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                        </svg>
                        <svg class="msgs-read-check" width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                        </svg>
                    </span>
                    @else
                    <svg class="msgs-sent-check" width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    @endif
                    @endif
                </span>
            </div>
        </div>
        @endforeach
    </div>

    <div class="msgs-input-bar">
        <textarea
            class="msgs-input"
            id="msgsInput"
            wire:model="newMessage"
            wire:keydown.enter.prevent="sendMessage"
            placeholder="Escribe un mensaje..."
            rows="1"
            aria-label="Escribe un mensaje"></textarea>
        <button class="msgs-send-btn" wire:click="sendMessage" aria-label="Enviar mensaje">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
            </svg>
        </button>
    </div>
</div>
@else
<div class="msgs-empty-state" id="msgsEmptyState">
    <div class="msgs-empty-icon" aria-hidden="true">
        <svg width="56" height="56" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.25" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
        </svg>
    </div>
    <div class="msgs-empty-text">
        <p class="msgs-empty-label">Selecciona una conversación</p>
        <p class="msgs-empty-sublabel">o inicia una nueva para comenzar a chatear.</p>
    </div>
</div>
@endif