<x-dashboard-layout title="Заказы Yandex Market" back="true" :backUrl="route('dashboard')">
  @empty($campaign_id)
    <div class="alert alert-danger show">
      Не выбрана кампания, перейдите в <a href="{{ route('dashboard.tools.yandex-market.settings') }}">настройки</a>.
    </div>
  @else
    <form action="" method="get">
      <select name="status">
        <option value="">Статус</option>
        <option value="">Любой Статус</option>
        <option value="PROCESSING" selected>Обрабатывается</option>
      </select>
      <select name="substatus">
        <option value="">Подстатус</option>
        <option value="">Любой Подстатус</option>
        <option value="STARTED" selected>Можно комплектовать</option>
        {{-- <option value="READY_TO_SHIP">Готов к отгрузке</option> --}}
      </select>
      <label>
        <input type="checkbox" name="fake" checked>
        Тестовые
      </label>
      <x-button name="action">Запросить</x-button>
    </form>
  @endempty
</x-dashboard-layout>
