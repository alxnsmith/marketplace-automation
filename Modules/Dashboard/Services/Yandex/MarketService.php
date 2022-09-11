<?php

namespace Modules\Dashboard\Services\Yandex;

use Modules\Dashboard\Services\Yandex;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Modules\Dashboard\Events\SendJobProgress;
use PDFMerger\PDFMerger;

class MarketService implements IMarketService
{
  /**
   * const Yandex
   * 
   * @var Yandex
   */
  const Yandex = Yandex::class;
  /**
   * const Status
   * 
   * @var Status
   */
  const Status = Status::class;

  /**
   * const Substatus
   * 
   * @var Substatus
   */
  const SubStatus = SubStatus::class;

  static function url($name, ...$args)
  {
    $urls = [
      'get_orders' => 'https://api.partner.market.yandex.ru/v2/campaigns/%s/orders.json',
      'status_update' => 'https://api.partner.market.yandex.ru/v2/campaigns/%s/orders/status-update',
      'get_order_labels_pdf' => 'https://api.partner.market.yandex.ru/v2/campaigns/%s/orders/%s/delivery/labels.json',
    ];
    return sprintf($urls[$name], ...$args);
  }

  static function _request_orders($campaign_id, $status = "ALL", $substatus = "ALL", $page = 1, $fake = false)
  {
    $URL = self::url('get_orders', $campaign_id);
    $query = compact('status', 'substatus', 'fake', 'page');

    return self::Yandex::request($URL, 'GET', compact('query'))->json();
  }

  static function get_orders($campaign_id, $status = "ALL", $substatus = "ALL", $pages = 0, $fake = false): array
  {
    $page = 1;
    $response = static::_request_orders($campaign_id, $status, $substatus, $page++, $fake);
    ['pager' => $pager, 'orders' => $orders] = $response;

    $pages = min($pages, $pager['pagesCount']) ?: $pager['pagesCount']; // If pages has been provided as 0, then get all pages

    for ($page; $page <= $pages; $page++) {
      $response = static::_request_orders($campaign_id, $status, $substatus, $page, $fake);
      $orders = array_merge($orders, $response['orders']);
    }
    return $orders;
  }

  static function get_order_labels_pdf(int $campaign_id, int $order_id)
  {
    $URL = static::url('get_order_labels_pdf', $campaign_id, $order_id);
    $response = self::Yandex::request($URL);

    return $response;
  }

  static function get_labels($orders, $broadcast = false)
  {
    $dispatch = fn (...$args) => SendJobProgress::dispatch(auth()->id(), 'get_labels', ...$args);
    if ($broadcast) $dispatch('Запуск процесса получения этикеток');

    $settings = YandexSettingsRepository::getInstance();
    $campaign_id = $settings->getCampaignId();

    $labels = [];
    $i = 1;
    foreach ($orders as $order_id) {
      $response = self::get_order_labels_pdf($campaign_id, $order_id);
      if ($response->status() == 200) $labels[$order_id] = $response;
      if ($broadcast) $dispatch('Получение этикеток: ' . $i++ . '/' . count($orders));
    }

    $merger = new PDFMerger();
    $files = [];
    $i = 1;
    foreach ($labels as $order_id => $file) {
      $files[] = $tmp_file = tempnam(sys_get_temp_dir(), "order_labels_{$order_id}_");
      file_put_contents($tmp_file, $file);
      $merger->addPDF($tmp_file);
      if ($broadcast) {
        $dispatch('Объединение этикеток: ' . $i++ . '/' . count($labels));
      }
    }

    $path = "tmp/";
    $dir = Storage::disk('public')->path($path);
    $filename = uniqid('label_') . '.pdf';

    if (!is_dir($dir)) mkdir($dir, 0777, true);
    $merger->merge('file',  $dir . $filename);

    $url = Storage::disk('public')->url($path . $filename);

    $result = ['disk' => 'public', 'file' => $path . $filename, 'url' => $url];
    if ($broadcast) {
      $dispatch($result, SendJobProgress::Type::Array, SendJobProgress::Status::Success);
    }
    return $result;
  }

  static function ready_to_ship($orders, $broadcast = false)
  {
    static::update_status_orders($orders, 'PROCESSING', 'READY_TO_SHIP', $broadcast);
  }

