<?php

namespace App\Http\Controllers;


use App\Services\Yandex;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
  public function index()
  {
    $data = [
      'tools' => [
        "tools.yandex-market.parts.tools-card" => [
          "settings" => Yandex::Settings::get(),
        ]
      ]
    ];
    return view('dashboard', $data);
  }
}
