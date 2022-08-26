<x-dashboard-layout title="Заказы Yandex Market" back="true">
  <form action="" method="get">
    <input type="number" name="campaign_id" placeholder="campaign_id" required>
    <select name="status" required>
      <option value="">Статус</option>
      <option value="PROCESSING">Обрабатывается</option>
    </select>
    <select name="substatus" required>
      <option value="">Подстатус</option>
      <option value="READY_TO_SHIP">Можно комплектовать</option>
    </select>
    <x-button name="action">Запросить</x-button>
  </form>
</x-dashboard-layout>