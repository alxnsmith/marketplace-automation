<?php

namespace Modules\Dashboard\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Bus;
use Yandex\OAuth\OAuthClient;

use Modules\Dashboard\Http\Requests\YandexMarketActionRequest;
use Modules\Dashboard\Http\Requests\YandexMarketOrdersShowRequest;
use Modules\Dashboard\Jobs\ProcessYandexMarketOrders;
use Modules\Dashboard\Services\Yandex;
use Modules\Dashboard\Services\Yandex\YandexMarketRepository;
use Modules\Dashboard\Services\Yandex\YandexSettingsRepository;


class YandexMarketController extends Controller
{
  public function settings()
  {
    $settings_values = YandexSettingsRepository::getInstance()->getValuesArray();
    $is_logged_in = Yandex::checkAuth();
    return view('dashboard::tools.yandex-market.settings', compact('settings_values', 'is_logged_in'));
  }

  public function updateSettings()
  {
    $new_values = request()->validate([
      'settings.campaign_id' => 'required|integer',
    ])['settings'];

    $settings = YandexSettingsRepository::getInstance();
    $settings->updateValues($new_values);

    notify('Настройки успешно обновлены');
    return redirect()
      ->back();
  }

  public function ordersShow(YandexMarketOrdersShowRequest $request)
  {
    $query = $request->validated();

    $yandexMarket = YandexMarketRepository::getInstance();
    $orders = $yandexMarket->getOrders($query, null, true);

    return view('dashboard::tools.yandex-market.show-orders', compact('orders'));
  }

  public function orders()
  {
    return view('dashboard::tools.yandex-market.get-orders-form');
  }

  public function action(YandexMarketActionRequest $request) // TODO: Move to API
  {
    $query = $request->validated();
    $actions = $query['actions'];
    $orders = $query['orders'];

    $jobs = [
      'get_labels' => new ProcessYandexMarketOrders(auth()->user(), Yandex::Market::prepareOrdersForAction($orders, 'get_labels'), 'get_labels'),
      'ready_to_ship' => new ProcessYandexMarketOrders(auth()->user(), Yandex::Market::prepareOrdersForAction($orders, 'ready_to_ship'), 'ready_to_ship'),
    ];

    if (in_array('get_labels', $actions) && in_array('ready_to_ship', $actions)) {
      Bus::chain([
        $jobs['get_labels'],
        $jobs['ready_to_ship'],
      ])->dispatch();
    } elseif (in_array('get_labels', $actions)) {
      Bus::dispatch($jobs['get_labels']);
    } elseif (in_array('ready_to_ship', $actions)) {
      Bus::dispatch($jobs['ready_to_ship']);
    }

    return [
      'status' => 'Processing',
      'channel' => 'JobProgress.' . auth()->id(),
      'actions' => [
        'get_labels' => [
          'status' => 'Processing',
          'label' => 'Получение этикеток',
        ],
        'ready_to_ship' => [
          'status' => 'Processing',
          'label' => 'Готовность к отправке',
        ],
      ],
    ];
  }

  public function login()
  {
    $state = json_encode([
      'redirect_uri' => url()->previous(),
    ]);

    Yandex::get_client()
      ->authRedirect(true, OAuthClient::CODE_AUTH_TYPE, $state);
  }

  public function _authenticate()
  {
    $token = Yandex::get_access_token_on_webhook();
    $settings = YandexSettingsRepository::getInstance();
    $settings->setAccessToken($token);

    $state = json_decode(request()->get('state')); // TODO: Add validation and fallback to dashboard
    notify('Авторизация прошла успешно');
    return redirect($state->redirect_uri);
  }

  public function logout()
  {
    $settings = YandexSettingsRepository::getInstance();
    $settings->reset();
    return redirect()->back();
  }
}
