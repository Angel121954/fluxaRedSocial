@extends('layouts.app')
@section('content')

<div class="login-screen">
    <div class="login-wrapper">

        <div class="login-card">

            <div class="login-brand">
                <x-header />
            </div>

            <h1 class="login-title">Confirma tu contraseña</h1>
            <p class="login-subtitle">
                Esta es un área segura de la aplicación. Por favor, confirma tu contraseña antes de continuar.
            </p>

            <form method="POST" action="{{ route('password.confirm') }}">
                @csrf

                <div class="input-group">
                    <label for="password" class="input-label">Contraseña</label>
                    <div class="input-wrapper">
                        <svg class="input-icon" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            class="input-field @error('password') error @enderror"
                            placeholder="••••••••"
                            autocomplete="current-password"
                            required />
                        <button type="button" class="toggle-password" aria-label="Mostrar contraseña">
                            <svg class="eye-open" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg class="eye-closed" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88L6.59 6.59m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            </svg>
                        </button>
                    </div>
                    @error('password')
                    <div class="error-message show">
                        <svg width="13" height="13" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                            <path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z" />
                        </svg>
                        <span>{{ $message }}</span>
                    </div>
                    @enderror
                </div>

                <button type="submit" class="btn-submit" style="margin-top:0.5rem">
                    <span class="loader"></span>
                    <span class="btn-text">Confirmar</span>
                </button>
            </form>

        </div>

        <p class="login-footer">© {{ date('Y') }} Fluxa. Todos los derechos reservados.</p>

    </div>
</div>

@endsection

@push('styles')
@vite('resources/css/auth/login.css')
@endpush

@push('scripts')
@vite('resources/js/shared/passwordVisibility.js')
@endpush
