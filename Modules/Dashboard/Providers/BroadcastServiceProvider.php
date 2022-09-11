<?php

namespace Modules\Dashboard\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
  /**
   * Bootstrap any application services.
   *
   * @return void
   */
  public function boot()
  {
    Broadcast::routes();

    require module_path('Dashboard', 'Routes/channels.php');
  }
}
