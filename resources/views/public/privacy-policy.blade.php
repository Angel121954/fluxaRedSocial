@extends('layouts.app')
@section('title', 'Política de Privacidad — Fluxa')

@section('content')
<x-topbar :profile="$profile ?? null" />

{{-- ══════════════════════════════════════════
     HERO
════════════════════════════════════════════ --}}
<section class="privacy-hero">
    <div class="privacy-hero-inner">
        <span class="privacy-badge">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
            Privacidad
        </span>
        <h1>Tu privacidad <span class="privacy-accent">importa</span></h1>
        <p class="privacy-hero-sub">
            Aquí explicamos qué datos recopilamos, para qué los usamos y cómo los protegemos.
            Sin letra pequeña.
        </p>
        <p class="privacy-date">Última actualización: {{ \Carbon\Carbon::now()->translatedFormat('d \d\e F \d\e Y') }}</p>
    </div>
</section>

{{-- ══════════════════════════════════════════
     RESUMEN RÁPIDO (cards visuales)
════════════════════════════════════════════ --}}
<section class="privacy-summary">
    <div class="privacy-summary-inner">
        <p class="privacy-summary-label">Resumen rápido</p>
        <div class="privacy-summary-grid">
            <div class="privacy-sum-card">
                <div class="privacy-sum-icon privacy-sum-icon--green">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <p><strong>No vendemos</strong> tus datos a nadie</p>
            </div>
            <div class="privacy-sum-card">
                <div class="privacy-sum-icon privacy-sum-icon--blue">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </div>
                <p>Controlas <strong>qué es visible</strong> en tu perfil</p>
            </div>
            <div class="privacy-sum-card">
                <div class="privacy-sum-icon privacy-sum-icon--teal">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </div>
                <p>Puedes <strong>eliminar tu cuenta</strong> cuando quieras</p>
            </div>
            <div class="privacy-sum-card">
                <div class="privacy-sum-icon privacy-sum-icon--purple">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <p>Tus contraseñas se guardan <strong>cifradas</strong></p>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════
     LAYOUT: ÍNDICE + CONTENIDO
