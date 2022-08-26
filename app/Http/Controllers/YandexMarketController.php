<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yandex\OAuth\OAuthClient;
use Yandex\OAuth\Exception\AuthRequestException;
use Illuminate\Support\Arr;

class YandexMarketController extends Controller
{
  private function get_client()
  {
    return new OAuthClient(env('YANDEX_CLIENT_ID'), env('YANDEX_CLIENT_SECRET'));
  }
  private function get_access_token()
  {
    return session('YANDEX_ACCESS_TOKEN');
  }
  private function get_client_id()
  {
    return env('YANDEX_CLIENT_ID');
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
    if (!request()->has('action')) return view('tools.yandex-market.get-orders-form');

    $campaign_id = request()->get('campaign_id');
    $URL = "https://api.partner.market.yandex.ru/v2/campaigns/{$campaign_id}/orders.json";

    $status = request()->get('status');
    if ($status) $URL .= "?status={$status}";
    $substatus = request()->get('substatus');
    if ($substatus) $URL .= "&substatus={$substatus}";

    // $URL = "https://webhook.site/7a5b168a-7a38-4e0b-962d-79709a80faa7";

    // Fetch data from url with next headers:
    // Authorization: OAuth oauth_token="$this->get_access_token()", oauth_client_id="$this->get_client_id()"
    $response = json_decode(file_get_contents($URL, false, stream_context_create([
      'http' => [
        'header' => "Authorization: OAuth oauth_token=\"{$this->get_access_token()}\" , oauth_client_id=\"{$this->get_client_id()}\"",
      ],
    ])), true);

    // $orders = Arr::get($response, 'orders', []);
    $orders = $response;


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
