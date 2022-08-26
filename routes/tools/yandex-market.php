<?php

use App\Http\Controllers\YandexMarketController;
use Illuminate\Support\Facades\Route;


Route::name('.yandex-market')->prefix('yandex-market')->controller(YandexMarketController::class)->group(function () {
  addRouteGet('settings');
  addRouteGet('get-orders');
});
