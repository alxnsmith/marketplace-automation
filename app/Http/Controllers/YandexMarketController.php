<?php

namespace App\Http\Controllers;

use App\Helpers\Validators;
use App\Services\Yandex;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Yandex\OAuth\OAuthClient;

class YandexMarketController extends Controller
{
  public function settings()
  {
    $settings = Yandex::Settings::get();
    return view('tools.yandex-market.settings', compact('settings'));
  }

  public function udpate_settings()
  {
    $settings = request()->validate([
      'settings.campaign_id' => 'required|integer',
    ])['settings'];

    Yandex::Settings::update($settings);
    notify('Настройки успешно обновлены');

    return redirect()
      ->back();
  }

  public function get_orders()
  {
    $campaign_id = Yandex::Settings::get('campaign_id');
    if (!request()->has('action')) return view('tools.yandex-market.get-orders-form', compact('campaign_id'));

    $table = Yandex::Market::get_orders_table($campaign_id);

    return view('tools.yandex-market.show-orders', compact('table'));
  }

  public function action()
  {
    $query = request()->validate([
      'action' => 'required|in:do_actions',

      'actions' => 'required_if:do_action,do_actions|array',
      'actions.*' => [Validators::checkKeys(['ready_to_ship', 'get_labels'], '%s - Неверное действие')],

      'orders' =>  'required|array',
      'orders.*' => 'required|integer',
    ]);
    $orders = $query['orders'];

    if (Arr::has($query, 'actions.ready_to_ship')) Yandex::Market::ready_to_ship($orders);
    if (Arr::has($query, 'actions.get_labels')) return Yandex::Market::get_labels($orders);

    // return $query['orders'];
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
    Yandex::Settings::init($token);

    $state = json_decode(request()->get('state')); // TODO: Add validation and fallback to dashboard
    notify('Авторизация прошла успешно');
    return redirect($state->redirect_uri);
  }

  public function logout()
  {
    Yandex::Settings::clean();
    return redirect()->back();
  }
}
