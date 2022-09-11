<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Route;

Route::prefix('dashboard')->middleware(['auth'])->name('dashboard')->group(function () {
  Route::get('/', 'DashboardController@index')->name('.index');

  Route::prefix('tools')->name('.tools')->group(function () {
    require __DIR__ . '/tools/yandex-market-route.php';
  });
});
