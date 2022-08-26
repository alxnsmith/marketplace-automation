@php
$links = [
['title'=>'Показать заказы', 'route'=>'dashboard.tools.yandex-market.get-orders'],
['title'=>'Принять заказы', 'route'=>'welcome'],
['title'=>'Настройки', 'route'=>'dashboard.tools.yandex-market.settings'],
];
@endphp

<x-dashboard.card title="Yandex Market">
  <ul class="space-y-2">
    @foreach($links as $link)
    <li class="hover:underline border-b border-dashed">
      <a class="block w-full" href="{{route($link['route'])}}">{{$link['title']}}</a>
    </li>
    @endforeach
  </ul>
</x-dashboard.card>