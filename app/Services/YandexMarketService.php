<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Response;
use Yandex\OAuth\OAuthClient;
use Yandex\OAuth\Exception\AuthRequestException;

class YandexMarketService
{
  const Settings = YandexMarketSettings::class;

  static function get_client(): OAuthClient
  {
    return new OAuthClient(self::get_client_id(), self::get_client_secret());
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
    return 'OAuth oauth_token="' . self::Settings::get('access_token') . '" , oauth_client_id="' . self::get_client_id() . '"';
  }

  static function get_orders($campaign_id): array
  {
    $URL = "https://api.partner.market.yandex.ru/v2/campaigns/{$campaign_id}/orders.json";
    $params = request()->only(['status', 'substatus', 'fake']);

    $response = Http::withHeaders(['Authorization' => self::get_auth_header()])
      ->get($URL, $params)
      ->json();

    if (Arr::has($response, 'error')) {
      throw new AuthRequestException($response['error']['message']);
    }

    return $response;
  }

  static function get_order_labels_pdf(int $campaign_id, int $order_id)
  {
    $URL = "https://api.partner.market.yandex.ru/v2/campaigns/{$campaign_id}/orders/{$order_id}/delivery/labels.json";
    $response = Http::withHeaders(['Authorization' => self::get_auth_header()])
      ->get($URL);
    $filename = $order_id . '.pdf';

    return $response->body();
    // return Response::make($response->body(), 200, [
    //   'Content-Type' => 'application/pdf',
    //   'Content-Disposition' => 'inline; filename="' . $filename . '"'
    // ]);
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

class YandexMarketSettings extends AbstractSettings
{
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
  static function is_logged_in()
  {
    return static::get('access_token') !== null;
  }
}
