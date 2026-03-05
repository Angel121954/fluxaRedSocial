{{--
    Componente: <x-form-group>
    Props:
      - name    (string)  Campo para detectar errores con @error
      - label   (string)  Texto del label
      - hint    (string)  Texto de ayuda opcional bajo el label
--}}
@props(['name', 'label', 'hint' => null])

<div class="form-group">
    <label class="form-label" for="input-{{ $name }}">{{ $label }}</label>

    @if($hint)
    <span class="form-hint">{{ $hint }}</span>
    @endif

    {{ $slot }}

    @error($name)
    <span class="form-error">{{ $message }}</span>
    @enderror
</div>