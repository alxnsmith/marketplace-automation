<x-dashboard::layouts.master title="Заказы Yandex Market">
  <hr class="my-4" />
  <form action="{{ route('dashboard.tools.yandex-market.action') }}">
    @csrf
    <div class="acitons mb-3 flex items-center gap-4 border-t border-b py-2">
      {{-- <x-dashboard::button name="action" value="get_labels">Скачать Ярлыки</x-button> --}}
      <label>
        <input type="checkbox" name="actions[ready_to_ship]" checked>
        Готов к отгрузке
      </label>
      <label>
        <input type="checkbox" name="actions[get_labels]" checked>
        Скачать ярлыки
      </label>
      <x-dashboard::button name="action" value='do_actions'>Выполнить</x-button>
    </div>
    <x-dashboard::table class="orders-table text-center" :data="$table" :csv="true" :sizes="[70]" />
  </form>
</x-dashboard::layouts.master>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const $check_all = document.querySelector('[data-action="check_all"]');
    const [...$checkboxes] = document.querySelectorAll('[data-action="check_order"]');

    $checkboxes.setAll = val => $checkboxes.forEach($el => $el.checked = val);
    $checkboxes.hasUnchecked = () => $checkboxes.some($el => !$el.checked);
    $check_all.updateCheck = () => $check_all.checked = !$checkboxes.hasUnchecked();
    $check_all.handler = e => $checkboxes.setAll(e.target.checked);

    $check_all.addEventListener('change', $check_all.handler);
    $checkboxes.forEach($checkbox => $checkbox.addEventListener('change', $check_all.updateCheck));
  });
</script>
