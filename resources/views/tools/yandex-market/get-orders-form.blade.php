<x-dashboard-layout title="Заказы Yandex Market">
  @empty($campaign_id)
    <div class="notify notify-danger show">
      Не выбрана кампания, перейдите в <a href="{{ route('dashboard.tools.yandex-market.settings') }}">настройки</a>.
    </div>
  @else
    <form action="" method="get">
      <fieldset class="flex items-center gap-4">
        <x-dashboard.field-wrap label="Статус">
          <select name="status">
            <option value="">Любой</option>
            <option value="PROCESSING" selected>Обрабатывается</option>
          </select>
        </x-dashboard.field-wrap>
        <x-dashboard.field-wrap label="Подстатус">
          <select name="substatus">
            <option value="">Любой</option>
            <option value="STARTED" selected>Можно комплектовать</option>
            {{-- <option value="READY_TO_SHIP">Готов к отгрузке</option> --}}
          </select>
        </x-dashboard.field-wrap>
        <x-dashboard.field-wrap label="Кол-во">
          <select name="orders">
            <option value="" selected>Все</option>
            @foreach (range(50, 500, 50) as $i)
              <option value="{{ $i }}">{{ $i }}</option>
            @endforeach
          </select>
        </x-dashboard.field-wrap>
        <x-dashboard.field-wrap>
          <label>
            <input type="checkbox" name="fake" checked>
            Тестовые
          </label>
        </x-dashboard.field-wrap>
        <x-dashboard.field-wrap class="ml-auto">
          <x-button name="action">Запросить</x-button>
        </x-dashboard.field-wrap>
      </fieldset>
    </form>
  @endempty
</x-dashboard-layout>
