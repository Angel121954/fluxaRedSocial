{{--
    resources/views/livewire/messages/messages-page.blade.php

    ESTRUCTURA CLAVE:
    ─ Un único elemento raíz <div> para Livewire (requerimiento de morfdom)
    ─ El modal vive DENTRO del elemento raíz para que Livewire lo controle
    ─ @script al final → Alpine.data + bridge Echo
--}}

{{-- ══════════════════════════════════════════════════════════
     RAÍZ ÚNICA DEL COMPONENTE LIVEWIRE
     Todo (topbar, layout, modal) va dentro de este div.
══════════════════════════════════════════════════════════ --}}
<div
    class="msgs-root"
    x-data="msgsEcho()">
    {{-- Topbar (estático, no necesita ser reactivo) --}}
    <x-topbar :profile="$profile" />

    {{-- Wrapper de página (igual que el resto de la app) --}}
    <div class="msgs-page">
        <div class="msgs-wrapper">

            <div class="msgs-layout {{ $activeConversationId ? 'msgs-layout--active' : '' }}">

                {{-- ══════════════════════════════════════════════════════
                     SIDEBAR — lista de conversaciones
                ══════════════════════════════════════════════════════ --}}
                <aside class="msgs-sidebar">

                    <div class="msgs-sidebar-top">
                        <h2 class="msgs-title">Mensajes</h2>
                        <button
                            wire:click="openModal"
                            class="btn-new-msg"
                            title="Nueva conversación">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                        </button>
                    </div>

                    <div class="msgs-search-wrap">
                        <svg class="msgs-search-icon" xmlns="http://www.w3.org/2000/svg" width="13" height="13"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input
                            id="msgsSearch"
                            type="text"
                            wire:model.live.debounce.300ms="searchQuery"
                            placeholder="Buscar conversación…"
                            class="msgs-search"
                            autocomplete="off">
                    </div>

                    <ul id="msgsConvList" class="msgs-conv-list">

                        @forelse ($conversations as $conv)
                        @php
                        $other = ($conv['user_a_id'] == auth()->id()) ? $conv['user_b'] : $conv['user_a'];
                        $isActive = $activeConversationId == $conv['id'];
                        $avatar = $other['profile']['avatar'] ?? $other['avatar_url'];
                        $name = $other['profile']['name'] ?? $other['name'];
                        $username = $other['profile']['username'] ?? $other['username'];
                        @endphp

                        <li class="msgs-conv-item-wrapper {{ $isActive ? 'active' : '' }}">
                            <a
                                wire:click="selectConversation({{ $conv['id'] }})"
                                class="msgs-conv-item {{ $isActive ? 'active' : '' }}">
                                <div class="msgs-conv-avatar-wrap">
                                    <img
                                        src="{{ $avatar ?? asset('img/default-avatar.png') }}"
                                        alt="{{ $name }}"
                                        class="msgs-conv-avatar">
                                    @if ($other['online'] ?? false)
                                    <span class="msgs-online-dot"></span>
                                    @endif
                                </div>
                                <div class="msgs-conv-info">
                                    <div class="msgs-conv-row-top">
                                        <span class="msgs-conv-name">{{ $name }}</span>
                                        <span class="msgs-conv-time">{{ $conv['last_message_time'] ?? '' }}</span>
                                    </div>
                                    <div class="msgs-conv-row-bottom">
                                        <span class="msgs-conv-preview">
                                            {{ $conv['last_message_sender_id'] == auth()->id() ? 'Tú: ' : '' }}{{ Str::limit($conv['last_message'] ?? 'Sin mensajes', 40) }}
                                        </span>
                                        @if ($conv['unread_count'] > 0)
                                        <span class="msgs-unread-badge">{{ $conv['unread_count'] }}</span>
                                        @endif
                                    </div>
                                </div>
                            </a>

                            <div class="msgs-conv-menu" x-data="{ open: false }" @click.outside="open = false" style="display: none;">
                                <button class="msgs-conv-menu-btn" @click.stop="open = !open; $el.nextElementSibling.style.display = open ? 'block' : 'none'" title="Opciones">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        fill="currentColor" viewBox="0 0 16 16">
                                        <circle cx="8" cy="3" r="1.5" />
                                        <circle cx="8" cy="8" r="1.5" />
                                        <circle cx="8" cy="13" r="1.5" />
                                    </svg>
                                </button>
                                <ul class="msgs-conv-menu" x-show="open" x-cloak style="display: none;">
                                    <li>
                                        <button
                                            class="msgs-conv-menu-item danger"
                                            wire:click="deleteConversation({{ $conv['id'] }})"
                                            wire:confirm="¿Eliminar esta conversación?"
                                            @click="open = false">
                                            Eliminar
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </li>

                        @empty
                        <li class="msgs-empty-sidebar">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9
                                             8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512
                                             15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            <p>No tienes conversaciones aún.</p>
                            <button wire:click="openModal" class="btn-new-msg-empty">
                                Escríbele a alguien
                            </button>
                        </li>
                        @endforelse

                    </ul>
                </aside>

                {{-- ══════════════════════════════════════════════════════
                     PANEL DE CHAT
                ══════════════════════════════════════════════════════ --}}
                <section class="msgs-chat">

                    @if ($activeConversationId && $otherUser)

                    <header class="msgs-chat-header">
                        <button
                            id="msgsBackBtn"
                            class="msgs-back-btn"
                            wire:click="$set('activeConversationId', null)">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>

                        <a href="{{ route('profile.show', $otherUser['username']) }}" class="msgs-chat-header-user">
                            <div class="msgs-chat-header-avatar-wrap">
                                <img
                                    src="{{ $otherUser['profile']['avatar'] ?? $otherUser['avatar_url'] }}"
                                    alt="{{ $otherUser['profile']['name'] }}"
                                    class="msgs-chat-header-avatar">
                            </div>
                            <div class="msgs-chat-header-info">
                                <span class="msgs-chat-header-name">{{ $otherUser['profile']['name'] }}</span>
                                <span class="msgs-chat-header-sub">
                                    @if (($otherUser['online'] ?? false))
                                    <span class="msgs-online-label">En línea</span>
                                    @else
                                    Última vez {{ $otherUser['last_seen'] ?? '' }}
                                    @endif
                                </span>
                            </div>
                        </a>
                    </header>

                    <div id="msgsBubbleList" class="msgs-bubble-list">

                        @foreach ($messages as $msg)
                        @php
                        $isMine = $msg['sender_id'] == auth()->id();
                        $time = \Carbon\Carbon::parse($msg['created_at'])
                        ->timezone('America/Bogota')
                        ->format('H:i');
                        $isRead = $msg['read_at'] ?? false;
                        @endphp

                        <div class="msgs-bubble-wrap {{ $isMine ? 'mine' : 'theirs' }}">

                            @unless ($isMine)
                            <img
                                src="{{ $msg['sender']['profile']['avatar']
                                                    ?? $msg['sender']['avatar_url']
                                                    ?? asset('img/default-avatar.png') }}"
                                alt="{{ $msg['sender']['name'] }}"
                                class="msgs-bubble-avatar">
                            @endunless

                            <div class="msgs-bubble {{ $isMine ? 'msgs-bubble-mine' : 'msgs-bubble-theirs' }}">
                                {{ $msg['body'] }}
                                <span class="msgs-bubble-time">
                                    {{ $time }}
                                    @if ($isMine)
                                    <span class="msgs-double-check">
                                        @if ($isRead)
                                        <svg class="msgs-read-check" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                        <svg class="msgs-read-check" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                        @else
                                        <svg class="msgs-sent-check" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                        @endif
                                    </span>
                                    @endif
                                </span>
                            </div>

                        </div>
                        @endforeach

                    </div>

                    <footer class="msgs-input-bar">
                        <div class="msgs-input-wrap">
                            <button class="msgs-attach-btn" title="Adjuntar archivo">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                </svg>
                            </button>
                            <textarea
                                id="msgsInput"
                                wire:model="newMessage"
                                wire:keydown.enter.prevent="sendMessage"
                                placeholder="Escribe un mensaje…"
                                class="msgs-input"
                                rows="1"
                                x-data
                                x-on:input="
                                    $el.style.height = 'auto';
                                    $el.style.height = Math.min($el.scrollHeight, 140) + 'px';
                                "></textarea>
                        </div>

                        <button
                            id="msgsSendBtn"
                            wire:click="sendMessage"
                            wire:loading.attr="disabled"
                            wire:target="sendMessage"
                            class="msgs-send-btn"
                            :disabled="$wire.newMessage.trim().length === 0">
                            <span wire:loading.remove wire:target="sendMessage">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z" />
                                </svg>
                            </span>
                            <span wire:loading wire:target="sendMessage">
                                <svg class="animate-spin" xmlns="http://www.w3.org/2000/svg"
                                    width="18" height="18" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4" />
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z" />
                                </svg>
                            </span>
                        </button>
                    </footer>

                    @else

                    <div class="msgs-empty-state">
                        <div class="msgs-empty-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9
                                             8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512
                                             15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                        </div>
                        <div class="msgs-empty-text">
                            <p class="msgs-empty-label">Selecciona una conversación</p>
                            <p class="msgs-empty-sublabel">O inicia una nueva</p>
                        </div>
                        <button id="btnNuevoMensajeEmpty" wire:click="openModal" class="btn-new-msg-empty">
                            Nueva conversación
                        </button>
                    </div>

                    @endif

                </section>

            </div>{{-- .msgs-layout --}}
        </div>{{-- .msgs-wrapper --}}
    </div>{{-- .msgs-page --}}

    {{-- ══════════════════════════════════════════════════════
         MODAL — nuevo mensaje
         Está DENTRO del root div para que Livewire lo controle.
         position: fixed en CSS lo saca del flujo normal.
    ══════════════════════════════════════════════════════ --}}
    @if ($modalOpen)
    <div
        id="msgsModalOverlay"
        class="msgs-modal-overlay"
        wire:click.self="closeModal"
        x-data
        x-init="$nextTick(() => $refs.modalInput?.focus())">
        <div class="msgs-modal">
            <div class="msgs-modal-header">
                <h3 class="msgs-modal-title">Nuevo mensaje</h3>
                <button id="msgsModalClose" wire:click="closeModal" class="msgs-modal-close">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="msgs-modal-search-wrap">
                <svg class="msgs-modal-search-icon" xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input
                    id="msgsModalSearch"
                    x-ref="modalInput"
                    type="text"
                    wire:model.live.debounce.350ms="modalSearch"
                    placeholder="Buscar usuario…"
                    class="msgs-modal-search"
                    autocomplete="off">
            </div>

            <ul id="msgsModalResults" class="msgs-modal-results">
                @if (strlen(trim($modalSearch)) < 2)
                    <li class="msgs-modal-hint">Empieza a escribir para buscar usuarios.</li>
                    @elseif (count($modalResults) === 0)
                    <li class="msgs-modal-hint">No se encontraron usuarios.</li>
                    @else
                    @foreach ($modalResults as $u)
                    <li>
                        <button wire:click="startConversation({{ $u['id'] }})" class="msgs-modal-user-item">
                            <img
                                src="{{ $u['profile']['avatar'] ?? $u['avatar_url'] }}"
                                alt="{{ $u['profile']['name'] }}"
                                class="msgs-modal-user-avatar">
                            <div class="msgs-modal-user-info">
                                <span class="msgs-modal-user-name">{{ $u['profile']['name'] }}</span>
                                <span class="msgs-modal-user-handle">@{{ $u['profile']['username'] }}</span>
                            </div>
                        </button>
                    </li>
                    @endforeach
                    @endif
            </ul>
        </div>
    </div>
    @endif

