<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Yandex\OAuth\OAuthClient;
use Yandex\OAuth\Exception\AuthRequestException;

class YandexMarketService
{
  static $default_settings = [
    'campaign_id' => null,
  ];

  static function get_client(): OAuthClient
  {
    return new OAuthClient(self::get_client_id(), self::get_client_secret());
  }

  static function get_settings(): array
  {
    $default = self::$default_settings;
    return session('YANDEX_SETTINGS', $default);
  }
  static function set_settings($settings)
  {
    session(['YANDEX_SETTINGS' => $settings]);
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
