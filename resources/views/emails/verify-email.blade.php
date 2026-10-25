<x-mail::message>
# Hola, {{ $user->name }}!

Gracias por registrarte en **Fluxa**. Confirma tu correo electrónico para activar tu cuenta y acceder a todas las funciones.

<x-mail::button :url="$url">
Verificar mi correo
</x-mail::button>

Este enlace caduca en {{ $expire }} minutos.

Si no creaste una cuenta en Fluxa, puedes ignorar este mensaje.

<x-mail::subcopy>
Si el botón no funciona, copia y pega este enlace en tu navegador: [{{ $url }}]({{ $url }})
</x-mail::subcopy>

Saludos,<br>
El equipo de Fluxa
</x-mail::message>
