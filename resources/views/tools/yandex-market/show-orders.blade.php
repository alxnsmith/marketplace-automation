<?php
$cols = ['id', 'status', 'substatus', 'total', 'fake'];
$data = Arr::map(
    $orders,
    fn($o) => [
        'id' => $o['id'],
        'status' => $o['status'],
        'substatus' => $o['substatus'],
        'total' => $o['total'],
        'fake' => $o['fake'] ? 'Да' : 'Нет',
    ],
);
?>
<x-dashboard-layout title="Заказы Yandex Market" back="true" :backUrl="route('dashboard')">
  @dump(compact('pager', 'orders'))
  <hr class="my-4" />
  <x-dashboard.table :data="$data" />
</x-dashboard-layout>
