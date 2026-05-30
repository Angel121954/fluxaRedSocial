<x-mail::message>
# Hola, {{ $user->name }}!

Este es tu resumen semanal de lo **mas importante** en Fluxa.

<x-mail::panel>
## Tus logros de la semana

@if ($stats['badges_earned'] > 0)
- **{{ $stats['badges_earned'] }}** {{ $stats['badges_earned'] === 1 ? 'nueva insignia' : 'nuevas insignias' }} obtenidas
@endif

## Tu actividad

@if ($stats['projects_created'] > 0)
- **{{ $stats['projects_created'] }}** {{ $stats['projects_created'] === 1 ? 'proyecto creado' : 'proyectos creados' }}
@endif
@if ($stats['comments_made'] > 0)
- **{{ $stats['comments_made'] }}** {{ $stats['comments_made'] === 1 ? 'comentario' : 'comentarios' }} en publicaciones
@endif
@if ($stats['diary_responses'] > 0)
- **{{ $stats['diary_responses'] }}** {{ $stats['diary_responses'] === 1 ? 'respuesta' : 'respuestas' }} en el diario
@endif
@if ($stats['work_experiences_added'] > 0)
- **{{ $stats['work_experiences_added'] }}** {{ $stats['work_experiences_added'] === 1 ? 'nueva experiencia' : 'nuevas experiencias' }} laboral{{ $stats['work_experiences_added'] === 1 ? '' : 'es' }}
@endif
@if ($stats['educations_added'] > 0)
- **{{ $stats['educations_added'] }}** {{ $stats['educations_added'] === 1 ? 'nuevo estudio' : 'nuevos estudios' }} agregados
@endif
@if ($stats['salary_reports'] > 0)
- **{{ $stats['salary_reports'] }}** {{ $stats['salary_reports'] === 1 ? 'reporte' : 'reportes' }} salarial{{ $stats['salary_reports'] === 1 ? '' : 'es' }} compartidos
@endif

## Reconocimiento

@if ($stats['new_followers'] > 0)
- **{{ $stats['new_followers'] }}** {{ $stats['new_followers'] === 1 ? 'nuevo seguidor' : 'nuevos seguidores' }}
@endif
@if ($stats['new_likes'] > 0)
- **{{ $stats['new_likes'] }}** {{ $stats['new_likes'] === 1 ? 'like' : 'likes' }} en tus proyectos
@endif
@if ($stats['new_comments'] > 0)
- **{{ $stats['new_comments'] }}** {{ $stats['new_comments'] === 1 ? 'comentario' : 'comentarios' }} en tus publicaciones
@endif
@if ($stats['endorsements_received'] > 0)
- **{{ $stats['endorsements_received'] }}** {{ $stats['endorsements_received'] === 1 ? 'nuevo endorsement' : 'nuevos endorsements' }} en tus habilidades
@endif
</x-mail::panel>

@if ($trendingProjects->isNotEmpty())
## Proyectos destacados de la semana

@foreach ($trendingProjects as $project)
**{{ $project->title }}** por {{ $project->user->name }}
{{ $project->likes_count }} {{ $project->likes_count === 1 ? 'like' : 'likes' }} · {{ $project->comments_count }} {{ $project->comments_count === 1 ? 'comentario' : 'comentarios' }}

@endforeach

<x-mail::button :url="route('explore.index')">
Ver proyectos destacados
</x-mail::button>
@endif

<x-mail::subcopy>
Puedes cambiar tus preferencias de notificaciones en cualquier momento desde [Configuracion]({{ route('notification-preference.index') }}).
</x-mail::subcopy>

Saludos,<br>
Ángel David Agudelo, Administrador de Fluxa
</x-mail::message>
