@php
$links = [['title' => 'Показать заказы', 'route' => 'dashboard.tools.yandex-market.get-orders'], ['title' => 'Распечатать ярлыки', 'route' => 'dashboard.tools.yandex-market.get-labels'], ['title' => 'Принять заказы', 'route' => 'welcome'], ['title' => 'Настройки', 'route' => 'dashboard.tools.yandex-market.settings']];
$is_logged_in = !empty(Arr::get($settings, 'access_token'));
@endphp

<x-dashboard.card title="Yandex Market">
  @dump($settings)
  @if ($is_logged_in)
    <ul class="space-y-2">
      @foreach ($links as $link)
        <li class="border-b border-dashed hover:underline">
          <a class="block w-full" href="{{ route($link['route']) }}">{{ $link['title'] }}</a>
        </li>
      @endforeach
    </ul>
  @endunless
  <x-slot name="footer">
    <div class="actions">
      @unless($is_logged_in)
        <x-button href="{{ route('dashboard.tools.yandex-market.login') }}">Войти</x-button>
      @else
        <x-button href="{{ route('dashboard.tools.yandex-market.logout') }}">Выйти</x-button>
      @endunless
    </div>
  </x-slot>
</x-dashboard.card>