  static function update_status_orders($orders, $status, $substatus = null, $broadcast = false)
  {
    $dispatch = fn (...$args) => SendJobProgress::dispatch(auth()->id(), 'ready_to_ship', ...$args);
    if ($broadcast) $dispatch('Запуск обработки заказов');

    $settings = YandexSettingsRepository::getInstance();
    $campaign_id = $settings->getCampaignId();
    $URL = static::url('status_update', $campaign_id);

    $orders = array_map(
      fn ($order_id) => [
        'id' => $order_id,
        'status' => $status,
        'substatus' => $substatus
      ],
      $orders
    );
    $orders = array_chunk($orders, $settings::STATUS_UPDATE_MAX_ORDERS_COUNT);

    $i = 1;
    foreach ($orders as $chunk) {
      $params = [
        'body' => json_encode(['orders' => $chunk]),
        'headers' => [
          'Content-Type' => 'application/json'
        ]
      ];


      $response = self::Yandex::request($URL, 'POST', $params);
      if ($response->status() != 200) throw new Exception("Ошибка при изменении статуса заказов: {$response->body()}");

      if ($broadcast) $dispatch('Обработка заказов: ' . $i++ . '/' . count($orders));
      $i += count($chunk);
    }

    if ($broadcast) $dispatch('Обработка заказов завершена', SendJobProgress::Type::Text, SendJobProgress::Status::Success);
  }

  static function filter_orders(array $orders, null|string|array $status = null, null|string|array $substatus = null, ?bool $fake = null)
  {
    $compare = function ($value, $filter) {
      if (is_array($filter)) {
        return in_array($value, $filter);
      } else {
        return $value == $filter;
      }
    };

    return Arr::where($orders, function ($value, $key) use ($status, $substatus, $fake, $compare) {
      $result = true;
      if ($status) $result = $result && $compare($value['status'],  $status);
      if ($substatus) $result = $result && $compare($value['substatus'], $substatus);
      if ($fake) $result = $result && $value['fake'] == $fake;
      return $result;
    });
  }

  static function prepareOrdersForAction($orders, $action)
  {
    switch ($action) {
      case 'ready_to_ship':
        $orders = static::filter_orders($orders, 'PROCESSING', 'STARTED');
        $orders = Arr::pluck($orders, 'id');
        break;
      case 'get_labels':
        $orders = static::filter_orders($orders, 'PROCESSING');
        $orders = Arr::pluck($orders, 'id');
        break;
    }

    return $orders;
  }
}


// enum Status: string
// {
//   use EnumValueFromName;

//   case CANCELLED = "Заказ отменен.";
//   case DELIVERED = "Заказ получен покупателем.";
//   case DELIVERY = "Заказ передан в доставку.";
//   case PICKUP = "Заказ доставлен в пункт самовывоза.";
//   case PROCESSING = "Заказ находится в обработке.";
//   case REJECTED = "Заказ создан, но не оплачен.";
//   case UNPAID = "Заказ оформлен, но еще не оплачен (если выбрана оплата при оформлении).";
// }


// enum SubStatus: string
// {
//   use EnumValueFromName;

//   case STARTED = "Заказ подтвержден, его можно начать обрабатывать.";
//   case READY_TO_SHIP = "Заказ собран и готов к отправке.";
//   case SHIPPED = "Заказ передан службе доставки.";
//   case CUSTOM = "Причина отмены заказа в свободной форме.";
//   case FULL_NOT_RANSOM = "Покупатель отказался покупать все товары из заказа.";
//   case PROCESSING_EXPIRED = "Магазин не обработал заказ в течение семи дней.";
//   case REPLACING_ORDER = "Покупатель решил заменить товар другим по собственной инициативе.";
//   case RESERVATION_EXPIRED = "Покупатель не завершил оформление зарезервированного заказа в течение 10 минут.";
//   case SHOP_FAILED = "Магазин не может выполнить заказ.";
//   case USER_BOUGHT_CHEAPER = "Покупатель нашел дешевле.";
//   case USER_CHANGED_MIND = "Покупатель отменил заказ по личным причинам.";
//   case USER_NOT_PAID = "Покупатель не оплатил заказ (для типа оплаты PREPAID) в течение 30 минут.";
//   case USER_REFUSED_DELIVERY = "Покупателя не устроили условия доставки.";
//   case USER_REFUSED_PRODUCT = "Покупателю не подошел товар.";
//   case USER_REFUSED_QUALITY = "Покупателя не устроило качество товара.";
//   case USER_UNREACHABLE = "Не удалось связаться с покупателем.";
//   case USER_WANTS_TO_CHANGE_ADDRESS = "Покупатель хочет изменить адрес доставки.";
//   case USER_WANTS_TO_CHANGE_DELIVERY_DATE = "Покупатель хочет изменить дату доставки.";
// }


interface IMarketService
{
  const Status = Status::class;
  const SubStatus = SubStatus::class;

  static function get_orders(string $campaign_id, string $status = 'ALL', string $substatus = 'ALL', int $pages = 0, $fake = false): array;
  // static function get_orders_table(string $campaign_id, string $status = 'ALL', string $substatus = 'ALL', int $pages = 0, $fake = false): array;
}
