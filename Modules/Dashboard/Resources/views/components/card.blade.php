@props(['title' => '', 'bodyClass' => []])
@php
$has_header = !(empty($title) && empty($header));
$header = $header ?? "<h3 class='text-xl'>" . $title . '</h3>';
@endphp

<div {{ $attributes->merge(['class' => 'card-item']) }}>
  @unless(empty($has_header))
    <div class="card-item-head">{!! $header !!}</div>
  @endunless
  @if ($slot->isNotEmpty())
    <div @class([...$bodyClass, 'card-item-body'])>
      {{ $slot }}
    </div>
  @endif
  @unless(empty($footer))
    <div class="card-item-footer">
      {{ $footer }}
    </div>
  @endunless
</div>
