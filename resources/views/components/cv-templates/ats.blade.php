@props([
'profile',
'user',
'technologies' => collect([]),
'projects' => collect([]),
'workExperiences' => collect([]),
'educations' => collect([]),
'cvSettings' => [],
])
{{-- ATS-friendly CV template: single column, clean hierarchy, no tables or complex layouts --}}
<div style="width:800px;margin:0 auto;font-family:Arial,Verdana,sans-serif;font-size:11pt;color:#000;line-height:1.5;padding:40px 50px;background:#fff;">

    {{-- ══ HEADER ══ --}}
    <div style="text-align:center;margin-bottom:20px;">
        <h1 style="font-size:20pt;font-weight:700;margin:0 0 4px;color:#000;">{{ $user->name }}</h1>
        <p style="font-size:12pt;margin:0 0 2px;color:#333;">{{ $rolProfesional ?? 'Software Developer' }}</p>
        <p style="font-size:10pt;margin:0;color:#555;">
            @if(!empty($user->email) && ($cvSettings['show_email'] ?? true))
                {{ $user->email }} |
            @endif
            @php $cvLocation = trim(($profile->city ?? '') . ', ' . ($profile->country ?? ''), ' ,'); @endphp
            @if(!empty($cvLocation) && ($cvSettings['show_location'] ?? true))
                {{ $cvLocation }} |
            @endif
            @if(!empty($profile->phone_number))
                {{ $profile->phone_code ?? '' }} {{ $profile->phone_number }} |
            @endif
            linkedin.com/in/{{ $user->username }}
        </p>
    </div>

    {{-- ══ RESUMEN ══ --}}
    @if(!empty($profile->bio))
    <section style="margin-bottom:16px;">
        <h2 style="font-size:13pt;font-weight:700;border-bottom:1px solid #ccc;padding-bottom:4px;margin:0 0 8px;color:#000;">Resumen Profesional</h2>
        <p style="margin:0;font-size:10pt;color:#333;">{{ $profile->bio }}</p>
    </section>
    @endif

    {{-- ══ SECCIONES DINÁMICAS ══ --}}
    @foreach($cvSettings['section_order'] as $section)
        @switch($section)
            @case('experience')
                @if(($cvSettings['show_experience'] ?? true) && isset($workExperiences) && $workExperiences->isNotEmpty())
                <section style="margin-bottom:16px;">
                    <h2 style="font-size:13pt;font-weight:700;border-bottom:1px solid #ccc;padding-bottom:4px;margin:0 0 8px;color:#000;">Experiencia Laboral</h2>
                    @foreach($workExperiences->take(5) as $exp)
                    <div style="margin-bottom:10px;">
                        <div style="font-size:11pt;font-weight:700;color:#000;">{{ $exp->position }}</div>
                        <div style="font-size:10pt;color:#333;">
                            {{ $exp->company }}
                            @if(!empty($exp->location)) — {{ $exp->location }} @endif
                        </div>
                        <div style="font-size:9pt;color:#666;margin-bottom:3px;">
                            {{ \Carbon\Carbon::parse($exp->started_at)->format('M Y') }} —
                            {{ $exp->current ? 'Presente' : ($exp->ended_at ? \Carbon\Carbon::parse($exp->ended_at)->format('M Y') : 'Presente') }}
                            | {{ \App\Models\WorkExperience::TYPES[$exp->type] ?? $exp->type }}
                        </div>
                        @if(!empty($exp->description))
                        <p style="margin:0;font-size:10pt;color:#333;">{{ $exp->description }}</p>
                        @endif
                    </div>
                    @endforeach
                </section>
                @endif
            @break

            @case('education')
                @if(($cvSettings['show_education'] ?? true) && isset($educations) && $educations->isNotEmpty())
                <section style="margin-bottom:16px;">
                    <h2 style="font-size:13pt;font-weight:700;border-bottom:1px solid #ccc;padding-bottom:4px;margin:0 0 8px;color:#000;">Educación</h2>
                    @foreach($educations->take(5) as $edu)
                    <div style="margin-bottom:8px;">
                        <div style="font-size:11pt;font-weight:700;color:#000;">{{ $edu->degree }}</div>
                        <div style="font-size:10pt;color:#333;">
                            {{ $edu->institution }}
                            @if(!empty($edu->field)) — {{ $edu->field }} @endif
                        </div>
                        <div style="font-size:9pt;color:#666;">
                            @if($edu->current)
                                En curso
                            @elseif(!empty($edu->graduated_year))
                                Graduado en {{ $edu->graduated_year }}
                            @endif
                        </div>
                    </div>
                    @endforeach
                </section>
                @endif
            @break

            @case('projects')
                @if(($cvSettings['show_projects'] ?? true) && $projects->isNotEmpty())
                <section style="margin-bottom:16px;">
                    <h2 style="font-size:13pt;font-weight:700;border-bottom:1px solid #ccc;padding-bottom:4px;margin:0 0 8px;color:#000;">Proyectos</h2>
                    @foreach($projects->take(3) as $proj)
                    <div style="margin-bottom:8px;">
                        <div style="font-size:11pt;font-weight:700;color:#000;">{{ $proj->title }}</div>
                        @if(!empty($proj->content))
                        <p style="margin:2px 0;font-size:10pt;color:#333;">{{ $proj->content }}</p>
                        @endif
                        @if($proj->technologies->isNotEmpty())
                        <p style="margin:0;font-size:9pt;color:#666;">
                            {{ $proj->technologies->pluck('name')->implode(' · ') }}
                        </p>
                        @endif
                    </div>
                    @endforeach
                </section>
                @endif
            @break

            @case('skills')
                @if($technologies->isNotEmpty())
                <section style="margin-bottom:16px;">
                    <h2 style="font-size:13pt;font-weight:700;border-bottom:1px solid #ccc;padding-bottom:4px;margin:0 0 8px;color:#000;">Habilidades Técnicas</h2>
                    <div style="font-size:10pt;color:#333;">
                        {{ $technologies->pluck('name')->implode(', ') }}
                    </div>
                </section>
                @endif
            @break
        @endswitch
    @endforeach

    {{-- ══ FOOTER ══ --}}
    <div style="margin-top:20px;padding-top:10px;border-top:1px solid #ddd;font-size:9pt;color:#888;text-align:center;">
        CV generado desde Fluxa red social para Desarrolladores — {{ $urlPerfil ?? (request()->getHost() . '/profile/' . $user->username) }}
    </div>
</div>