</div>{{-- .msgs-root (raíz única del componente Livewire) --}}

{{-- ══════════════════════════════════════════════════════════
     BRIDGE Echo → Livewire
     @script vive fuera del root div — Livewire lo maneja aparte.
══════════════════════════════════════════════════════════ --}}
@script
<script>
    Alpine.data('msgsEcho', () => ({
        activeChannel: null,

        init() {
            // Observar cambio de conversación activa → re-suscribir canal
            $wire.$watch('activeConversationId', (id) => {
                this.subscribeToChannel(id);
            });

            // Suscribirse si ya hay una conversación activa al cargar
            if ($wire.activeConversationId) {
                this.subscribeToChannel($wire.activeConversationId);
            }

            // Scroll automático
            $wire.on('message-sent', () => this.scrollToBottom(true));
            $wire.on('message-received', () => this.scrollToBottom(true));

            // Scroll inicial
            this.$nextTick(() => this.scrollToBottom(false));
        },

        subscribeToChannel(id) {
            if (this.activeChannel) {
                window.Echo.leave(this.activeChannel);
                this.activeChannel = null;
            }

            if (!id) return;

            this.activeChannel = `conversation.${id}`;

            window.Echo.private(this.activeChannel)
                .listen('NewMessageEvent', (e) => {
                    if (e.message) {
                        $wire.receiveMessage(e.message);
                    }
                });
        },

        scrollToBottom(smooth = false) {
            const list = document.getElementById('msgsBubbleList');
            if (!list) return;
            list.scrollTo({
                top: list.scrollHeight,
                behavior: smooth ? 'smooth' : 'instant',
            });
        },
    }));
</script>
@endscript

@push('styles')
@vite('resources/css/core/messages.css')
@endpush

@push('scripts')
@vite('resources/js/core/messages/index.js')
@endpush