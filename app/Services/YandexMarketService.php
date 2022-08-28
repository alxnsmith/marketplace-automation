<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Yandex\OAuth\OAuthClient;
use Yandex\OAuth\Exception\AuthRequestException;

class YandexMarketService
{
  const Settings = YandexMarketSettings::class;

  static function get_client(): OAuthClient
  {
    return new OAuthClient(self::get_client_id(), self::get_client_secret());
  }

  static function get_access_token(): string
  {
    return session('YANDEX_ACCESS_TOKEN');
  }
  static function get_client_id(): string
  {
    return env('YANDEX_CLIENT_ID');
  }
  static function get_client_secret(): string
  {
    return env('YANDEX_CLIENT_SECRET');
  }

  static function get_auth_header(): string
  {
    return 'OAuth oauth_token="' . self::get_access_token() . '" , oauth_client_id="' . self::get_client_id() . '"';
  }

  static function get_orders($campaign_id): array
  {
    $URL = "https://api.partner.market.yandex.ru/v2/campaigns/{$campaign_id}/orders.json";
    $params = request()->only(['status', 'substatus', 'fake']);

    $response = Http::withHeaders(['Authorization' => self::get_auth_header()])
      ->get($URL, $params)
      ->json();

    return $response;
  }

  static function get_access_token_on_webhook(): string
  {
    // Инициализируем OAuth-клиент
    $client = YandexMarketService::get_client();

    // Извлекаем код подтверждения из URL-параметра code ($_REQUEST['code'])
    // и обмениваем его на авторизационный токен.   
    try {
      $code = request()->get('code');
      $client->requestAccessToken($code);
    } catch (AuthRequestException $ex) {
      echo $ex->getMessage();
    }

    // Получаем токен
    return $client->getAccessToken();
  }
}

class YandexMarketSettings
{
  const SESSION_KEY = 'YANDEX_SETTINGS';

  static function sess_key($key = null)
  {
    return $key ? self::SESSION_KEY . '.' . $key : self::SESSION_KEY;
  }


  static function _get_default_settings()
  {
    return [
      'access_token' => null,
      'campaign_id' => null,
    ];
  }

  static function get($option = null): array|string
  {
    $default = self::_get_default_settings();
    $settings = session(self::sess_key(), $default);
    if (empty($option))
      return $settings;


    return Arr::get($settings, $option);
  }

  static function set($a = null, $b = null)
  {
    if (is_array($a)) return session([self::sess_key() => $a]);
    if (is_string($a)) return session([self::sess_key() . '.' . $a => $b]);
  }

  static function update($settings)
  {
    $current = self::get();
    $current = array_merge($current, $settings);
    self::set($current);
  }

  static function clean($key = null)
  {
    $default = self::_get_default_settings();
    if (empty($key)) return self::set($default);

    return session([self::sess_key($key) => $default[$key]]);
  }
}
