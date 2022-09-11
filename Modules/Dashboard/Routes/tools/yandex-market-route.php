<?php

use Modules\Dashboard\Http\Controllers\YandexMarketController;
use Illuminate\Support\Facades\Route;


Route::name('.yandex-market')->prefix('yandex-market')->controller(YandexMarketController::class)->group(function () {
  Route::middleware('yandex.auth')->group(function () {
    Route::get('/settings', 'settings')->name('.settings');
    Route::patch('/settings', 'updateSettings')->name('.settings.update');

    Route::get('/orders', 'orders')->name('.orders');
    Route::get('/orders/show', 'ordersShow')->name('.orders.show');

    Route::post('/action', 'action')->name('.action');

    Route::post('/logout', 'logout')->name('.logout');
  });

  Route::get('/login', 'login')->name('.login');
  Route::get('/callback', '_authenticate')->name('.callback');
});
