@extends('layouts.app')
@section('title', 'Mensajes — Fluxa')

@section('content')
<x-topbar :profile="$profile" />

<div class="msgs-page">
    <div class="msgs-wrapper">

        <h1 class="msgs-title">Mensajes</h1>

        <div class="msgs-layout">

            <!-- ══════════════════════════════════════
                 PANEL IZQUIERDO — Lista de chats
            ══════════════════════════════════════ -->
            <aside class="msgs-sidebar" id="msgsSidebar">

                <!-- Acciones -->
                <div class="msgs-sidebar-top">
                    <button class="btn-new-msg" id="btnNuevoMensaje" aria-label="Iniciar nueva conversación">
                        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                        </svg>
                        Nuevo mensaje
                    </button>

                    <div class="msgs-search-wrap">
                        <svg class="msgs-search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="search" class="msgs-search" id="msgsSearch"
                            placeholder="Buscar..." autocomplete="off" aria-label="Buscar conversación">
                    </div>
                </div>

                <!-- Lista de conversaciones -->
                <div class="msgs-conv-list" id="msgsConvList" role="list">

                    @forelse($conversations as $conv)
                    @php
                    $isActive = isset($activeConversation) && $activeConversation->id === $conv->id;
                    $otherChat = $conv->otherChat;
                    $lastMsg = $conv->messages->last();
                    @endphp
                    <a href="{{ route('messages.index', ['conv' => $conv->id]) }}" class="msgs-conv-item{{ $isActive ? ' active' : '' }}" role="listitem">
                        <div class="msgs-conv-avatar-wrap">
                            <img src="{{ $otherChat->avatar_url }}"
                                alt="{{ $otherChat->name }}" class="msgs-conv-avatar"
                                onerror="this.src='/img/default-avatar.png'">
                        </div>
                        <div class="msgs-conv-info">
                            <div class="msgs-conv-row-top">
                                <span class="msgs-conv-name">{{ $otherChat->name }}</span>
                                <span class="msgs-conv-time" data-timestamp="{{ $lastMsg ? $lastMsg->created_at->getTimestampMs() : '' }}">{{ $lastMsg ? $lastMsg->created_at->diffForHumans() : '' }}</span>
                            </div>
                            <div class="msgs-conv-row-bottom">
                                <span class="msgs-conv-preview">
                                    @if($lastMsg && $lastMsg->sender_id === auth()->id())
                                    <span class="msgs-conv-preview-you">Tú: </span>
                                    @endif
                                    {{ $lastMsg ? Str::limit($lastMsg->body, 40) : '' }}
                                </span>
                                @if($conv->unread())
                                <span class="msgs-unread-badge">{{ $conv->unreadCount() }}</span>
                                @endif
                            </div>
                        </div>
                    </a>
                    @empty
                    <div class="msgs-empty-sidebar">
                        <p>No tienes conversaciones aún.</p>
                    </div>
                    @endforelse

                </div>
            </aside>

            <!-- ══════════════════════════════════════
                 PANEL DERECHO — Vista del chat
            ══════════════════════════════════════ -->
            <section class="msgs-chat" id="msgsChat" aria-label="Chat activo">

                @if(isset($activeConversation) && isset($otherUser))
                <!-- Cabecera -->
                <div class="msgs-chat-header">
                    <div class="msgs-chat-header-user">
                        <a href="{{ route('profile.show', ['username' => $otherUser->username]) }}">
                            <div class="msgs-chat-header-avatar-wrap">
                                <img src="{{ $otherUser->avatar_url }}"
                                    alt="{{ $otherUser->name }}"
                                    class="msgs-chat-header-avatar"
                                    onerror="this.src='/img/default-avatar.png'">
                            </div>
                        </a>
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

                <!-- Burbujas -->
                <div class="msgs-bubble-list" id="msgsBubbleList" role="log" aria-live="polite">
                    @php $lastDate = null; @endphp
                    @foreach($activeConversation->messages as $message)
                    @php
                    $isMine = $message->sender_id === auth()->id();
                    $msgDate = $message->created_at->toDateString();
                    $today = now()->toDateString();
                    $yesterday = now()->subDay()->toDateString();

                    if ($msgDate === $today) {
                    $dateLabel = 'Hoy';
                    } elseif ($msgDate === $yesterday) {
                    $dateLabel = 'Ayer';
                    } else {
                    $dateLabel = $message->created_at->translatedFormat('d \d\e F Y');
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
                        <img src="{{ $message->sender->avatar_url }}"
                            alt=""
                            class="msgs-bubble-avatar"
                            onerror="this.src='/img/default-avatar.png'">
                        @endif
                        <div class="msgs-bubble{{ $isMine ? ' msgs-bubble-mine' : ' msgs-bubble-theirs' }}">
                            {{ $message->body }}
                            <span class="msgs-bubble-time">
                                {{ $message->created_at->format('H:i') }}
                                @if($isMine)
                                @if($message->isRead())
                                <svg class="msgs-read-check" width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
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

                <!-- Input -->
                <div class="msgs-input-bar">
                    <textarea
                        class="msgs-input"
                        id="msgsInput"
                        placeholder="Escribe un mensaje..."
                        rows="1"
                        aria-label="Escribe un mensaje"
                        data-user-id="{{ auth()->id() }}"
                        data-user-name="{{ auth()->user()->name }}"
                        data-user-avatar="{{ auth()->user()->avatar_url }}"></textarea>
                    <button class="msgs-send-btn"
                        id="msgsSendBtn"
                        data-conv-id="{{ $activeConversation->id }}"
                        data-recipient="{{ $otherUser->username }}"
                        aria-label="Enviar mensaje">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                    </button>
                </div>

                @else
                <!-- Estado vacío -->
                <div class="msgs-empty-state" id="msgsEmptyState">
                    <div class="msgs-empty-icon" aria-hidden="true">
                        <svg width="56" height="56" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

        </div><!-- /msgs-layout -->
    </div><!-- /msgs-wrapper -->
</div><!-- /msgs-page -->

<!-- ── Modal: Nueva conversación ── -->
<div class="msgs-modal-overlay" id="msgsModalOverlay" aria-hidden="true">
    <div class="msgs-modal" role="dialog" aria-modal="true" aria-labelledby="msgsModalTitle">
        <div class="msgs-modal-header">
            <h2 class="msgs-modal-title" id="msgsModalTitle">Nueva conversación</h2>
            <button class="msgs-modal-close" id="msgsModalClose" aria-label="Cerrar">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div class="msgs-modal-search-wrap">
            <svg class="msgs-modal-search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input type="search" class="msgs-modal-search" id="msgsModalSearch"
                placeholder="Buscar usuarios..." autocomplete="off" aria-label="Buscar usuario">
        </div>
        <div class="msgs-modal-results" id="msgsModalResults" role="listbox" aria-live="polite">
            <p class="msgs-modal-hint">Empieza a escribir para buscar usuarios.</p>
        </div>
    </div>
</div>

@endsection

@push('styles')
@vite('resources/css/core/messages.css')
@endpush

@push('scripts')
@vite('resources/js/core/messages/index.js')
@endpush