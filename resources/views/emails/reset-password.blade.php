<x-mail::message>
# Hola, {{ $user->name }}!

Recibimos una solicitud para restablecer la contraseña de tu cuenta en **Fluxa**.

<x-mail::button :url="$url">
Restablecer contraseña
</x-mail::button>

Este enlace caduca en {{ $expire }} minutos.

Si no solicitaste este cambio, ignora este mensaje. Tu contraseña actual seguirá funcionando.

<x-mail::subcopy>
Si el botón no funciona, copia y pega este enlace en tu navegador: [{{ $url }}]({{ $url }})
</x-mail::subcopy>

Saludos,<br>
El equipo de Fluxa
</x-mail::message>
