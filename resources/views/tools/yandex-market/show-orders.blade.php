<x-dashboard-layout title="Заказы Yandex Market">
  <hr class="my-4" />
  <form action="{{ route('dashboard.tools.yandex-market.action') }}">
    @csrf
    <div class="acitons mb-3 flex items-center gap-4 border-t border-b py-2">
      {{-- <x-button name="action" value="get_labels">Скачать Ярлыки</x-button> --}}
      <label>
        <input type="checkbox" name="actions[ready_to_ship]" checked>
        Готов к отгрузке
      </label>
      <label>
        <input type="checkbox" name="actions[get_labels]" checked>
        Скачать ярлыки
      </label>
      <x-button name="action" value='do_actions'>Выполнить</x-button>
    </div>
    <x-dashboard.table class="text-center" :data="$table" :csv="true" :sizes="[70]" />
  </form>
</x-dashboard-layout>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const $check_all = document.querySelector('[data-action="check_all"]');
    const [...$checkboxes] = document.querySelectorAll('[data-action="check_order"]');

    $check_all.addEventListener('change', function() {
      $checkboxes.forEach(function($checkbox) {
        $checkbox.checked = $check_all.checked;
      });
    });

    $checkboxes.forEach(function($checkbox) {
      $checkbox.addEventListener('change', function() {
        if ($checkboxes.every(function($checkbox) {
            return $checkbox.checked;
          })) {
          $check_all.checked = true;
        } else {
          $check_all.checked = false;
        }
      });
    });

  });
</script>