════════════════════════════════════════════ --}}
<div class="privacy-layout">

    {{-- Sidebar índice --}}
    <aside class="privacy-nav">
        <p class="privacy-nav-title">Contenido</p>
        <ol class="privacy-nav-list">
            <li><a href="#p1">Responsable del tratamiento</a></li>
            <li><a href="#p2">Datos que recopilamos</a></li>
            <li><a href="#p3">Cómo usamos tus datos</a></li>
            <li><a href="#p4">Compartir información</a></li>
            <li><a href="#p5">Cookies</a></li>
            <li><a href="#p6">Seguridad</a></li>
            <li><a href="#p7">Tus derechos</a></li>
            <li><a href="#p8">Retención de datos</a></li>
            <li><a href="#p9">Menores de edad</a></li>
            <li><a href="#p10">Cambios a esta política</a></li>
            <li><a href="#p11">Contacto</a></li>
        </ol>
    </aside>

    {{-- Contenido principal --}}
    <main class="privacy-content">

        <section class="privacy-section" id="p1">
            <div class="privacy-section-header">
                <span class="privacy-number">01</span>
                <h2>Responsable del tratamiento</h2>
            </div>
            <p>
                Fluxa es una plataforma desarrollada y operada por <strong>Ángel David Agudelo Cuartas</strong>,
                con sede en Colombia. Somos responsables del tratamiento de los datos personales que
                recopilamos a través de la plataforma disponible en
                <a href="{{ config('app.url') }}" class="privacy-link">{{ config('app.url') }}</a>.
            </p>
            <p>
                Para cualquier consulta relacionada con el tratamiento de tus datos, puedes contactarnos
                en <a href="mailto:angeldavidagudelocuartas13@gmail.com" class="privacy-link">angeldavidagudelocuartas13@gmail.com</a>.
            </p>
        </section>

        <section class="privacy-section" id="p2">
            <div class="privacy-section-header">
                <span class="privacy-number">02</span>
                <h2>Datos que recopilamos</h2>
            </div>
            <p>Recopilamos datos en tres momentos distintos:</p>

            <div class="privacy-data-block">
                <h3>
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Al registrarte
                </h3>
                <ul class="privacy-list">
                    <li>Nombre y apellidos</li>
                    <li>Correo electrónico</li>
                    <li>Contraseña (almacenada cifrada con bcrypt)</li>
                    <li>Si usas Google OAuth: nombre, email y foto de perfil pública</li>
                </ul>
            </div>

            <div class="privacy-data-block">
                <h3>
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Al usar la plataforma
                </h3>
                <ul class="privacy-list">
                    <li>Información de perfil: biografía, username, foto, ubicación, sitio web</li>
                    <li>Stack tecnológico y experiencia laboral</li>
                    <li>Proyectos publicados, likes, comentarios y bookmarks</li>
                    <li>Preferencias de privacidad y notificaciones</li>
                </ul>
            </div>

            <div class="privacy-data-block">
                <h3>
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18" />
                    </svg>
                    Automáticamente
                </h3>
                <ul class="privacy-list">
                    <li>Dirección IP y datos del navegador (user agent)</li>
                    <li>Páginas visitadas y tiempo de sesión</li>
                    <li>Datos de cookies de sesión y preferencias</li>
                </ul>
            </div>
        </section>

        <section class="privacy-section" id="p3">
            <div class="privacy-section-header">
                <span class="privacy-number">03</span>
                <h2>Cómo usamos tus datos</h2>
            </div>
            <p>Utilizamos tus datos exclusivamente para:</p>
            <ul class="privacy-list">
                <li>Crear y gestionar tu cuenta en la plataforma</li>
                <li>Mostrarte contenido relevante en el feed y en Explorar</li>
                <li>Enviarte notificaciones que hayas habilitado</li>
                <li>Generar tu CV exportable en PDF</li>
                <li>Detectar y prevenir actividad fraudulenta o abusiva</li>
                <li>Mejorar el rendimiento y la experiencia de la plataforma</li>
            </ul>
            <div class="privacy-callout">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M5 13l4 4L19 7" />
                </svg>
                No usamos tus datos para publicidad de terceros ni para crear perfiles de comportamiento con fines comerciales.
            </div>
        </section>

        <section class="privacy-section" id="p4">
            <div class="privacy-section-header">
                <span class="privacy-number">04</span>
                <h2>Compartir información</h2>
            </div>
            <p>
                Fluxa <strong>no vende ni cede</strong> tus datos personales a terceros.
                Solo compartimos información en los siguientes casos excepcionales:
            </p>
            <ul class="privacy-list">
                <li>
                    <strong>Proveedores de servicio:</strong> plataformas como Cloudinary (almacenamiento
                    de imágenes) que actúan como encargados del tratamiento bajo contratos de confidencialidad.
                </li>
                <li>
                    <strong>Requerimiento legal:</strong> cuando sea exigido por una autoridad competente
                    conforme a la legislación colombiana o internacional aplicable.
                </li>
                <li>
                    <strong>Protección de derechos:</strong> cuando sea necesario para defender los derechos,
                    la seguridad o la propiedad de Fluxa o sus usuarios.
                </li>
            </ul>
        </section>

        <section class="privacy-section" id="p5">
            <div class="privacy-section-header">
                <span class="privacy-number">05</span>
                <h2>Cookies</h2>
            </div>
            <p>
                Fluxa utiliza cookies esenciales para el funcionamiento de la plataforma.
                No utilizamos cookies de seguimiento publicitario ni de análisis de comportamiento de terceros.
            </p>

            <div class="privacy-table-wrap">
                <table class="privacy-table">
                    <thead>
                        <tr>
                            <th>Cookie</th>
                            <th>Propósito</th>
                            <th>Duración</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>fluxa_session</code></td>
                            <td>Mantiene tu sesión iniciada</td>
                            <td>Sesión</td>
                        </tr>
                        <tr>
                            <td><code>XSRF-TOKEN</code></td>
                            <td>Protección contra ataques CSRF</td>
                            <td>2 horas</td>
                        </tr>
                        <tr>
                            <td><code>remember_web_*</code></td>
                            <td>Recuerda tu sesión si marcas "Recordarme"</td>
                            <td>400 días</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="privacy-section" id="p6">
            <div class="privacy-section-header">
                <span class="privacy-number">06</span>
                <h2>Seguridad</h2>
            </div>
            <p>
                Implementamos medidas técnicas y organizativas para proteger tus datos contra
                acceso no autorizado, alteración o pérdida:
            </p>
            <ul class="privacy-list">
                <li>Contraseñas almacenadas con hashing <strong>bcrypt</strong></li>
                <li>Autenticación de dos factores (2FA) disponible para todas las cuentas</li>
                <li>Comunicaciones cifradas mediante <strong>HTTPS/TLS</strong></li>
                <li>Protección CSRF en todos los formularios</li>
                <li>Acceso a la base de datos restringido al entorno del servidor</li>
            </ul>
            <div class="privacy-callout privacy-callout--warn">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                Ningún sistema es 100% seguro. Si detectas una vulnerabilidad, repórtala en
                <a href="mailto:angeldavidagudelocuartas13@gmail.com" class="privacy-link">angeldavidagudelocuartas13@gmail.com</a>.
            </div>
        </section>

        <section class="privacy-section" id="p7">
            <div class="privacy-section-header">
                <span class="privacy-number">07</span>
                <h2>Tus derechos</h2>
            </div>
            <p>
                De acuerdo con la Ley 1581 de 2012 (Habeas Data, Colombia) y normativas aplicables,
                tienes derecho a:
            </p>

            <div class="privacy-rights-grid">
                <div class="privacy-right-card">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <strong>Acceso</strong>
                    <p>Conocer qué datos tenemos sobre ti</p>
                </div>
                <div class="privacy-right-card">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    <strong>Rectificación</strong>
                    <p>Corregir datos incorrectos o desactualizados</p>
                </div>
                <div class="privacy-right-card">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    <strong>Supresión</strong>
                    <p>Solicitar la eliminación de tus datos</p>
                </div>
                <div class="privacy-right-card">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                    </svg>
                    <strong>Oposición</strong>
                    <p>Oponerte a ciertos tratamientos de tus datos</p>
                </div>
                <div class="privacy-right-card">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    <strong>Portabilidad</strong>
                    <p>Exportar tus datos en formato legible</p>
                </div>
                <div class="privacy-right-card">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    <strong>Revocación</strong>
                    <p>Revocar el consentimiento otorgado</p>
                </div>
            </div>

            <p style="margin-top: 20px;">
                Para ejercer cualquiera de estos derechos, escríbenos a
                <a href="mailto:angeldavidagudelocuartas13@gmail.com" class="privacy-link">angeldavidagudelocuartas13@gmail.com</a>
                indicando tu solicitud. Responderemos en un plazo máximo de <strong>15 días hábiles</strong>.
            </p>
        </section>

        <section class="privacy-section" id="p8">
            <div class="privacy-section-header">
                <span class="privacy-number">08</span>
                <h2>Retención de datos</h2>
            </div>
            <p>
                Conservamos tus datos mientras tu cuenta esté activa. Al eliminar tu cuenta:
            </p>
            <ul class="privacy-list">
                <li>Tus datos personales (nombre, email, foto) se eliminan de forma permanente</li>
                <li>El contenido público (proyectos, comentarios) puede permanecer de forma anónima durante un período de hasta <strong>30 días</strong> antes de su eliminación total</li>
                <li>Los registros técnicos (logs) se conservan por razones de seguridad hasta <strong>90 días</strong></li>
            </ul>
        </section>

        <section class="privacy-section" id="p9">
            <div class="privacy-section-header">
                <span class="privacy-number">09</span>
                <h2>Menores de edad</h2>
            </div>
            <p>
                Fluxa no está dirigida a personas menores de <strong>14 años</strong>.
                No recopilamos intencionalmente datos de menores. Si eres padre, madre o tutor
                y crees que tu hijo ha creado una cuenta, contáctanos en
                <a href="mailto:angeldavidagudelocuartas13@gmail.com" class="privacy-link">angeldavidagudelocuartas13@gmail.com</a>
                y procederemos a eliminar la cuenta de inmediato.
            </p>
        </section>

        <section class="privacy-section" id="p10">
            <div class="privacy-section-header">
                <span class="privacy-number">10</span>
                <h2>Cambios a esta política</h2>
            </div>
            <p>
                Podemos actualizar esta Política de Privacidad periódicamente. Cuando lo hagamos,
                te notificaremos dentro de la plataforma con al menos <strong>7 días de anticipación</strong>
                ante cambios sustanciales.
            </p>
            <p>
                La fecha de "última actualización" al inicio de esta página siempre refleja la versión vigente.
                El uso continuo de Fluxa tras los cambios implica tu aceptación.
            </p>
        </section>

        <section class="privacy-section" id="p11">
            <div class="privacy-section-header">
                <span class="privacy-number">11</span>
                <h2>Contacto</h2>
            </div>
            <p>
                Para cualquier consulta relacionada con tu privacidad o el tratamiento de tus datos:
            </p>
            <div class="privacy-contact-box">
                <div class="privacy-contact-row">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <div>
                        <p class="privacy-contact-label">Correo electrónico</p>
                        <a href="mailto:angeldavidagudelocuartas13@gmail.com" class="privacy-link">angeldavidagudelocuartas13@gmail.com</a>
                    </div>
                </div>
                <div class="privacy-contact-divider"></div>
                <div class="privacy-contact-row">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <div>
                        <p class="privacy-contact-label">Ubicación</p>
                        <span style="font-size:14px;color:#374151;">Colombia</span>
                    </div>
                </div>
                <div class="privacy-contact-divider"></div>
                <div class="privacy-contact-row">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <p class="privacy-contact-label">Tiempo de respuesta</p>
                        <span style="font-size:14px;color:#374151;">Máximo 15 días hábiles</span>
                    </div>
                </div>
            </div>

            <p style="margin-top:20px;">
                También puedes revisar nuestros
                <a href="{{ route('terms') }}" class="privacy-link">Términos y Condiciones</a>
                para más información sobre el uso de la plataforma.
            </p>
        </section>

    </main>
</div>

<x-footer />
@endsection

@push('styles')
@vite('resources/css/public/privacyPolicy.css')
@endpush