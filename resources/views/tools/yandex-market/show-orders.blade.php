<?php
$cols = ['id', 'status', 'substatus', 'total', 'fake'];
$data = Arr::map(
    $orders,
    fn($v, $k) => [
        '<input type="checkbox" data-action="check_all">' => [
            'class' => 'text-center',
            'html' => "<input type='checkbox' data-action='check_order' name='orders[{$k}]' value='{$v['id']}'>",
        ],
        'id' => $v['id'],
        'status' => $v['status'],
        'substatus' => $v['substatus'],
        'total' => $v['total'],
        'fake' => $v['fake'] ? 'Да' : 'Нет',
    ],
);
?>
<x-dashboard-layout title="Заказы Yandex Market" hasBack="true" :backUrl="route('dashboard')">
  @dump(compact('pager', 'orders'))
  <hr class="my-4" />
  <form action="{{ route('dashboard.tools.yandex-market.action') }}">
    @csrf
    <div class="acitons mb-3">
      <x-button name="action" value="get_labels">Скачать Ярлыки</x-button>
    </div>
    <x-dashboard.table :data="$data" :sizes="[70]" />
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
