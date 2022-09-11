<?php

namespace Modules\Dashboard\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use Yandex\OAuth\OAuthClient;
use Yandex\OAuth\Exception\AuthRequestException;

use Modules\Dashboard\Services\Yandex\MarketService as YandexMarketService;
use Modules\Dashboard\Services\Yandex\YandexSettingsRepository;

class Yandex
{
  static $access_token = null;
  static $campaign_id = null;

  /**
   * const Market
   * 
   * @var YandexMarketService
   */
  const Market = YandexMarketService::class;

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
    $settings = YandexSettingsRepository::getInstance();
    $access_token = $settings->getAccessToken();
    return 'OAuth oauth_token="' . $access_token . '", oauth_client_id="' . self::get_client_id() . '"';
  }

  static function get_access_token_on_webhook(): string
  {
    // Инициализируем OAuth-клиент
    $client = static::get_client();

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

  static function request($URL, $method = 'GET', $params = []): Response
  {
    $response = Http::withHeaders(['Authorization' => static::get_auth_header()])
      ->send(Str::upper($method), $URL, $params);
    if (Arr::has($response, 'error')) {
      throw new AuthRequestException($response['error']['message']);
    }

    return $response;
  }

  static function checkAuth($state = true)
  {
    $settings = YandexSettingsRepository::getInstance();

    $is_auth = $settings->hasAccessToken();
    return $state === $is_auth;
  }
}
