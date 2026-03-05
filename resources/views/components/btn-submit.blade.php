{{--
    Componente: <x-btn-submit>
    Props:
      - type  (string)  Tipo del botón, por defecto "submit"
--}}
@props(['type' => 'submit'])

<button type="{{ $type }}" {{ $attributes->merge(['class' => 'btn-submit']) }}>
    {{ $slot }}
</button>