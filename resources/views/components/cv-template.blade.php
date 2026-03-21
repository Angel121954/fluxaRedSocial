{{-- resources/views/components/cv-template.blade.php --}}
@php
/** @var \App\Models\User $usuarioActual */
$usuarioActual = Auth::user();

/** @var \App\Models\Profile $perfilActual */
$perfilActual = $profile;

$urlPerfil = request()->getHost() . '/' . $usuarioActual->username;
$urlQrExterno = 'https://api.qrserver.com/v1/create-qr-code/?size=100x100&data='
. urlencode('https://' . $urlPerfil)
. '&color=0d9488&bgcolor=ffffff&margin=6';

// Conteos del usuario
$cantidadSeguidores = $usuarioActual->followers_count ?? 0;
$cantidadSiguiendo = $usuarioActual->followings_count ?? 0;
$diasActivo = (int) ($usuarioActual->days_active ?? 0);

$excepcionesIcono = [
'amazonwebservices' => 'plain-wordmark',
'angularjs' => 'plain',
'django' => 'plain',
'tailwindcss' => 'plain',
'kubernetes' => 'plain',
'graphql' => 'plain',
'firebase' => 'plain',
'express' => 'original-wordmark',
];

$paleta = [
'fondo' => '#f8fafc',
'tarjeta' => '#ffffff',
'primario' => '#14b8a6',
'primarioOscuro' => '#0d9488',
'primarioTexto' => '#ffffff',
'secundario' => '#f0fdfa',
'borde' => '#e2e8f0',
'texto' => '#0f172a',
'textoSuave' => '#64748b',
'barraLateral' => '#f8fafc',
'azul' => '#0ea5e9',
'linkedin' => '#0077b5',
'twitter' => '#1da1f2',
];

$rolProfesional = $technologies->isNotEmpty()
? $technologies->first()->name . ' Developer'
: 'Software Developer';

$estadisticas = [
['valor' => $projects->count(), 'etiqueta' => 'Proyectos'],
['valor' => $cantidadSiguiendo, 'etiqueta' => 'Siguiendo'],
['valor' => $cantidadSeguidores, 'etiqueta' => 'Seguidores'],
['valor' => $diasActivo, 'etiqueta' => 'Días activo'],
];

// ── Resolver fuentes de imagen ──
// Si viene base64 desde el controlador (Browsershot) lo usa.
// Si no, usa la URL normal (preview en pantalla / html2pdf).
$srcAvatar = $avatarBase64
?? str_replace('type=normal', 'type=large', (string)($perfilActual->avatar ?? ''));

$srcLogo = $logoBase64
?? asset('img/logoFluxa.png');

$srcQr = $qrBase64
?? $urlQrExterno;
@endphp

