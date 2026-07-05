@extends('layouts.app')
@section('title', 'Mensajes — Fluxa')

@section('content')
<x-topbar />

<div class="msgs-page">
    <div class="msgs-wrapper">
        <div class="msgs-layout{{ isset($activeConversation) ? ' chat-active' : '' }}" id="msgsLayout">

            <!-- ══════════════════════════════════════
                 PANEL IZQUIERDO — Lista de chats
            ══════════════════════════════════════ -->
            <aside class="msgs-sidebar" id="msgsSidebar">

                <!-- Acciones top -->
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

                <!-- Tabs de filtro -->
                <div class="msgs-tabs" role="tablist" aria-label="Filtrar conversaciones">
                    <button class="msgs-tab active" data-tab="all" role="tab" aria-selected="true">Todos</button>
                    <button class="msgs-tab" data-tab="unread" role="tab" aria-selected="false">
                        No leídos
                        @php $totalUnread = $conversations->sum('unread_count'); @endphp
                        @if($totalUnread > 0)
                        <span class="msgs-tab-badge">{{ $totalUnread > 9 ? '9+' : $totalUnread }}</span>
                        @endif
                    </button>
                </div>

                <!-- Lista de conversaciones -->
                <div class="msgs-conv-list" id="msgsConvList" role="list">

                    @forelse($conversations as $conv)
                    @php
                    $isActive = isset($activeConversation) && $activeConversation->id === $conv->id;
                    $otherChat = $conv->otherChat;
                    $lastMsg = $conv->latestMessage;
                    @endphp
                    <a href="{{ route('messages.index', ['conv' => $conv->id]) }}"
                        class="msgs-conv-item{{ $isActive ? ' active' : '' }}"
                        data-unread="{{ $conv->unread_count > 0 ? 'true' : 'false' }}"
                        data-type="individual"
                        role="listitem">
                        <div class="msgs-conv-avatar-wrap">
                            <img src="{{ $otherChat->avatar_url }}"
                                alt="{{ $otherChat->name }}" class="msgs-conv-avatar"
                                onerror="this.src='/img/default-avatar.png'">
                            {{-- Online dot se puede manejar vía JS/realtime --}}
                        </div>
                        <div class="msgs-conv-info">
                            <div class="msgs-conv-row-top">
                                <span class="msgs-conv-name">{{ $otherChat->name }}</span>
                                <span class="msgs-conv-time"
                                    data-timestamp="{{ $lastMsg ? $lastMsg->created_at->getTimestampMs() : '' }}">
                                    {{ $lastMsg ? $lastMsg->created_at->diffForHumans(null, true, true) : '' }}
                                </span>
                            </div>
                            @if($otherChat->profile?->role ?? $otherChat->profile?->headline ?? false)
                            <span class="msgs-conv-role">{{ $otherChat->profile->role ?? $otherChat->profile->headline }}</span>
                            @endif
                            <div class="msgs-conv-row-bottom">
                                <span class="msgs-conv-preview">
                                    @if($lastMsg && $lastMsg->sender_id === auth()->id())
                                    <span class="msgs-conv-preview-you">Tú: </span>
                                    @endif
                                    @if($lastMsg && $lastMsg->isMedia() && !$lastMsg->body)
                                    @if($lastMsg->isGif())GIF @elseif($lastMsg->isImage())Imagen @else Archivo @endif
                                    @else
                                    {{ $lastMsg ? Str::limit($lastMsg->body, 38) : '' }}
                                    @endif
                                </span>
                                @if($conv->unread_count > 0)
                                <span class="msgs-unread-badge">{{ $conv->unread_count }}</span>
                                @endif
                            </div>
                        </div>
                    </a>
                    @empty
                    <div class="msgs-empty-sidebar">
                        <svg width="36" height="36" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        <p>No tienes conversaciones aún.</p>
                        <button class="btn-new-msg-empty" id="btnNuevoMensajeEmpty">Iniciar chat</button>
                    </div>
                    @endforelse

                </div>

            </aside>

            @include('messages.partials.chat-panel')

        </div><!-- /msgs-layout -->
    </div><!-- /msgs-wrapper -->
</div><!-- /msgs-page -->

<!-- ── Modal: Nueva conversación ── -->
<x-modal id="msgsModalOverlay" title="Nueva conversación">
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
</x-modal>

<!-- ── Modal para vista previa de imagen en mensajes ── -->
<div class="img-modal" id="msgsImgModal">
    <div class="modal-wrap">
        <button class="modal-x" data-close="msgsImgModal">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
        <img src="" id="msgsImgModalImg" alt="Imagen" width="100" height="100" />
    </div>
</div>

<!-- ── Modal: Editar mensaje ── -->
<x-modal id="msgsEditModal" title="Editar mensaje">
    <textarea class="modal-report-textarea" id="msgsEditTextarea" rows="4" maxlength="2000" placeholder="Escribe tu mensaje..."></textarea>
    <div class="msgs-edit-charcount-wrap">
        <span class="msgs-edit-charcount" id="msgsEditCharCount">0/2000</span>
    </div>

    <x-slot:footer>
        <button class="btn btn-secondary" data-close="msgsEditModal">Cancelar</button>
        <button class="btn btn-primary" id="msgsEditSave">Guardar</button>
    </x-slot:footer>
</x-modal>

<!-- ── Modal: Compartir proyecto ── -->
<x-modal id="msgsShareModal" title="Compartir proyecto" subtitle="Selecciona un proyecto para compartir en el chat" maxWidth="sm">
    <div class="msgs-share-loading" id="msgsShareLoading">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="msgs-share-spinner">
            <circle cx="12" cy="12" r="10" stroke-dasharray="32" stroke-dashoffset="32" stroke-linecap="round" />
        </svg>
        <span>Cargando proyectos...</span>
    </div>
    <div class="msgs-share-empty" id="msgsShareEmpty" style="display:none">
        <svg width="32" height="32" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
        </svg>
        <p>No tienes proyectos aún.</p>
    </div>
    <div class="msgs-share-list" id="msgsShareList"></div>

    <x-slot:footer>
        <button class="btn btn-secondary" data-close="msgsShareModal">Cancelar</button>
        <button class="btn btn-primary" id="msgsShareSend" disabled>Enviar</button>
    </x-slot:footer>
</x-modal>

@endsection

@push('styles')
@vite('resources/css/shared/modal.css')
@vite(['resources/css/core/messages/layout.css', 'resources/css/core/messages/chat.css', 'resources/css/core/messages/modal.css', 'resources/css/core/messages/media.css', 'resources/css/core/messages/emoji-picker.css', 'resources/css/core/messages/responsive.css'])
@vite('resources/css/core/messages/giphy.css')
@endpush

@push('scripts')
@vite('resources/js/core/messages/index.js')
@endpush