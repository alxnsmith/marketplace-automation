<x-dashboard::layouts.master title="Заказы Yandex Market">
  <form action="{{ route('dashboard.tools.yandex-market.orders.show') }}" method="get">
    <fieldset class="flex items-center gap-4">
      <x-dashboard::field-wrap label="Статус">
        <x-dashboard::select name="status">
          <option value="">Любой</option>
          <option value="PROCESSING" selected>Обрабатывается</option>
        </x-dashboard::select>
      </x-dashboard::field-wrap>
      <x-dashboard::field-wrap label="Подстатус">
        <x-dashboard::select name="substatus">
          <option value="">Любой</option>
          <option value="STARTED" selected>Можно комплектовать</option>
          {{-- <option value="READY_TO_SHIP">Готов к отгрузке</option> --}}
        </x-dashboard::select>
      </x-dashboard::field-wrap>
      <x-dashboard::field-wrap label="Кол-во">
        <x-dashboard::select name="pages">
          <option value="0" selected>Все</option>
          @foreach (range(50, 500, 50) as $i => $val)
            <option value="{{ $i + 1 }}">{{ $val }}</option>
          @endforeach
        </x-dashboard::select>
      </x-dashboard::field-wrap>
      @env('local')
      <x-dashboard::field-wrap>
        <x-dashboard::checkbox name="fake" label="Тестовый запрос" value="on" checked />
      </x-dashboard::field-wrap>
      @endenv
      <x-dashboard::field-wrap class="ml-auto">
        <x-dashboard::button name="action">Запросить</x-button>
      </x-dashboard::field-wrap>
    </fieldset>
  </form>
</x-dashboard::layouts.master>
