<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Yandex\OAuth\OAuthClient;
use Yandex\OAuth\Exception\AuthRequestException;

class YandexMarketService
{
  static function get_client()
  {
    return new OAuthClient(self::get_client_id(), self::get_client_secret());
  }


  static function get_access_token()
  {
    return session('YANDEX_ACCESS_TOKEN');
  }
  static function get_client_id()
  {
    return env('YANDEX_CLIENT_ID');
  }
  static function get_client_secret()
  {
    return env('YANDEX_CLIENT_SECRET');
  }

  static function get_auth_header()
  {
    return 'OAuth oauth_token="' . self::get_access_token() . '" , oauth_client_id="' . self::get_client_id() . '"';
  }

  static function get_orders($campaign_id,)
  {
    $URL = "https://api.partner.market.yandex.ru/v2/campaigns/{$campaign_id}/orders.json";
    $params = request()->only(['status', 'substatus', 'fake']);

    $response = Http::withHeaders(['Authorization' => self::get_auth_header()])
      ->get($URL, $params)
      ->json();

    return $response;
  }

  static function get_access_token_on_webhook()
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
