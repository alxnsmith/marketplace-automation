<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yandex\OAuth\OAuthClient;
use Yandex\OAuth\Exception\AuthRequestException;

class YandexMarketController extends Controller
{
  private function get_client()
  {
    return new OAuthClient(env('YANDEX_CLIENT_ID'), env('YANDEX_CLIENT_SECRET'));
  }

  public function settings()
  {
    $credentials = [
      'client_id'     => env('YANDEX_CLIENT_ID'),
      'client_secret' => env('YANDEX_CLIENT_SECRET'),
    ];
    return view('tools.yandex-market.settings', compact('credentials'));
  }

  public function get_orders()
  {
    $orders = [1, 2, 3];


    $data = [
      ...compact('orders')
    ];

    return view('tools.yandex-market.show-orders', $data);
  }

  public function login()
  {
    $client = $this->get_client();

    $state = json_encode([
      'redirect_uri' => url()->previous(),
      'state'        => 'state',
    ]);
    $client->authRedirect(true, OAuthClient::CODE_AUTH_TYPE, $state);
  }
  public function _authenticate()
  {
    $code = request()->get('code');

    // Инициализируем OAuth-клиент
    $clientId = env('YANDEX_CLIENT_ID');
    $clientSecret = env('YANDEX_CLIENT_SECRET');
    $client = new OAuthClient($clientId, $clientSecret);

    // Извлекаем код подтверждения из URL-параметра code ($_REQUEST['code'])
    // и обмениваем его на авторизационный токен.   
    try {
      $client->requestAccessToken($code);
    } catch (AuthRequestException $ex) {
      echo $ex->getMessage();
    }

    // Получаем токен
    $token = $client->getAccessToken();
    session(['YANDEX_ACCESS_TOKEN' => $token]); // Sets token to session

    $state = json_decode(request()->get('state')); // TODO: Add validation and fallback to dashboard
    return redirect($state->redirect_uri);
  }

  public function logout()
  {
    session()->remove('YANDEX_ACCESS_TOKEN');
    return redirect()->back();
  }
}
