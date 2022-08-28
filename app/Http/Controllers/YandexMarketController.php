<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yandex\OAuth\OAuthClient;
use App\Services\YandexMarketService;

class YandexMarketController extends Controller
{
  public function settings()
  {
    $settings = YandexMarketService::Settings::get();
    return view('tools.yandex-market.settings', compact('settings'));
  }

  public function udpate_settings()
  {
    $settings = request()->validate([
      'settings.campaign_id' => 'required|integer',
    ])['settings'];

    YandexMarketService::Settings::update($settings);

    return redirect()
      ->back()
      ->with('alerts', [
        ['type' => 'success', 'html' => 'Настройки сохранены'],
      ]);
  }

  public function get_orders()
  {
    $campaign_id = YandexMarketService::Settings::get('campaign_id');
    if (!request()->has('action')) return view('tools.yandex-market.get-orders-form', compact('campaign_id'));

    $data = [...YandexMarketService::get_orders($campaign_id)];
    return view('tools.yandex-market.show-orders', $data);
  }

  public function login()
  {
    $state = json_encode([
      'redirect_uri' => url()->previous(),
    ]);

    YandexMarketService::get_client()
      ->authRedirect(true, OAuthClient::CODE_AUTH_TYPE, $state);
  }

  public function get_labels()
  {
    $campaign_id = request()->get('campaign_id');
    return YandexMarketService::get_orders($campaign_id);
  }

  public function _authenticate()
  {
    $token = YandexMarketService::get_access_token_on_webhook();
    YandexMarketService::Settings::set('access_token', $token);

    $state = json_decode(request()->get('state')); // TODO: Add validation and fallback to dashboard
    return redirect($state->redirect_uri);
  }

  public function logout()
  {
    YandexMarketService::Settings::clean();
    return redirect()->back();
  }
}
