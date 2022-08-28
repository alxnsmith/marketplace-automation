<?php

namespace App\Http\Controllers;

use App\Services\YandexMarketService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
  public function index()
  {
    $data = [
      'tools' => [
        "tools.yandex-market.parts.tools-card" => [
          "settings" => YandexMarketService::Settings::get(),
        ]
      ]
    ];
    // dd($data);
    return view('dashboard', $data);
  }
}
