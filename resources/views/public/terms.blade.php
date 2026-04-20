@extends('layouts.app')
@section('title', 'Términos y Condiciones — Fluxa')

@section('content')
<x-topbar :profile="$profile ?? null" />

{{-- ══════════════════════════════════════════
     HERO
════════════════════════════════════════════ --}}
<section class="terms-hero">
    <div class="terms-hero-inner">
        <span class="terms-badge">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
            Legal
        </span>
        <h1>Términos y <span class="terms-accent">Condiciones</span></h1>
        <p class="terms-hero-sub">
            Al usar Fluxa, aceptas estos términos. Te recomendamos leerlos con calma.
        </p>
        <p class="terms-date">Última actualización: {{ \Carbon\Carbon::now()->translatedFormat('d \d\e F \d\e Y') }}</p>
    </div>
</section>

{{-- ══════════════════════════════════════════
     LAYOUT: ÍNDICE + CONTENIDO
════════════════════════════════════════════ --}}
<div class="terms-layout">

    {{-- Sidebar índice --}}
    <aside class="terms-nav" id="termsNav">
        <p class="terms-nav-title">Contenido</p>
        <ol class="terms-nav-list">
            <li><a href="#s1">Aceptación</a></li>
            <li><a href="#s2">Descripción del servicio</a></li>
            <li><a href="#s3">Registro y cuenta</a></li>
            <li><a href="#s4">Conducta del usuario</a></li>
            <li><a href="#s5">Contenido publicado</a></li>
            <li><a href="#s6">Propiedad intelectual</a></li>
            <li><a href="#s7">Privacidad</a></li>
            <li><a href="#s8">Suspensión y terminación</a></li>
            <li><a href="#s9">Limitación de responsabilidad</a></li>
            <li><a href="#s10">Modificaciones</a></li>
            <li><a href="#s11">Contacto</a></li>
        </ol>
    </aside>

    {{-- Contenido principal --}}
    <main class="terms-content">

        <section class="terms-section" id="s1">
            <div class="terms-section-header">
                <span class="terms-number">01</span>
                <h2>Aceptación de los términos</h2>
            </div>
            <p>
                Al acceder o utilizar la plataforma Fluxa, disponible en
                <a href="{{ config('app.url') }}" class="terms-link">{{ config('app.url') }}</a>,
                confirmas que has leído, entendido y aceptas estar vinculado por estos Términos y Condiciones.
            </p>
            <p>
                Si accedes a Fluxa en nombre de una organización, declaras que tienes autoridad
                para aceptar estos términos en nombre de dicha organización.
            </p>
            <div class="terms-callout">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Si no estás de acuerdo con alguno de estos términos, por favor deja de usar la plataforma.
            </div>
        </section>

        <section class="terms-section" id="s2">
            <div class="terms-section-header">
                <span class="terms-number">02</span>
                <h2>Descripción del servicio</h2>
            </div>
            <p>
                Fluxa es una red social para desarrolladores latinoamericanos que permite compartir
                proyectos, progreso profesional y conocimiento técnico con una comunidad activa.
            </p>
            <p>El servicio incluye, entre otras funcionalidades:</p>
            <ul class="terms-list">
                <li>Publicación de proyectos e historias de progreso</li>
                <li>Perfil profesional con portafolio y stack tecnológico</li>
                <li>Sistema de likes, comentarios y endorsements</li>
                <li>Exportación de CV en formato PDF</li>
                <li>Exploración y descubrimiento de otros desarrolladores</li>
            </ul>
            <p>
                Nos reservamos el derecho de modificar, suspender o discontinuar cualquier
                funcionalidad en cualquier momento, con o sin previo aviso.
            </p>
        </section>

        <section class="terms-section" id="s3">
            <div class="terms-section-header">
                <span class="terms-number">03</span>
                <h2>Registro y cuenta</h2>
            </div>
            <p>
                Para acceder a las funcionalidades de Fluxa debes crear una cuenta proporcionando
                información verídica, precisa y actualizada. Eres responsable de:
            </p>
            <ul class="terms-list">
                <li>Mantener la confidencialidad de tu contraseña</li>
                <li>Todas las actividades que ocurran bajo tu cuenta</li>
                <li>Notificarnos de inmediato ante cualquier uso no autorizado</li>
                <li>No compartir tu cuenta con terceros</li>
            </ul>
            <p>
                Fluxa permite el registro mediante cuenta de Google. En ese caso, los datos de
                autenticación son gestionados por Google conforme a sus propias políticas de privacidad.
            </p>
            <div class="terms-callout terms-callout--warn">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                Está prohibido crear cuentas falsas, suplantar identidades o automatizar el registro.
            </div>
        </section>

        <section class="terms-section" id="s4">
            <div class="terms-section-header">
                <span class="terms-number">04</span>
                <h2>Conducta del usuario</h2>
            </div>
            <p>Al usar Fluxa te comprometes a no:</p>
            <ul class="terms-list">
                <li>Publicar contenido ofensivo, discriminatorio o que incite al odio</li>
                <li>Acosar, amenazar o intimidar a otros usuarios</li>
                <li>Difundir spam, publicidad no solicitada o contenido engañoso</li>
                <li>Intentar acceder sin autorización a sistemas o cuentas de terceros</li>
                <li>Usar la plataforma para actividades ilegales</li>
                <li>Scraping masivo de datos sin consentimiento previo por escrito</li>
                <li>Publicar contenido que infrinja derechos de propiedad intelectual de terceros</li>
            </ul>
            <p>
                Fluxa se reserva el derecho de eliminar contenido y suspender cuentas que
                incumplan estas normas sin previo aviso.
            </p>
        </section>

        <section class="terms-section" id="s5">
            <div class="terms-section-header">
                <span class="terms-number">05</span>
                <h2>Contenido publicado</h2>
            </div>
            <p>
                Al publicar contenido en Fluxa, conservas la propiedad intelectual sobre el mismo
                y nos otorgas una licencia mundial, no exclusiva, libre de regalías para:
            </p>
            <ul class="terms-list">
                <li>Mostrar y distribuir tu contenido dentro de la plataforma</li>
                <li>Indexarlo en buscadores internos</li>
                <li>Adaptarlo a diferentes formatos o dispositivos para su correcta visualización</li>
            </ul>
            <p>
                Esta licencia termina cuando eliminas tu contenido o cierras tu cuenta,
                salvo que haya sido compartido por otros usuarios.
            </p>
            <p>
                Eres el único responsable del contenido que publicas. Fluxa actúa como
                plataforma intermediaria y no valida ni garantiza la veracidad de los proyectos publicados.
            </p>
        </section>

        <section class="terms-section" id="s6">
            <div class="terms-section-header">
                <span class="terms-number">06</span>
                <h2>Propiedad intelectual</h2>
            </div>
            <p>
                Todos los elementos de Fluxa — incluyendo diseño, logo, código fuente de la plataforma,
                marca y experiencia de usuario — son propiedad exclusiva de Fluxa y están protegidos
                por las leyes de propiedad intelectual aplicables.
            </p>
            <p>
                Queda estrictamente prohibido reproducir, copiar, distribuir o crear obras derivadas
                de cualquier elemento de la plataforma sin autorización expresa y por escrito.
            </p>
        </section>

        <section class="terms-section" id="s7">
            <div class="terms-section-header">
                <span class="terms-number">07</span>
                <h2>Privacidad</h2>
            </div>
            <p>
                El tratamiento de tus datos personales se rige por nuestra
                <a href="#" class="terms-link">Política de Privacidad</a>,
                la cual forma parte integral de estos términos. Al aceptar estos términos,
                también aceptas nuestra política de privacidad.
            </p>
            <p>
                Fluxa no vende ni cede datos personales a terceros con fines comerciales.
                Los datos recopilados se utilizan exclusivamente para mejorar la experiencia
                dentro de la plataforma.
            </p>
        </section>

        <section class="terms-section" id="s8">
            <div class="terms-section-header">
                <span class="terms-number">08</span>
                <h2>Suspensión y terminación</h2>
            </div>
            <p>
                Puedes eliminar tu cuenta en cualquier momento desde la sección de
                <a href="{{ route('account.index') }}" class="terms-link">configuración de cuenta</a>.
                Al hacerlo, tus datos personales serán eliminados conforme a nuestra política de privacidad.
            </p>
            <p>Fluxa puede suspender o eliminar tu cuenta si:</p>
            <ul class="terms-list">
                <li>Incumples estos términos o nuestras normas de comunidad</li>
                <li>Tu actividad representa un riesgo para otros usuarios o la plataforma</li>
                <li>Se detecta actividad fraudulenta o uso automatizado no autorizado</li>
            </ul>
        </section>

        <section class="terms-section" id="s9">
            <div class="terms-section-header">
                <span class="terms-number">09</span>
                <h2>Limitación de responsabilidad</h2>
            </div>
            <p>
                Fluxa se proporciona "tal como está" sin garantías de ningún tipo, expresas o implícitas.
                No garantizamos que el servicio sea ininterrumpido, libre de errores o completamente seguro.
            </p>
            <p>En la medida permitida por la ley, Fluxa no será responsable por:</p>
            <ul class="terms-list">
                <li>Pérdida de datos o contenido publicado</li>
                <li>Daños derivados del uso o imposibilidad de uso de la plataforma</li>
                <li>Conductas de terceros o de otros usuarios dentro de la plataforma</li>
                <li>Interrupciones del servicio por causas ajenas a nuestro control</li>
            </ul>
        </section>

        <section class="terms-section" id="s10">
            <div class="terms-section-header">
                <span class="terms-number">10</span>
                <h2>Modificaciones</h2>
            </div>
            <p>
                Nos reservamos el derecho de actualizar estos Términos y Condiciones en cualquier momento.
                Los cambios sustanciales serán notificados a través de la plataforma con al menos
                <strong>7 días de anticipación</strong>.
            </p>
            <p>
                El uso continuado de Fluxa después de la entrada en vigencia de los cambios
                constituye tu aceptación de los nuevos términos.
            </p>
        </section>

        <section class="terms-section" id="s11">
            <div class="terms-section-header">
                <span class="terms-number">11</span>
                <h2>Contacto</h2>
            </div>
            <p>
                Si tienes preguntas, dudas o inquietudes sobre estos términos, puedes contactarnos a través de:
            </p>
            <div class="terms-contact-grid">
                <a href="mailto:angeldavidagudelocuartas13@gmail.com" class="terms-contact-card">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <span>Correo electrónico</span>
                    <small>angeldavidagudelocuartas13@gmail.com</small>
                </a>
                <a href="{{ route('suggestions.create') }}" class="terms-contact-card">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    <span>Sugerencias</span>
                    <small>Formulario dentro de la plataforma</small>
                </a>
            </div>
        </section>

    </main>
</div>

<x-footer />
@endsection

@push('styles')
@vite('resources/css/public/terms.css')
@endpush