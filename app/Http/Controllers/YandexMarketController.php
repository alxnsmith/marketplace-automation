<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yandex\OAuth\OAuthClient;
use App\Services\YandexMarketService;

class YandexMarketController extends Controller
{
  public function settings()
  {
    $settings = YandexMarketService::get_settings();

    return view('tools.yandex-market.settings', compact('settings'));
  }
  public function udpate_settings()
  {
    $settings = request()->validate([
      'settings.campaign_id' => 'required|integer',
    ])['settings'];

    YandexMarketService::set_settings($settings);

    return redirect()
      ->back()
      ->with('alerts', [
        ['type' => 'success', 'html' => 'Настройки сохранены'],
      ]);
  }

  public function get_orders()
  {
    if (!request()->has('action')) return view('tools.yandex-market.get-orders-form');

    $campaign_id = request()->get('campaign_id');
    session(['YANDEX_CAMPAIGN_ID' => $campaign_id]);


    $data = [...YandexMarketService::get_orders($campaign_id)];
    return view('tools.yandex-market.show-orders', $data);
  }

  public function login()
  {
    $state = json_encode([
      'redirect_uri' => url()->previous(),
      'state'        => 'state',
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
    session(['YANDEX_ACCESS_TOKEN' => YandexMarketService::get_access_token_on_webhook()]); // Sets token to session

    $state = json_decode(request()->get('state')); // TODO: Add validation and fallback to dashboard
    return redirect($state->redirect_uri);
  }

  public function logout()
  {
    session()->remove('YANDEX_ACCESS_TOKEN');
    return redirect()->back();
  }
}
