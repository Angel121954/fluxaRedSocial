{{--
    Componente: <x-btn-cancel>
    Props:
      - href  (string|null)  Si se pasa, renderiza un <a>. Si no, un <button type="button">
--}}
@props(['href' => null])

@if($href)
<a href="{{ $href }}" {{ $attributes->merge(['class' => 'btn-cancel']) }}>
    {{ $slot->isEmpty() ? 'Cancelar' : $slot }}
</a>
@else
<button type="button" {{ $attributes->merge(['class' => 'btn-cancel']) }}>
    {{ $slot->isEmpty() ? 'Cancelar' : $slot }}
</button>
@endif