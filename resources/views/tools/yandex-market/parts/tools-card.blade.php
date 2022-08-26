@php
$links = [
['title'=>'Показать заказы', 'route'=>'dashboard.tools.yandex-market.get-orders'],
['title'=>'Принять заказы', 'route'=>'welcome'],
['title'=>'Настройки', 'route'=>'dashboard.tools.yandex-market.settings'],
];

@endphp

<x-dashboard.card title="Yandex Market">
  @if(session()->has('YANDEX_ACCESS_TOKEN'))
  <ul class="space-y-2">
    @foreach($links as $link)
    <li class="hover:underline border-b border-dashed">
      <a class="block w-full" href="{{route($link['route'])}}">{{$link['title']}}</a>
    </li>
    @endforeach
  </ul>
  @endif
  <x-slot name="footer">
    <div class="actions">
      @unless(session()->has('YANDEX_ACCESS_TOKEN'))
      <x-button href="{{ route('dashboard.tools.yandex-market.login') }}">Войти</x-button>
      @else
      <x-button href="{{ route('dashboard.tools.yandex-market.logout') }}">Выйти</x-button>
      @endif
    </div>
  </x-slot>
</x-dashboard.card>