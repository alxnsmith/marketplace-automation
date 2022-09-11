<?php

namespace Modules\Dashboard\Services\Yandex;

use Illuminate\Support\Arr;
use Modules\Core\Traits\Singleton;
use Modules\Dashboard\Services\Yandex;

class YandexMarketRepository
{
  use Singleton;

  public YandexSettingsRepository $settings;

  function __construct()
  {
    $this->settings = YandexSettingsRepository::getInstance();
  }


  function getCampaignId()
  {
    return $this->settings->getCampaignId();
  }

  /**
   * Get orders from Yandex Market by query and filter fields.
   * If $fields is null - return all fields.
   * 
   * @param array $query
   * @param ?array $fields
   * @param bool $assoc
   */
  function getOrders(array $query = [], ?array $fields = null, bool $assoc = false): array
  {
    $orders = Yandex::Market::get_orders($this->getCampaignId(), ...$query);
    if ($fields) $orders = Arr::map($orders, fn ($order) => Arr::only($order, $fields));
    if ($assoc) $orders = Arr::pluck($orders, null, 'id');

    return $orders;
  }

  /**
   * Can request actions to Yandex Market.
   * 
   * @return bool
   */
  function canRequestActions(): bool
  {
    $is_auth = auth()->check();
    $is_auth_yandex = $this->settings->hasAccessToken();
    $campaign_is_set = $this->settings->hasCampaignId();
    return $is_auth && $is_auth_yandex && $campaign_is_set;
  }
}
