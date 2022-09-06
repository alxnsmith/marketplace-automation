<?php

use Modules\Dashboard\Http\Controllers\YandexMarketController;
use Illuminate\Support\Facades\Route;


Route::name('.yandex-market')->prefix('yandex-market')->controller(YandexMarketController::class)->group(function () {
  Route::middleware('yandex.auth')->group(function () {
    _addRouteController('get', 'settings');
    _addRouteController('patch', 'udpate-settings');

    _addRouteController('get', 'get-orders');
    _addRouteController('get', 'action');
    _addRouteController('get', 'logout');
  });

  _addRouteController('get', 'login');
  _addRouteController('get', '_authenticate');
});
