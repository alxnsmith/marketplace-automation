<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
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

Route::get('/', function () {
  return view('welcome');
})->name('welcome');

Route::prefix('dashboard')->middleware(['auth'])->name('dashboard')->group(function () {
  Route::get('/', [DashboardController::class, 'index']);

  Route::prefix('tools')->name('.tools')->group(function () {
    require __DIR__ . '/tools/yandex-market-route.php';
  });
});

require __DIR__ . '/auth.php';

function _addRouteController($method, $name)
{
  Route::$method('/' . $name, Str::replace('-', '_', $name))->name('.' . $name);
}
