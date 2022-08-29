<?php

namespace App\Http\Controllers;

use App\Services\Yandex;
use Illuminate\Http\Request;
use Yandex\OAuth\OAuthClient;

use PDFMerger\PDFMerger;

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

    return redirect()
      ->back()
      ->with('alerts', [
        ['type' => 'success', 'html' => 'Настройки сохранены'],
      ]);
  }

  public function get_orders()
  {
    $campaign_id = Yandex::Settings::get('campaign_id');
    if (!request()->has('action')) return view('tools.yandex-market.get-orders-form', compact('campaign_id'));

    $data = [...Yandex::Market::get_orders($campaign_id)];
    return view('tools.yandex-market.show-orders', $data);
  }

  public function action()
  {
    $query = request()->validate([
      'action' => 'required|in:get_labels',
      'orders' =>  'required|array',
      'orders.*' => 'required|integer',
    ]);
    switch ($query['action']) {
      case 'get_labels':
        $campaign_id = Yandex::Settings::get('campaign_id');
        $orders = $query['orders'];
        $labels = [];
        foreach ($orders as $order_id) {
          $labels[$order_id] = Yandex::Market::get_order_labels_pdf($campaign_id, $order_id);
        }

        // dd($labels);
        $merger = new PDFMerger();
        $files = [];
        foreach ($labels as $order_id => $file) {
          $files[] = $tmp_file = tempnam(sys_get_temp_dir(), "order_labels_{$order_id}_");
          file_put_contents($tmp_file, $file);
          $merger->addPDF($tmp_file);
        }
        $merger->merge('download', "labels.pdf");
        return redirect()->back()->with('alerts', [
          ['type' => 'success', 'html' => 'Ярлыки сформированы'],
        ]);
        // return view('tools.yandex-market.show-labels', compact('labels'));
    }

    return $query['orders'];
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
    return redirect($state->redirect_uri);
  }

  public function logout()
  {
    Yandex::Settings::clean();
    return redirect()->back();
  }
}
