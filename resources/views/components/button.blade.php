@props(['href' => false, 'class' => ''])
@php
$tag = empty($href) ? 'button' : 'a';

$attrs = [
    'type' => $tag == 'button' ? 'submit' : false,
    'href' => $href,
    'class' => 'btn ' . $class,
];
@endphp

<{{ $tag }} {{ $attributes->merge($attrs) }}>{!! $slot !!}</{{ $tag }}>
