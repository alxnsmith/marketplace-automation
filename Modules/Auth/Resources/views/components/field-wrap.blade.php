@props(['label' => null, 'class' => ''])

<div @class([
    'field-wrap flex flex-col',
    $class,
    'pt-7' => is_null($label),
])>
  @if ($label)
    <label class="mb-1">{{ $label }}</label>
  @endif
  {{ $slot }}
</div>
