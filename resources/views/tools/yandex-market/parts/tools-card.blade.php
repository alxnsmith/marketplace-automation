@php
$links = [['title' => 'Показать заказы', 'route' => 'dashboard.tools.yandex-market.get-orders'], ['title' => 'Настройки', 'route' => 'dashboard.tools.yandex-market.settings']];
$is_logged_in = !empty(Arr::get($settings, 'access_token'));

$title = 'Yandex Market';
if (!empty($settings['campaign_id'])) {
    $title .= " <sup>[{$settings['campaign_id']}]</sup>";
}
@endphp

<x-dashboard.card :title="$title">
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
