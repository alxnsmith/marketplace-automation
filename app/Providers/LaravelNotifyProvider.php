<?php

namespace App\Providers;

use App\Utils\LaravelNotify\LaravelNotify;
use Illuminate\Support\ServiceProvider;

class LaravelNotifyProvider extends ServiceProvider
{
  /**
   * Register services.
   *
   * @return void
   */
  public function register()
  {
    $this->app->singleton('notify', function () {
      return new LaravelNotify();
    });
  }

  /**
   * Bootstrap services.
   *
   * @return void
   */
  public function boot()
  {
    //
  }
}
