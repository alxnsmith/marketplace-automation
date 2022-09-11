{{-- beautifull checkbox with label, stylized with tailwind --}}

@props([
    'name' => '',
    'value' => '',
    'checked' => false,
    'disabled' => false,
    'label' => '',
    'labelClass' => '',
])

<label @class(['inline-flex items-center', $labelClass])>
  <input type="checkbox" name="{{ $name }}" value="{{ $value }}"
    {{ $attributes->merge(['class' => 'form-checkbox h-5 w-5 text-indigo-600 transition duration-150 ease-in-out']) }}
    {{ $checked ? 'checked' : '' }} {{ $disabled ? 'disabled' : '' }}>
  @if ($label)
    <span class="ml-2">{{ $label }}</span>
  @endif
</label>
