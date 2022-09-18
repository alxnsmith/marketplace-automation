<x-dashboard::layouts.master title="Заказы Yandex Market">
  <div x-data="orders">
    <template x-if="status=='Empty'">
      <div class="flex h-full flex-col items-center justify-center">
        <div class="text-2xl font-semibold text-gray-700">Заказов нет</div>
      </div>
    </template>
    <template x-if="status=='Form'">
      <form action="{{ route('dashboard.tools.yandex-market.action') }}" method="post" @submit.prevent="action">
        <div class="acitons mb-3 flex items-center justify-between gap-4 border-t border-b py-2">
          <div class="options flex gap-3 border-r pr-3">
            <h2>Задачи: </h2>
            <x-dashboard::checkbox x-model="form.actions" value="get_labels" label="Скачать ярлыки" />
            <x-dashboard::checkbox x-model="form.actions" value="ready_to_ship" label="Готов к отгрузке" />
          </div>
          <x-dashboard::button name="action" value='do_actions'>Выполнить</x-button>
        </div>

        <table class="ui-table text-center">
          <thead>
            <tr>
              <th class="p-[0!important]" width="30px">
                <div class="flex items-center justify-center">
                  <x-dashboard::checkbox @change="checkAll" data-action="check_all" />
                </div>
              </th>
              <th width="70px">№</th>
              <th>Номер</th>
              <th>Когда создан</th>
              <th>Отгрузка</th>
              <th>Статус</th>
              <th>Сумма, ₽</th>
              @env('local')
              <th>Тестовый</th>
              @endenv
            </tr>
          </thead>
          <tbody>
            <template x-for="(order, idx) in Object.values(orders)">
              <tr :data-order-id="order.id">
                <td class="p-[0!important] text-center">
                  <div class="flex items-center justify-center">
                    <x-dashboard::checkbox name="orders[]" type="checkbox" data-action="check_order"
                      @change="checkOrder($event, order)" />
                  </div>
                </td>
                <td x-text="idx+1"></td>
                <td x-text="order.id"></td>
                <td>
                  <div x-text="order.creationDate.split(' ')[0]"></div>
                  <div x-text="order.creationDate.split(' ')[1]" class="text-xs text-gray-500"></div>
                </td>
                <td x-text="order.delivery.shipments[0].shipmentDate"></td>
                <td>
                  <div x-text="order.status"></div>
                  <div x-text="order.substatus" class="text-xs text-gray-500"></div>
                </td>
                <td x-text="order.itemsTotal"></td>
                @env('local')
                <td x-text="order.fake"></td>
                @endenv
              </tr>
            </template>
          </tbody>
        </table>

      </form>
    </template>
    <template x-for="_, action in doing_actions">
      <div x-data="actionsProgress(action)" class="mb-2 border-b pb-2">
        <div class="mb-1 text-sm text-gray-500" x-html="action.label"></div>
        <div class="flex items-center gap-4">
          <span class="relative inline-flex h-4 w-4 rounded-full"
            :class="{ 'bg-blue-500 ': status == 'Processing', 'bg-green-500': status == 'Success' }">
            <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-sky-400 opacity-75"
              x-show="status=='Processing'"></span>
          </span>
          <div class="text-gray-500" x-html="message"></div>
        </div>
    </template>
  </div>

  <script>
    window.Orders = @json($orders);
  </script>
  @vite(['Modules/Dashboard/Resources/assets/js/pages/show-orders.js'])

</x-dashboard::layouts.master>
