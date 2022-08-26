@props(['title'=>''])
@php
  $head = $header ?? "<h3 class='text-xl'>" . $title . '</h3>'; 
@endphp

<div class="card-item">
  @unless(empty($head))
    <div class="card-item-head">{!! $head !!}</div>
  @endunless
  @unless(empty($slot))
    <div class="card-item-body">
      {{$slot}}
    </div>
  @endunless
  @unless(empty($footer))
    <div class="card-item-footer">
      {{$footer}}
    </div>
  @endunless
</div>