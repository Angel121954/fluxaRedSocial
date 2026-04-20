@extends('layouts.app')
@section('title', 'Contacto — Fluxa')

@section('content')
<x-topbar :profile="$profile ?? null" />

{{-- ══════════════════════════════════════════
     CONTACT SECTION
════════════════════════════════════════════ --}}
<section class="contact-section">
    <div class="contact-wrapper">

        {{-- ── Columna izquierda: info ── --}}
        <div class="contact-info">

            <a href="{{ route('explore.index') }}" class="contact-brand" aria-label="Volver a inicio">
                <img src="{{ asset('img/logoFluxa.png') }}" alt="Fluxa" class="contact-logo" />
            </a>

            <h1 class="contact-title">Contáctanos</h1>

            <p class="contact-description">
                ¿Tienes preguntas o comentarios?<br>
                No dudes en ponerte en contacto con nosotros.
            </p>

            <div class="contact-channels">
                <a href="mailto:angeldavidagudelocuartas13@gmail.com" class="contact-channel">
                    <span class="contact-channel-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="4" width="20" height="16" rx="3" />
                            <path d="m2 7 10 7 10-7" />
                        </svg>
                    </span>
                    <span>angeldavidagudelocuartas13@gmail.com</span>
                </a>
                <a href="tel:+573046363941" class="contact-channel">
                    <span class="contact-channel-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13.5a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.6 2.69l3-.01a2 2 0 0 1 2 1.72c.13.96.36 1.9.68 2.81a2 2 0 0 1-.45 2.11L7.91 9.22a16 16 0 0 0 6.29 6.29l1.61-1.61a2 2 0 0 1 2.11-.45c.91.32 1.85.55 2.81.68a2 2 0 0 1 1.72 2.02z" />
                        </svg>
                    </span>
                    <span>+57 304 636 3941</span>
                </a>
            </div>

            <div class="contact-divider"></div>

            <div class="contact-socials">
                {{-- Twitter / X --}}
                <a href="https://x.com/AngelDavidAgude" class="contact-social-link" aria-label="Twitter" target="_blank" rel="noopener noreferrer">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
                    </svg>
                </a>
                {{-- Facebook --}}
                <a href="https://www.facebook.com/angeldavid.agudelocuartas.3" class="contact-social-link" aria-label="Facebook" target="_blank" rel="noopener noreferrer">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                    </svg>
                </a>
                {{-- YouTube --}}
                <a href="https://www.youtube.com/@angeldavidagudelocuartas4224" class="contact-social-link" aria-label="YouTube" target="_blank" rel="noopener noreferrer">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z" />
                    </svg>
                </a>
                {{-- GitHub --}}
                <a href="https://github.com/Angel121954" class="contact-social-link" aria-label="GitHub" target="_blank" rel="noopener noreferrer">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 0C5.37 0 0 5.37 0 12c0 5.31 3.435 9.795 8.205 11.385.6.105.825-.255.825-.57 0-.285-.015-1.23-.015-2.235-3.015.555-3.795-.735-4.035-1.41-.135-.345-.72-1.41-1.23-1.695-.42-.225-1.02-.78-.015-.795.945-.015 1.62.87 1.845 1.23 1.08 1.815 2.805 1.305 3.495.99.105-.78.42-1.305.765-1.605-2.67-.3-5.46-1.335-5.46-5.925 0-1.305.465-2.385 1.23-3.225-.12-.3-.54-1.53.12-3.18 0 0 1.005-.315 3.3 1.23.96-.27 1.98-.405 3-.405s2.04.135 3 .405c2.295-1.56 3.3-1.23 3.3-1.23.66 1.65.24 2.88.12 3.18.765.84 1.23 1.905 1.23 3.225 0 4.605-2.805 5.625-5.475 5.925.435.375.81 1.095.81 2.22 0 1.605-.015 2.895-.015 3.3 0 .315.225.69.825.57A12.02 12.02 0 0 0 24 12c0-6.63-5.37-12-12-12z" />
                    </svg>
                </a>
            </div>
        </div>

        {{-- ── Columna derecha: ilustración + formulario ── --}}
        <div class="contact-right">

            {{-- Ilustración decorativa --}}
            <div class="contact-illustration-wrap">
                <div class="contact-blob"></div>
                <svg class="contact-illustration" viewBox="0 0 340 260" fill="none" xmlns="http://www.w3.org/2000/svg">
                    {{-- Laptop body --}}
                    <rect x="90" y="120" width="160" height="100" rx="8" fill="#e8f9f9" stroke="#12b3b6" stroke-width="2" />
                    <rect x="98" y="128" width="144" height="78" rx="4" fill="#cdf0f0" />
                    {{-- Laptop base --}}
                    <rect x="70" y="220" width="200" height="10" rx="5" fill="#b2e5e5" />
                    <rect x="110" y="218" width="120" height="4" rx="2" fill="#90d8d8" />

                    {{-- Code lines on screen --}}
                    <rect x="106" y="138" width="55" height="5" rx="2.5" fill="#12b3b6" opacity=".7" />
                    <rect x="106" y="148" width="80" height="5" rx="2.5" fill="#12b3b6" opacity=".4" />
                    <rect x="106" y="158" width="65" height="5" rx="2.5" fill="#12b3b6" opacity=".5" />
                    <rect x="106" y="168" width="90" height="5" rx="2.5" fill="#12b3b6" opacity=".35" />
                    <rect x="106" y="178" width="50" height="5" rx="2.5" fill="#12b3b6" opacity=".6" />
                    <rect x="106" y="188" width="70" height="5" rx="2.5" fill="#12b3b6" opacity=".3" />

                    {{-- Person body --}}
                    <ellipse cx="170" cy="230" rx="30" ry="8" fill="#b2e5e5" opacity=".5" />
                    <rect x="148" y="150" width="44" height="70" rx="12" fill="#12b3b6" />
                    {{-- Jacket lapels --}}
                    <path d="M148 165 L162 175 L170 155" stroke="#0d8e91" stroke-width="2" fill="none" />
                    <path d="M192 165 L178 175 L170 155" stroke="#0d8e91" stroke-width="2" fill="none" />
                    {{-- Arms --}}
                    <path d="M148 165 Q128 175 120 190 Q118 198 125 200" stroke="#f5c5a3" stroke-width="14" stroke-linecap="round" fill="none" />
                    <path d="M192 165 Q212 175 220 190 Q222 198 215 200" stroke="#f5c5a3" stroke-width="14" stroke-linecap="round" fill="none" />
                    {{-- Hands on keyboard --}}
                    <ellipse cx="125" cy="202" rx="10" ry="7" fill="#f5c5a3" />
                    <ellipse cx="215" cy="202" rx="10" ry="7" fill="#f5c5a3" />

                    {{-- Head --}}
                    <ellipse cx="170" cy="130" rx="26" ry="28" fill="#f5c5a3" />
                    {{-- Hair --}}
                    <path d="M144 122 Q148 96 170 94 Q192 96 196 122 Q190 105 170 103 Q150 105 144 122Z" fill="#3d2b1f" />
                    <path d="M144 122 Q140 135 143 148 Q144 120 148 112Z" fill="#3d2b1f" />
                    <path d="M196 122 Q200 135 197 148 Q196 120 192 112Z" fill="#3d2b1f" />

                    {{-- Face details --}}
                    <ellipse cx="162" cy="133" rx="3" ry="3.5" fill="#3d2b1f" opacity=".8" />
                    <ellipse cx="178" cy="133" rx="3" ry="3.5" fill="#3d2b1f" opacity=".8" />
                    <path d="M163 143 Q170 149 177 143" stroke="#c97878" stroke-width="2" stroke-linecap="round" fill="none" />

                    {{-- Headset --}}
                    <path d="M144 118 Q144 94 170 94 Q196 94 196 118" stroke="#3d2b1f" stroke-width="3" fill="none" stroke-linecap="round" />
                    <rect x="138" y="115" width="10" height="16" rx="5" fill="#12b3b6" />
                    <rect x="192" y="115" width="10" height="16" rx="5" fill="#12b3b6" />
                    {{-- Mic --}}
                    <path d="M138 128 Q130 132 132 140" stroke="#12b3b6" stroke-width="2.5" stroke-linecap="round" fill="none" />
                    <ellipse cx="133" cy="143" rx="5" ry="4" fill="#12b3b6" />

                    {{-- Chat bubble --}}
                    <rect x="210" y="80" width="90" height="48" rx="10" fill="white" stroke="#12b3b6" stroke-width="1.5" />
                    <path d="M215 128 L208 140 L228 128Z" fill="white" stroke="#12b3b6" stroke-width="1.5" />
                    {{-- Dots in bubble --}}
                    <circle cx="232" cy="104" r="5" fill="#12b3b6" opacity=".4" />
                    <circle cx="250" cy="104" r="5" fill="#12b3b6" opacity=".65" />
                    <circle cx="268" cy="104" r="5" fill="#12b3b6" />

                    {{-- Plant --}}
                    <rect x="288" y="185" width="14" height="30" rx="4" fill="#b2e5e5" />
                    <ellipse cx="295" cy="168" rx="18" ry="22" fill="#5cb85c" opacity=".7" />
                    <ellipse cx="278" cy="175" rx="14" ry="18" fill="#4caf50" opacity=".65" />
                    <ellipse cx="310" cy="178" rx="12" ry="16" fill="#66bb6a" opacity=".6" />
                </svg>
            </div>

            {{-- Formulario --}}
            <div class="contact-form-card">
                @if(session('success'))
                <div class="contact-alert contact-alert--success">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
                @endif

                <form action="{{ route('contact.store') }}" method="POST" class="contact-form" novalidate>
                    @csrf

                    <div class="contact-form-group">
                        <input
                            type="text"
                            id="name"
                            name="name"
                            class="contact-input @error('name') contact-input--error @enderror"
                            placeholder="Tu nombre"
                            value="{{ old('name') }}"
                            autocomplete="name"
                            required />
                        @error('name')
                        <span class="contact-field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="contact-form-group">
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="contact-input @error('email') contact-input--error @enderror"
                            placeholder="Tu correo electrónico"
                            value="{{ old('email') }}"
                            autocomplete="email"
                            required />
                        @error('email')
                        <span class="contact-field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="contact-form-group">
                        <textarea
                            id="message"
                            name="message"
                            class="contact-textarea @error('message') contact-input--error @enderror"
                            placeholder="Tu mensaje"
                            rows="5"
                            required>{{ old('message') }}</textarea>
                        @error('message')
                        <span class="contact-field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" class="contact-btn-submit">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="22" y1="2" x2="11" y2="13" />
                            <polygon points="22 2 15 22 11 13 2 9 22 2" />
                        </svg>
                        Enviar mensaje
                    </button>
                </form>
            </div>
        </div>

    </div>
</section>

<x-footer />
@endsection

@push('styles')
@vite('resources/css/public/contact.css')
@endpush