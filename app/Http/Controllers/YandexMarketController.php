<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class YandexMarketController extends Controller
{
  static function getCredentials()
  {
    // Указываем авторизационные данные
    $clientId = env('YANDEX_CLIENT_ID');
    $token = env('YANDEX_CLIENT_SECRET');
    return compact('clientId', 'token');
  }

  static function getClient()
  {
  }

  public function settings()
  {
    return view('tools.yandex-market.settings', [
      'credentials' => self::getCredentials(),
    ]);
  }
  public function get_orders()
  {
    $orders = [1, 2, 3];


    $data = [
      ...compact('orders')
    ];

    return view('tools.yandex-market.show-orders', $data);
  }
}
