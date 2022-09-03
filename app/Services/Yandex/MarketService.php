<?php

namespace App\Services\Yandex;

use App\Services\Yandex;
use App\Services\Yandex\Settings as YandexSettings;
use Exception;
use Illuminate\Support\Arr;
use PDFMerger\PDFMerger;
use ReflectionEnum;

class MarketService implements IMarketService
{
  const Settings = YandexSettings::class;
  const Status = Status::class;
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

  static function _request_orders($campaign_id, $status = "ALL", $substatus = "ALL", $pages = 1, $fake = false)
  {
    $URL = self::url('get_orders', $campaign_id);
    $query = compact('status', 'substatus', 'fake');

    return Yandex::request($URL, 'GET', compact('query'))->json();
  }

  static function get_orders($campaign_id, $status = "ALL", $substatus = "ALL", $pages = 0, $fake = false): array
  {
    $response = static::_request_orders($campaign_id, $status, $substatus, $pages, $fake);
    ['pager' => $pager, 'orders' => $orders] = $response;

    $pages = min($pages, $pager['pagesCount']) ?: $pager['pagesCount']; // If pages has been provided as 0, then get all pages

    for ($i = 2; $i <= $pages; $i++) {
      $response = static::_request_orders($campaign_id, $status, $substatus, $i, $fake);
      $orders = array_merge($orders, $response['orders']);
    }

    return $orders;
  }

  static function get_orders_table($campaign_id, $status = "ALL", $substatus = "ALL", $pages = 0, $fake = false): array
  {
    $orders =  static::get_orders($campaign_id, $status, $substatus, $pages, $fake);

    $cols = [
      'i' => '№',
      'check_all' => '<input type="checkbox" data-action="check_all">',
      'id' => 'ID',
      'status' => 'Статус',
      'substatus' => "Подстатус",
      'fake' => "Тестовый"
    ];
    $thead = array_values($cols);
    $table = [$thead];

    foreach ($orders as $i => $order) {
      $row = [];
      $values =  array_merge($cols, Arr::only($order, array_keys($cols)));

      foreach ($values as $key => $val) {
        switch ($key) {
          case 'substatus':
            $value = SubStatus($val);
            break;
          case 'status':
            $value = Status($val);
            break;
          case 'fake':
            $value = $val ? 'Да' : 'Нет';
            break;
          case 'check_all':
            $value = "<input type='checkbox' data-action='check_order' name='orders[]' value='{$order['id']}'>";
            break;
          case 'i':
            $value = $i + 1;
            break;
          default:
            $value = $val;
            break;
        }
        $row[] = $value;
      }
      $table[] = $row;
    }
    return $table;
  }

  static function get_order_labels_pdf(int $campaign_id, int $order_id)
  {
    $URL = static::url('get_order_labels_pdf', $campaign_id, $order_id);
    $response = Yandex::request($URL);

    return $response->body();
  }

  static function get_labels($orders)
  {
    $campaign_id = Yandex::Settings::get('campaign_id');
    $labels = [];
    foreach ($orders as $order_id) {
      $labels[$order_id] = Yandex::Market::get_order_labels_pdf($campaign_id, $order_id);
    }

    $merger = new PDFMerger();
    $files = [];
    foreach ($labels as $order_id => $file) {
      $files[] = $tmp_file = tempnam(sys_get_temp_dir(), "order_labels_{$order_id}_");
      file_put_contents($tmp_file, $file);
      $merger->addPDF($tmp_file);
    }

    $merger->merge('download', "labels.pdf");

    notify('Ярлыки сформированы');
    return view('tools.yandex-market.show-labels', compact('labels'));
  }

  static function ready_to_ship($orders)
  {
    static::update_status_orders($orders, 'PROCESSING', 'READY_TO_SHIP');
    notify('Статусы успешно изменены');
  }

  static function update_status_orders($orders, $status, $substatus = null)
  {
    $campaign_id = Yandex::Settings::get('campaign_id');
    $URL = static::url('status_update', $campaign_id);

    $orders = array_map(
      fn ($order_id) => [
        'id' => $order_id,
        'status' => $status,
        'substatus' => $substatus
      ],
      $orders
    );
    $orders = array_chunk($orders, static::Settings::STATUS_UPDATE_MAX_ORDERS_COUNT);

    foreach ($orders as $chunk) {
      $params = [
        'body' => json_encode(['orders' => $chunk]),
        'headers' => [
          'Content-Type' => 'application/json'
        ]
      ];

      $response = Yandex::request($URL, 'POST', $params);

      if ($response->status() != 200) {
        throw new Exception("Ошибка при изменении статуса заказов: {$response->body()}");
      }
    }

    return $response->json();
  }
}

function Status(string $name): string
{
  return match ($name) {
    "CANCELLED" => "Заказ отменен.",
    "DELIVERED" => "Заказ получен покупателем.",
    "DELIVERY" => "Заказ передан в доставку.",
    "PICKUP" => "Заказ доставлен в пункт самовывоза.",
    "PROCESSING" => "Заказ находится в обработке.",
    "REJECTED" => "Заказ создан, но не оплачен.",
    "UNPAID" => "Заказ оформлен, но еще не оплачен (если выбрана оплата при оформлении).",
    default => "{{" . $name . "}}"
  };
}

function SubStatus(string $name): string
{
  return match ($name) {
    "ALL" => "Все",
    "STARTED" => "Заказ подтвержден, его можно начать обрабатывать.",
    "READY_TO_SHIP" => "Заказ собран и готов к отправке.",
    "SHIPPED" => "Заказ передан службе доставки.",
    "CUSTOM" => "Причина отмены заказа в свободной форме.",
    "FULL_NOT_RANSOM" => "Покупатель отказался покупать все товары из заказа.",
    "PROCESSING_EXPIRED" => "Магазин не обработал заказ в течение семи дней.",
    "REPLACING_ORDER" => "Покупатель решил заменить товар другим по собственной инициативе.",
    "RESERVATION_EXPIRED" => "Покупатель не завершил оформление зарезервированного заказа в течение 10 минут.",
    "SHOP_FAILED" => "Магазин не может выполнить заказ.",
    "USER_BOUGHT_CHEAPER" => "Покупатель нашел дешевле.",
    "USER_CHANGED_MIND" => "Покупатель отменил заказ по личным причинам.",
    "USER_NOT_PAID" => "Покупатель не оплатил заказ (для типа оплаты PREPAID) в течение 30 минут.",
    "USER_REFUSED_DELIVERY" => "Покупателя не устроили условия доставки.",
    "USER_REFUSED_PRODUCT" => "Покупателю не подошел товар.",
    "USER_REFUSED_QUALITY" => "Покупателя не устроило качество товара.",
    "USER_UNREACHABLE" => "Не удалось связаться с покупателем.",
    "USER_WANTS_TO_CHANGE_ADDRESS" => "Покупатель хочет изменить адрес доставки.",
    "USER_WANTS_TO_CHANGE_DELIVERY_DATE" => "Покупатель хочет изменить дату доставки.",
    default => "{{" . $name . "}}"
  };
}


interface IMarketService
{
  const Settings = YandexSettings::class;
  const Status = Status::class;
  const SubStatus = SubStatus::class;

  static function get_orders(string $campaign_id, string $status = 'ALL', string $substatus = 'ALL', int $pages = 0, $fake = false): array;
  static function get_orders_table(string $campaign_id, string $status = 'ALL', string $substatus = 'ALL', int $pages = 0, $fake = false): array;
}
