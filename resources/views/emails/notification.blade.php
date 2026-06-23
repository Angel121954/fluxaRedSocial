<x-mail::message>
# Hola, {{ $user->name }}!

{{ $fromUserName ? "**{$fromUserName}** {$body}" : $body }}

@if ($actionUrl)
<x-mail::button :url="$actionUrl">
{{ $actionText ?? 'Ver más' }}
</x-mail::button>
@endif

<x-mail::subcopy>
Puedes cambiar tus preferencias de notificaciones en cualquier momento desde [Configuración]({{ route('notification-preference.index') }}).
</x-mail::subcopy>

Saludos,<br>
El equipo de Fluxa
</x-mail::message>
