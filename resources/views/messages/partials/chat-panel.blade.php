<section class="msgs-chat" id="msgsChat" aria-label="Chat activo">

    @if(isset($activeConversation) && isset($otherUser))
    <!-- Cabecera -->
    <div class="msgs-chat-header">
        <div class="msgs-chat-header-user">
            <a href="{{ route('profile.show', ['username' => $otherUser->username]) }}" class="msgs-chat-header-avatar-link">
                <div class="msgs-chat-header-avatar-wrap">
                    <img src="{{ $otherUser->avatar_url }}"
                        alt="{{ $otherUser->name }}"
                        class="msgs-chat-header-avatar"
                        onerror="this.src='/img/default-avatar.png'">
                    <span class="msgs-header-online-dot" id="msgsHeaderOnlineDot" style="display:none"></span>
                </div>
            </a>
            <div class="msgs-chat-header-info">
                <a href="{{ route('profile.show', ['username' => $otherUser->username]) }}" class="msgs-chat-header-name">
                    {{ $otherUser->name }}
                    @if($otherUser->is_verified ?? false)
                    <svg class="msgs-verified" width="14" height="14" viewBox="0 0 24 24" fill="var(--accent)">
                        <path d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                    </svg>
                    @endif
                </a>
                <span class="msgs-chat-header-sub" id="msgsHeaderSub">
                    {{ $otherUser->profile->role ?? $otherUser->profile->headline ?? '@'.$otherUser->username }}
                    <span class="msgs-online-sep" id="msgsOnlineSep" style="display:none"> • </span>
                    <span class="msgs-online-label" id="msgsOnlineLabel" style="display:none">En línea</span>
                </span>
            </div>
        </div>

        <!-- Acciones del header -->
        <div class="msgs-header-actions">
            <button class="msgs-header-action msgs-header-action--more" id="msgsMoreBtn" aria-label="Más opciones" title="Más opciones">
                <svg width="17" height="17" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                </svg>
            </button>
            <!-- Dropdown "más opciones" -->
            <div class="msgs-more-dropdown" id="msgsMoreDropdown" aria-hidden="true">
                <button class="msgs-more-item{{ $hasBlockedOther ? ' is-blocked' : '' }}" id="msgsBlockBtn"
                    data-user-id="{{ $otherUser->id }}"
                    data-blocked="{{ $hasBlockedOther ? 'true' : 'false' }}"
                    data-accepts-messages="{{ ($otherUser->profile->accept_messages ?? true) ? 'true' : 'false' }}">
                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        @if($hasBlockedOther)
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        @else
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                        @endif
                    </svg>
                    {{ $hasBlockedOther ? 'Desbloquear usuario' : 'Bloquear usuario' }}
                </button>
            </div>
        </div>

        <button class="msgs-back-btn" id="msgsBackBtn" aria-label="Volver">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </button>
    </div>

    <!-- Burbujas -->
    <div class="msgs-bubble-list" id="msgsBubbleList" role="log" aria-live="polite"
        data-conv-id="{{ $activeConversation->id }}">
        @php $lastDate = null; @endphp
        @foreach($activeMessages as $message)
        @php
        $isMine = $message->sender_id === auth()->id();
        $msgDate = $message->created_at->timezone('America/Bogota')->toDateString();
        $today = now()->timezone('America/Bogota')->toDateString();
        $yesterday = now()->timezone('America/Bogota')->subDay()->toDateString();

        if ($msgDate === $today) {
        $dateLabel = 'Hoy';
        } elseif ($msgDate === $yesterday) {
        $dateLabel = 'Ayer';
        } else {
        $dateLabel = $message->created_at->timezone('America/Bogota')->translatedFormat('d \d\e F Y');
        }

        $showDateSeparator = $lastDate !== $msgDate;
        $lastDate = $msgDate;
        @endphp
        @if($showDateSeparator)
        <div class="msgs-date-separator" data-date="{{ $msgDate }}">
            <span>{{ $dateLabel }}</span>
        </div>
        @endif
        <div class="msgs-bubble-wrap{{ $isMine ? ' mine' : ' theirs' }}" data-msg-id="{{ $message->id }}" data-created-at="{{ $message->created_at->timestamp }}">
            @if(!$isMine)
            <img src="{{ $message->sender?->avatar_url ?? '/img/default-avatar.png' }}"
                alt="" class="msgs-bubble-avatar"
                onerror="this.src='/img/default-avatar.png'">
            @endif
            <div class="msgs-bubble{{ $isMine ? ' msgs-bubble-mine' : ' msgs-bubble-theirs' }}{{ $message->isMedia() ? ' msgs-bubble--media' : '' }}{{ $message->isEdited() ? ' msgs-bubble-edited' : '' }}">
                @if($isMine && !$message->isMedia() && $message->created_at->diffInMinutes(now()) < 60)
                <button class="msgs-edit-btn" data-msg-id="{{ $message->id }}" data-body="{{ $message->body }}" aria-label="Editar mensaje" title="Editar">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 3a2.828 2.828 0 114 4L7.5 20.5 2 22l1.5-5.5L17 3z" />
                    </svg>
                </button>
                @endif
                @if($message->isMedia() && ($message->isImage() || $message->isGif()))
                <div class="msgs-media-img-wrap{{ $message->isGif() ? ' msgs-media-gif-wrap' : '' }}">
                    <img src="{{ $message->media_url }}" alt="{{ $message->media_name ?? 'Imagen' }}" class="msgs-media-img" loading="lazy">
                </div>
                @elseif($message->isMedia() && $message->isFile())
                <div class="msgs-media-file">
                    <div class="msgs-media-file-icon">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                        </svg>
                    </div>
                    <div class="msgs-media-file-info">
                        <span class="msgs-media-file-name">{{ $message->media_name ?? 'Archivo' }}</span>
                        <span class="msgs-media-file-size">@if($message->media_size){{ number_format($message->media_size / 1024, 1) }} KB @endif</span>
                    </div>
                </div>
                @endif
                @if($message->body)
                <div class="msgs-bubble-body">{{ $message->body }}</div>
                @endif
                <span class="msgs-bubble-time">
                    {{ $message->created_at->format('H:i') }}
                    @if($message->isEdited())
                    <span class="msgs-bubble-edited-label">· editado</span>
                    @endif
                </span>
                <!-- Reacciones -->
                <div class="msgs-bubble-reactions" data-msg-id="{{ $message->id }}">
                    {{-- Se llenan dinámicamente vía JS --}}
                </div>

            </div>
        </div>
        @endforeach

        <!-- Typing indicator -->
        <div class="msgs-typing-indicator" id="msgsTypingIndicator">
            <img src="{{ $otherUser->avatar_url ?? '/img/default-avatar.png' }}"
                alt="" class="msgs-typing-avatar"
                onerror="this.src='/img/default-avatar.png'">
            <div class="msgs-typing-dots">
                <span></span><span></span><span></span>
            </div>
        </div>
    </div>

    <!-- Input bar -->
    <div class="msgs-input-bar" id="msgsInputBar">
        <textarea
            class="msgs-input"
            id="msgsInput"
            placeholder="Escribe un mensaje a {{ $otherUser->name }}..."
            rows="1"
            aria-label="Escribe un mensaje"
            data-user-id="{{ auth()->id() }}"
            data-user-name="{{ auth()->user()->name }}"
            data-user-avatar="{{ auth()->user()->avatar_url }}"
            style="display: {{ (($otherUser->profile->accept_messages ?? true) && !$isBlockedByOther) ? '' : 'none' }}"></textarea>

        @php($canInteract = ($otherUser->profile->accept_messages ?? true) && !$isBlockedByOther)

        <div class="msgs-toolbar-father">
            <div class="msgs-toolbar" style="display: {{ $canInteract ? '' : 'none' }}">
                <button class="msgs-toolbar-btn" id="msgsEmojiBtn" aria-label="Emoji" title="Emoji">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </button>
                <button class="msgs-toolbar-btn" id="msgsAttachBtn" aria-label="Insertar enlace" title="Insertar enlace">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                    </svg>
                </button>
                <button class="msgs-toolbar-btn" id="msgsImageBtn" aria-label="Enviar imagen" title="Imagen">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </button>
                <button class="msgs-toolbar-btn msgs-toolbar-btn--label" id="msgsGifBtn" aria-label="GIF" title="GIF">
                    <span>GIF</span>
                </button>
            </div>

            <div class="msgs-input-actions">
                <button class="msgs-share-project-btn" id="msgsShareProjectBtn"
                    style="display: {{ $canInteract ? '' : 'none' }}"
                    data-conv-id="{{ $activeConversation->id }}">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                    </svg>
                    Compartir proyecto
                </button>
                <button class="msgs-send-btn" id="msgsSendBtn"
                    style="display: {{ $canInteract ? '' : 'none' }}"
                    disabled
                    data-conv-id="{{ $activeConversation->id }}"
                    data-recipient="{{ $otherUser->username }}"
                    aria-label="Enviar mensaje">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M3.478 2.405a.75.75 0 00-.926.94l2.432 7.905H13.5a.75.75 0 010 1.5H4.984l-2.432 7.905a.75.75 0 00.926.94 60.519 60.519 0 0018.445-8.986.75.75 0 000-1.218A60.517 60.517 0 003.478 2.405z" />
                    </svg>
                </button>
                <div id="msgsInputDisabled" class="msgs-input msgs-input--disabled"
                    style="display:{{ $canInteract ? 'none' : 'flex' }}">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M18.364 5.636a9 9 0 010 12.728M5.636 5.636a9 9 0 0112.728 0" />
                    </svg>
                    <span id="msgsDisabledText">
                        @if($isBlockedByOther)
                        No puedes enviar mensajes a este usuario. {{ $otherUser->name ?? '' }} te ha bloqueado.
                        @elseif($hasBlockedOther)
                        Has bloqueado a {{ $otherUser->name }}.
                        @else
                        {{ $otherUser->name }} no acepta mensajes directos.
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>

    @else
    <!-- Estado vacío -->
    <div class="msgs-empty-state" id="msgsEmptyState">
        <div class="msgs-empty-icon" aria-hidden="true">
            <svg width="36" height="36" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.25"
                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
            </svg>
        </div>
        <div class="msgs-empty-text">
            <p class="msgs-empty-label">Selecciona una conversación</p>
            <p class="msgs-empty-sublabel">o inicia una nueva para comenzar a chatear.</p>
        </div>
    </div>
    @endif

</section>
