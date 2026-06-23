@props(['url'])
<tr>
    <td class="header">
        <a href="{{ $url }}" style="display: inline-block;">
            @if (trim($slot) === 'Laravel')
            <img src="{{ asset('img/logoFluxa.png') }}" class="logo" alt="Logo de Fluxa">
            @else
            {!! $slot !!}
            @endif
        </a>
    </td>
</tr>