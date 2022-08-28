<x-dashboard-layout title="Заказы Yandex Market" back="true" :backUrl="route('dashboard')">
  <form action="" method="get">
    <input type="number" name="campaign_id" placeholder="campaign_id" value="{{session('YANDEX_CAMPAIGN_ID')}}"
      required>
    <select name="status" required>
      <option value="">Статус</option>
      <option value="PROCESSING" selected>Обрабатывается</option>
    </select>
    <select name="substatus" required>
      <option value="">Подстатус</option>
      <option value="STARTED" selected>Можно комплектовать</option>
      {{-- <option value="READY_TO_SHIP">Готов к отгрузке</option> --}}
    </select>
    <label>
      <input type="checkbox" name="fake" checked>
      Тестовые
    </label>
    <x-button name="action">Запросить</x-button>
  </form>
</x-dashboard-layout>