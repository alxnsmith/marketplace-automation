<?php

namespace App\Services\Yandex;

use App\Services\Yandex;
use App\Services\Yandex\Settings as YandexSettings;

class MarketService
{
  const Settings = YandexSettings::class;

  static function get_orders($campaign_id): array
  {
    $URL = "https://api.partner.market.yandex.ru/v2/campaigns/{$campaign_id}/orders.json";
    $query = request()->only(['status', 'substatus', 'fake']);
    $response = Yandex::request($URL, 'GET', compact('query'))->json();

    return $response;
  }

  static function get_order_labels_pdf(int $campaign_id, int $order_id)
  {
    $URL = "https://api.partner.market.yandex.ru/v2/campaigns/{$campaign_id}/orders/{$order_id}/delivery/labels.json";
    $response = Yandex::request($URL);

    return $response->body();
  }
}
