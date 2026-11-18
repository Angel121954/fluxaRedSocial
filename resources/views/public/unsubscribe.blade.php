@extends('layouts.app')
@section('title', 'Baja de correos — Fluxa')
@section('meta_description', 'Gestiona tu suscripción al resumen semanal de Fluxa.')

@push('meta')
<meta name="robots" content="noindex, nofollow">
@endpush

@section('content')
<x-topbar :profile="$profile" />

<main class="unsubscribe-page">
    <div class="unsubscribe-card">
        <span class="unsubscribe-badge">Correos de Fluxa</span>

        @if ($isSubscribed)
        <h1>¿Quieres dejar de recibir el resumen semanal?</h1>
        <p>
            Dejaremos de enviarte el resumen semanal. Seguirás recibiendo los correos de
            seguridad, verificación y recuperación de contraseña.
        </p>

        <form method="POST" action="{{ $unsubscribeActionUrl }}" class="unsubscribe-actions">
            @csrf
            <button type="submit" class="unsubscribe-btn unsubscribe-btn--danger">
                Sí, darme de baja
            </button>
            <a href="{{ route('landing') }}" class="unsubscribe-btn unsubscribe-btn--ghost">
                Mantener suscripción
            </a>
        </form>
        @else
        <h1>Ya no recibes el resumen semanal</h1>
        <p>
            Tu cuenta sigue activa. Puedes volver a activar el resumen cuando quieras desde
            tus preferencias de notificaciones.
        </p>

        <a href="{{ route('landing') }}" class="unsubscribe-btn unsubscribe-btn--primary">
            Volver a Fluxa
        </a>
        @endif

        <p class="unsubscribe-note">
            Puedes cambiar esta preferencia en cualquier momento desde
            <a href="{{ route('notification-preference.index') }}">tu configuración</a>.
        </p>
    </div>
</main>
@endsection

@push('styles')
@vite('resources/css/public/unsubscribe.css')
@endpush
