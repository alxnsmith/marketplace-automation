<x-dashboard::layouts.master title="Заказы Yandex Market">
  <form action="{{ route('dashboard.tools.yandex-market.orders.show') }}" method="get" class="flex flex-col gap-3">
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
        </x-dashboard::select>
      </x-dashboard::field-wrap>
      @php
        $tomorrow = now()->tomorrow();
        $tomorrow_f = $tomorrow->format('Y-m-d');
      @endphp
      <fieldset class="flex items-center gap-4">
        <x-dashboard::field-wrap label="Отгрузка С">
          <x-dashboard::input type="date" name="supplierShipmentDateFrom" value="{{ $tomorrow_f }}" />
        </x-dashboard::field-wrap>
        <x-dashboard::field-wrap label="Отгрузка По">
          <x-dashboard::input type="date" name="supplierShipmentDateTo" value="{{ $tomorrow_f }}" />
        </x-dashboard::field-wrap>
      </fieldset>
      <x-dashboard::field-wrap>
        <x-dashboard::select name="pages">
          <option value="0" selected>Все заказы</option>
          @foreach (range(50, 500, 50) as $i => $val)
            <option value="{{ $i + 1 }}">{{ $val }} шт</option>
          @endforeach
        </x-dashboard::select>
      </x-dashboard::field-wrap>
      @env('local')
      <x-dashboard::field-wrap>
        <x-dashboard::checkbox name="fake" label="Тестовый запрос" value="on" checked />
      </x-dashboard::field-wrap>
      @endenv
    </fieldset>
    <fieldset class="flex items-center justify-end gap-4">
      <x-dashboard::button name="action">Запросить</x-dashboard::button>
    </fieldset>
  </form>
</x-dashboard::layouts.master>
