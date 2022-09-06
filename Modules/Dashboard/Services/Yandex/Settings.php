<?php

namespace Modules\Dashboard\Services\Yandex;

use Modules\Dashboard\Services\AbstractSettings;

class Settings extends AbstractSettings
{
  const STATUS_UPDATE_MAX_ORDERS_COUNT = 30;
  const SESSION_KEY = 'YANDEX_SETTINGS';
  const DEFAULT_SETTINGS = [
    'access_token' => null,
    'campaign_id' => null,
  ];

  static function init($access_token)
  {
    static::set_defaults();
    static::set('access_token', $access_token);
  }
}