{{-- Wrapper: oculto para html2pdf, visible para Browsershot --}}
<div id="cv-template" @if(!$avatarBase64) style="position:fixed;left:-9999px;top:0;pointer-events:none;z-index:-1;" @endif>
    <div style="width:860px;background:{{ $paleta['fondo'] }};font-family:'Segoe UI',system-ui,sans-serif;padding:20px;box-sizing:border-box;">

        {{-- ══ TARJETA PRINCIPAL ══ --}}
        <div style="background:{{ $paleta['tarjeta'] }};border-radius:16px;overflow:hidden;box-shadow:0 1px 4px rgba(0,0,0,.08);">

            {{-- ── ENCABEZADO ── --}}
            <div style="padding:28px 36px 24px;border-bottom:1px solid {{ $paleta['borde'] }};">
                <div style="display:flex;align-items:center;gap:24px;">

                    <img id="cv-avatar" src="{{ $srcAvatar }}" width="90" height="90"
                        style="width:90px;height:90px;border-radius:50%;object-fit:cover;border:3px solid {{ $paleta['primario'] }};flex-shrink:0;" />

                    <div style="flex:1;min-width:0;">
                        <h1 style="margin:0 0 3px;font-size:24px;font-weight:800;color:{{ $paleta['texto'] }};letter-spacing:-0.3px;line-height:1.2;">{{ $usuarioActual->name }}</h1>
                        <p style="margin:0 0 12px;font-size:13px;color:{{ $paleta['textoSuave'] }};">&#64;{{ $usuarioActual->username }}</p>
                        <div style="display:inline-flex;align-items:center;gap:6px;background:{{ $paleta['secundario'] }};border:1px solid rgba(20,184,166,.35);border-radius:20px;padding:4px 12px;">
                            <img src="{{ $srcLogo }}" width="14" height="14"
                                style="width:14px;height:14px;border-radius:3px;object-fit:cover;flex-shrink:0;" />
                            <span style="font-size:12px;font-weight:600;color:{{ $paleta['primarioOscuro'] }};">{{ $rolProfesional }}</span>
                        </div>
                    </div>

                    <div style="text-align:center;flex-shrink:0;">
                        <img src="{{ $srcQr }}" width="82" height="82"
                            style="width:82px;height:82px;border-radius:10px;border:1px solid {{ $paleta['borde'] }};display:block;" />
                        <p style="margin:5px 0 0;font-size:9px;color:{{ $paleta['textoSuave'] }};font-weight:500;max-width:100px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $urlPerfil }}</p>
                    </div>

                </div>
            </div>

            {{-- ── CUERPO ── --}}
            <div style="display:table;width:100%;border-collapse:collapse;">

                {{-- ── BARRA LATERAL ── --}}
                <div style="display:table-cell;width:215px;vertical-align:top;background:{{ $paleta['barraLateral'] }};border-right:1px solid {{ $paleta['borde'] }};padding:22px 18px;">
                    <div style="display:flex;flex-direction:column;gap:18px;">

                        {{-- CONTACTO --}}
                        <div>
                            <p style="margin:0 0 10px;font-size:10px;font-weight:700;color:{{ $paleta['primario'] }};text-transform:uppercase;letter-spacing:1px;">Contacto</p>
                            <div style="display:flex;flex-direction:column;gap:7px;">

                                @if(!empty($perfilActual->phone_number))
                                <div style="display:flex;gap:7px;align-items:flex-start;">
                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="{{ $paleta['primario'] }}" stroke-width="2.2" style="flex-shrink:0;margin-top:1px;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                    <span style="font-size:11px;color:{{ $paleta['texto'] }};line-height:1.4;">{{ $perfilActual->phone_code ?? '' }} {{ $perfilActual->phone_number }}</span>
                                </div>
                                @endif

                                @if(!empty($usuarioActual->email))
                                <div style="display:flex;gap:7px;align-items:flex-start;">
                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="{{ $paleta['primario'] }}" stroke-width="2.2" style="flex-shrink:0;margin-top:1px;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    <span style="font-size:9.5px;color:{{ $paleta['texto'] }};word-break:break-all;line-height:1.4;">{{ $usuarioActual->email }}</span>
                                </div>
                                @endif

                                @if(!empty($perfilActual->location))
                                <div style="display:flex;gap:7px;align-items:flex-start;">
                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="{{ $paleta['primario'] }}" stroke-width="2.2" style="flex-shrink:0;margin-top:1px;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span style="font-size:11px;color:{{ $paleta['texto'] }};line-height:1.4;">{{ $perfilActual->location }}</span>
                                </div>
                                @endif

                            </div>
                        </div>

                        <div style="height:1px;background:{{ $paleta['borde'] }};"></div>

                        {{-- TECNOLOGÍAS --}}
                        @if($technologies->isNotEmpty())
                        <div>
                            <p style="margin:0 0 10px;font-size:10px;font-weight:700;color:{{ $paleta['primario'] }};text-transform:uppercase;letter-spacing:1px;">Tecnologías</p>
                            <div style="display:flex;flex-wrap:wrap;gap:6px;">
                                @foreach($technologies as $tecnologia)
                                @php
                                $slugIcono = (string) $tecnologia->slug;
                                $tipoIcono = $excepcionesIcono[$slugIcono] ?? 'original';
                                $inicialesTech = strtoupper(substr((string) $tecnologia->name, 0, 2));

                                // Base64 si viene del controlador, URL externa si es preview
                                $srcIcono = $tecnologia->iconoB64
                                ?? "https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/{$slugIcono}/{$slugIcono}-{$tipoIcono}.svg";
                                @endphp
                                <div title="{{ $tecnologia->name }}"
                                    style="width:34px;height:34px;border-radius:8px;background:{{ $paleta['tarjeta'] }};border:1px solid {{ $paleta['borde'] }};display:flex;align-items:center;justify-content:center;overflow:hidden;position:relative;flex-shrink:0;">
                                    @if($tecnologia->iconoB64)
                                    {{-- PDF: base64, sin onerror --}}
                                    <img src="{{ $srcIcono }}" width="20" height="20"
                                        alt="{{ $tecnologia->name }}"
                                        style="width:20px;height:20px;display:block;" />
                                    @else
                                    {{-- Preview: URL externa con fallback JS --}}
                                    <img src="{{ $srcIcono }}" width="20" height="20"
                                        alt="{{ $tecnologia->name }}"
                                        style="width:20px;height:20px;display:block;"
                                        onerror="this.style.display='none';this.nextElementSibling.style.display='flex';" />
                                    <span style="display:none;position:absolute;inset:0;align-items:center;justify-content:center;font-size:9px;font-weight:700;color:{{ $paleta['primarioOscuro'] }};background:{{ $paleta['secundario'] }};text-align:center;">{{ $inicialesTech }}</span>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <div style="height:1px;background:{{ $paleta['borde'] }};"></div>
                        @endif

                        {{-- REDES SOCIALES --}}
                        <div>
                            <p style="margin:0 0 10px;font-size:10px;font-weight:700;color:{{ $paleta['primario'] }};text-transform:uppercase;letter-spacing:1px;">Redes sociales</p>
                            <div style="display:flex;flex-direction:column;gap:7px;">

                                @if(!empty($perfilActual->linkedin_url))
                                <div style="display:flex;gap:6px;align-items:flex-start;">
                                    <span style="font-size:10px;font-weight:800;color:{{ $paleta['linkedin'] }};flex-shrink:0;line-height:1.4;">in</span>
                                    <span style="font-size:9.5px;color:{{ $paleta['texto'] }};word-break:break-all;line-height:1.4;">{{ $perfilActual->linkedin_url }}</span>
                                </div>
                                @endif

                                @if(!empty($perfilActual->twitter_url))
                                <div style="display:flex;gap:6px;align-items:flex-start;">
                                    <span style="font-size:10px;font-weight:800;color:{{ $paleta['twitter'] }};flex-shrink:0;line-height:1.4;">𝕏</span>
                                    <span style="font-size:9.5px;color:{{ $paleta['texto'] }};word-break:break-all;line-height:1.4;">{{ $perfilActual->twitter_url }}</span>
                                </div>
                                @endif

                                @if(!empty($perfilActual->github_url))
                                <div style="display:flex;gap:6px;align-items:flex-start;">
                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="{{ $paleta['texto'] }}" style="flex-shrink:0;margin-top:2px;">
                                        <path d="M12 0C5.37 0 0 5.37 0 12c0 5.31 3.435 9.795 8.205 11.385.6.105.825-.255.825-.57 0-.285-.015-1.23-.015-2.235-3.015.555-3.795-.735-4.035-1.41-.135-.345-.72-1.41-1.23-1.695-.42-.225-1.02-.78-.015-.795.945-.015 1.62.87 1.845 1.23 1.08 1.815 2.805 1.305 3.495.99.105-.78.42-1.305.765-1.605-2.67-.3-5.46-1.335-5.46-5.925 0-1.305.465-2.385 1.23-3.225-.12-.3-.54-1.53.12-3.18 0 0 1.005-.315 3.3 1.23.96-.27 1.98-.405 3-.405s2.04.135 3 .405c2.295-1.56 3.3-1.23 3.3-1.23.66 1.65.24 2.88.12 3.18.765.84 1.23 1.905 1.23 3.225 0 4.605-2.805 5.625-5.475 5.925.435.375.81 1.095.81 2.22 0 1.605-.015 2.895-.015 3.3 0 .315.225.69.825.57A12.02 12.02 0 0024 12c0-6.63-5.37-12-12-12z" />
                                    </svg>
                                    <span style="font-size:9.5px;color:{{ $paleta['texto'] }};word-break:break-all;line-height:1.4;">{{ $perfilActual->github_url }}</span>
                                </div>
                                @endif

                            </div>
                        </div>

                        <div style="height:1px;background:{{ $paleta['borde'] }};"></div>

                        {{-- ESTADÍSTICAS --}}
                        @if(!empty($estadisticas) && $projects->isNotEmpty())
                        <div>
                            <p style="margin:0 0 10px;font-size:10px;font-weight:700;color:{{ $paleta['primario'] }};text-transform:uppercase;letter-spacing:1px;">Estadísticas</p>
                            @foreach($estadisticas as $stat)
                            <div style="display:flex;align-items:center;gap:8px;padding:5px 0;{{ !$loop->last ? 'border-bottom:1px solid '.$paleta['borde'].';' : '' }}">
                                <span style="font-size:16px;font-weight:800;color:{{ $paleta['texto'] }};min-width:24px;line-height:1;">{{ $stat['valor'] }}</span>
                                <span style="font-size:11px;color:{{ $paleta['textoSuave'] }};">{{ $stat['etiqueta'] }}</span>
                            </div>
                            @endforeach
                        </div>
                        @endif

                    </div>
                </div>
                {{-- /BARRA LATERAL --}}

                {{-- ── CONTENIDO PRINCIPAL ── --}}
                <div style="display:table-cell;vertical-align:top;padding:26px 30px;">
                    <div style="display:flex;flex-direction:column;gap:22px;">

                        {{-- RESUMEN --}}
                        @if(!empty($perfilActual->bio))
                        <div>
                            <h2 style="margin:0 0 8px;font-size:14px;font-weight:800;color:{{ $paleta['texto'] }};border-bottom:2px solid {{ $paleta['borde'] }};padding-bottom:7px;">Resumen</h2>
                            <p style="margin:0;font-size:13px;color:{{ $paleta['textoSuave'] }};line-height:1.75;">{{ $perfilActual->bio }}</p>
                        </div>
                        @endif

                        {{-- SOBRE MÍ --}}
                        <div>
                            <h2 style="margin:0 0 10px;font-size:14px;font-weight:800;color:{{ $paleta['texto'] }};border-bottom:2px solid {{ $paleta['borde'] }};padding-bottom:7px;">Sobre mí</h2>
                            <div style="display:flex;flex-direction:column;gap:7px;">

                                @if(!empty($perfilActual->location))
                                <div style="display:flex;gap:8px;align-items:center;">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="{{ $paleta['primario'] }}" stroke-width="2" style="flex-shrink:0;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span style="font-size:12px;color:{{ $paleta['textoSuave'] }};font-weight:600;min-width:85px;">Ubicación:</span>
                                    <span style="font-size:12px;color:{{ $paleta['texto'] }};">{{ $perfilActual->location }}</span>
                                </div>
                                @endif

                                @if(!empty($perfilActual->website_url))
                                <div style="display:flex;gap:8px;align-items:center;">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="{{ $paleta['primario'] }}" stroke-width="2" style="flex-shrink:0;">
                                        <circle cx="12" cy="12" r="10" />
                                        <line x1="2" y1="12" x2="22" y2="12" />
                                        <path stroke-linecap="round" d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z" />
                                    </svg>
                                    <span style="font-size:12px;color:{{ $paleta['textoSuave'] }};font-weight:600;min-width:85px;">Sitio web:</span>
                                    <span style="font-size:12px;color:{{ $paleta['azul'] }};">{{ $perfilActual->website_url }}</span>
                                </div>
                                @endif

                                <div style="display:flex;gap:8px;align-items:center;">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="{{ $paleta['primario'] }}" stroke-width="2" style="flex-shrink:0;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span style="font-size:12px;color:{{ $paleta['textoSuave'] }};font-weight:600;min-width:85px;">Seguidores:</span>
                                    <span style="font-size:12px;color:{{ $paleta['texto'] }};">{{ $cantidadSeguidores }}</span>
                                </div>

                                <div style="display:flex;gap:8px;align-items:center;">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="{{ $paleta['primario'] }}" stroke-width="2" style="flex-shrink:0;">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                                        <line x1="16" y1="2" x2="16" y2="6" />
                                        <line x1="8" y1="2" x2="8" y2="6" />
                                        <line x1="3" y1="10" x2="21" y2="10" />
                                    </svg>
                                    <span style="font-size:12px;color:{{ $paleta['textoSuave'] }};font-weight:600;min-width:85px;">Miembro desde:</span>
                                    <span style="font-size:12px;color:{{ $paleta['texto'] }};">{{ \Carbon\Carbon::parse($perfilActual->created_at ?? now())->translatedFormat('F Y') }}</span>
                                </div>

                                @if(!empty($perfilActual->gender))
                                <div style="display:flex;gap:8px;align-items:center;">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="{{ $paleta['primario'] }}" stroke-width="2" style="flex-shrink:0;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    <span style="font-size:12px;color:{{ $paleta['textoSuave'] }};font-weight:600;min-width:85px;">Género:</span>
                                    <span style="font-size:12px;color:{{ $paleta['texto'] }};">{{ $perfilActual->gender === 'male' ? 'Masculino' : ($perfilActual->gender === 'female' ? 'Femenino' : 'No especificado') }}</span>
                                </div>
                                @endif

                            </div>
                        </div>

                        {{-- EXPERIENCIA LABORAL --}}
                        @if(isset($workExperiences) && $workExperiences->isNotEmpty())
                        <div>
                            <h2 style="margin:0 0 14px;font-size:14px;font-weight:800;color:{{ $paleta['texto'] }};border-bottom:2px solid {{ $paleta['borde'] }};padding-bottom:7px;">Experiencia Laboral</h2>
                            <div style="display:flex;flex-direction:column;gap:14px;">
                                @foreach($workExperiences->take(4) as $experiencia)
                                @php
                                $fechaInicio = \Carbon\Carbon::parse($experiencia->started_at)->translatedFormat('M Y');
                                $fechaFin = $experiencia->current
                                ? 'Presente'
                                : ($experiencia->ended_at
                                ? \Carbon\Carbon::parse($experiencia->ended_at)->translatedFormat('M Y')
                                : 'Presente');
                                @endphp
                                <div style="display:flex;gap:12px;align-items:flex-start;{{ !$loop->last ? 'padding-bottom:14px;border-bottom:1px solid '.$paleta['borde'].';' : '' }}">
                                    <div style="width:38px;height:38px;flex-shrink:0;border-radius:9px;background:{{ $paleta['secundario'] }};border:1px solid rgba(20,184,166,.35);display:flex;align-items:center;justify-content:center;">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="{{ $paleta['primario'] }}" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                    </div>
                                    <div style="flex:1;min-width:0;">
                                        <div style="display:flex;align-items:center;gap:8px;margin-bottom:2px;flex-wrap:wrap;">
                                            <h3 style="margin:0;font-size:13px;font-weight:700;color:{{ $paleta['texto'] }};">{{ $experiencia->position }}</h3>
                                            @if($experiencia->current)
                                            <span style="font-size:9px;font-weight:600;color:{{ $paleta['primarioOscuro'] }};background:{{ $paleta['secundario'] }};border:1px solid rgba(20,184,166,.35);padding:2px 8px;border-radius:20px;white-space:nowrap;">Actual</span>
                                            @endif
                                        </div>
                                        <div style="display:flex;align-items:center;gap:6px;margin-bottom:4px;">
                                            <span style="font-size:12px;font-weight:600;color:{{ $paleta['primario'] }};">{{ $experiencia->company }}</span>
                                            @if(!empty($experiencia->location))
                                            <span style="font-size:11px;color:{{ $paleta['textoSuave'] }};">· {{ $experiencia->location }}</span>
                                            @endif
                                        </div>
                                        <div style="display:flex;align-items:center;gap:5px;margin-bottom:5px;">
                                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="{{ $paleta['textoSuave'] }}" stroke-width="2" style="flex-shrink:0;">
                                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                                                <line x1="16" y1="2" x2="16" y2="6" />
                                                <line x1="8" y1="2" x2="8" y2="6" />
                                                <line x1="3" y1="10" x2="21" y2="10" />
                                            </svg>
                                            <span style="font-size:10px;color:{{ $paleta['textoSuave'] }};">{{ $fechaInicio }} — {{ $fechaFin }}</span>
                                        </div>
                                        @if(!empty($experiencia->description))
                                        <p style="margin:0;font-size:11px;color:{{ $paleta['textoSuave'] }};line-height:1.6;">{{ $experiencia->description }}</p>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        {{-- PROYECTOS --}}
                        @if($projects->isNotEmpty())
                        <div>
                            <h2 style="margin:0 0 12px;font-size:14px;font-weight:800;color:{{ $paleta['texto'] }};border-bottom:2px solid {{ $paleta['borde'] }};padding-bottom:7px;">Proyectos</h2>
                            <div style="display:flex;flex-direction:column;gap:13px;">
                                @foreach($projects->take(3) as $proyecto)
                                <div style="display:flex;gap:12px;align-items:flex-start;">
                                    <div style="width:44px;height:44px;flex-shrink:0;border-radius:10px;background:linear-gradient(135deg,{{ $paleta['primarioOscuro'] }},{{ $paleta['primario'] }});display:flex;align-items:center;justify-content:center;">
                                        <span style="font-size:17px;font-weight:800;color:#ffffff;line-height:1;">{{ strtoupper(substr((string)$proyecto->title, 0, 1)) }}</span>
                                    </div>
                                    <div style="flex:1;min-width:0;">
                                        <div style="display:flex;align-items:center;gap:8px;margin-bottom:3px;">
                                            <h3 style="margin:0;font-size:13px;font-weight:700;color:{{ $paleta['texto'] }};white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $proyecto->title }}</h3>
                                            <span style="flex-shrink:0;font-size:9px;font-weight:600;color:{{ $paleta['primarioOscuro'] }};background:{{ $paleta['secundario'] }};border:1px solid rgba(20,184,166,.35);padding:2px 8px;border-radius:20px;white-space:nowrap;">DÍA {{ $proyecto->days_active }}</span>
                                        </div>
                                        @if(!empty($proyecto->content))
                                        <p style="margin:0 0 5px;font-size:11px;color:{{ $paleta['textoSuave'] }};line-height:1.5;">{{ $proyecto->content }}</p>
                                        @endif
                                        @if($proyecto->technologies->isNotEmpty())
                                        <div style="display:flex;flex-wrap:wrap;gap:2px;">
                                            @foreach($proyecto->technologies as $techProyecto)
                                            <span style="font-size:10px;color:{{ $paleta['textoSuave'] }};">{{ $techProyecto->name }}{{ !$loop->last ? ' ·' : '' }}</span>
                                            @endforeach
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                    </div>
                </div>
                {{-- /CONTENIDO PRINCIPAL --}}

            </div>
            {{-- /CUERPO --}}

        </div>
        {{-- /TARJETA PRINCIPAL --}}

        {{-- ══ PIE DE PÁGINA ══ --}}
        <div style="margin-top:10px;background:{{ $paleta['tarjeta'] }};border-radius:12px;padding:14px 24px;display:flex;align-items:center;justify-content:space-between;box-shadow:0 1px 3px rgba(0,0,0,.06);">
            <div style="display:flex;flex-direction:column;gap:3px;">
                <div style="display:flex;align-items:center;gap:6px;">
                    <span style="font-size:10px;color:{{ $paleta['textoSuave'] }};">Generado con</span>
                    <img src="{{ $srcLogo }}" width="15" height="15" style="width:15px;height:15px;border-radius:3px;object-fit:cover;" />
                    <span style="font-size:11px;font-weight:700;color:{{ $paleta['primario'] }};">Fluxa</span>
                </div>
                <span style="font-size:11px;font-weight:700;color:{{ $paleta['texto'] }};">{{ $urlPerfil }}</span>
            </div>

            @if(!empty($estadisticas) && $projects->isNotEmpty())
            <div style="display:flex;gap:18px;align-items:flex-end;">
                @foreach($estadisticas as $statPie)
                <div style="text-align:center;">
                    <div style="font-size:17px;font-weight:800;color:{{ $paleta['texto'] }};line-height:1;">{{ $statPie['valor'] }}</div>
                    <div style="font-size:8px;color:{{ $paleta['textoSuave'] }};text-transform:uppercase;letter-spacing:.5px;margin-top:2px;">{{ $statPie['etiqueta'] }}</div>
                </div>
                @endforeach
            </div>
            @endif

            <img src="{{ $srcQr }}" width="60" height="60"
                style="width:60px;height:60px;border-radius:8px;border:1px solid {{ $paleta['borde'] }};" />
        </div>

        {{-- ══ LOGO FLUXA ══ --}}
        <div style="margin-top:8px;text-align:center;padding:10px 0;">
            <div style="display:inline-flex;align-items:center;gap:9px;">
                <img src="{{ $srcLogo }}" width="26" height="26"
                    style="width:26px;height:26px;border-radius:7px;object-fit:cover;" />
                <span style="font-size:20px;font-weight:800;color:{{ $paleta['texto'] }};letter-spacing:-0.5px;">Fluxa</span>
            </div>
        </div>

    </div>
</div>