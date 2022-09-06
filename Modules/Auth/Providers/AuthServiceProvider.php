<?php

namespace Modules\Auth\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\Facades\Blade;

class AuthServiceProvider extends ServiceProvider
{
  /**
   * @var string $moduleName
   */
  protected $moduleName = 'Auth';

  /**
   * @var string $moduleNameLower
   */
  protected $moduleNameLower = 'auth';

  /**
   * The model to policy mappings for the application.
   *
   * @var array<class-string, class-string>
   */
  protected $policies = [
    // 'App\Models\Model' => 'App\Policies\ModelPolicy',
  ];

  /**
   * Register any authentication / authorization services.
   *
   * @return void
   */

  /**
   * Boot the application events.
   *
   * @return void
   */
  public function boot(\Illuminate\Routing\Router $router)
  {
    $this->registerTranslations();
    $this->registerConfig();
    $this->registerViews();
    $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));

    Blade::componentNamespace("Modules" . '\\' . $this->moduleName . '\\View\\Components', $this->moduleNameLower);

    $this->registerPolicies();

    $router->aliasMiddleware('can', \Modules\Auth\Http\Middleware\Authorize::class);
    $router->aliasMiddleware('auth', \Modules\Auth\Http\Middleware\Authenticate::class);
    $router->aliasMiddleware('auth.basic', \Modules\Auth\Http\Middleware\AuthenticateWithBasicAuth::class);
    $router->aliasMiddleware('auth.session', \Modules\Auth\Http\Middleware\AuthenticateSession::class);
    $router->aliasMiddleware('guest', \Modules\Auth\Http\Middleware\RedirectIfAuthenticated::class);
    $router->aliasMiddleware('password.confirm', \Modules\Auth\Http\Middleware\RequirePassword::class);
  }

  /**
   * Register the service provider.
   *
   * @return void
   */
  public function register()
  {
    $this->app->register(RouteServiceProvider::class);
  }

  /**
   * Register config.
   *
   * @return void
   */
  protected function registerConfig()
  {
    $this->publishes([
      module_path($this->moduleName, 'Config/config.php') => config_path($this->moduleNameLower . '.php'),
    ], 'config');
    $this->mergeConfigFrom(
      module_path($this->moduleName, 'Config/config.php'),
      $this->moduleNameLower
    );
  }

  /**
   * Register views.
   *
   * @return void
   */
  public function registerViews()
  {
    $viewPath = resource_path('views/modules/' . $this->moduleNameLower);

    $sourcePath = module_path($this->moduleName, 'Resources/views');

    $this->publishes([
      $sourcePath => $viewPath
    ], ['views', $this->moduleNameLower . '-module-views']);

    $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->moduleNameLower);
  }

  /**
   * Register translations.
   *
   * @return void
   */
  public function registerTranslations()
  {
    $langPath = resource_path('lang/modules/' . $this->moduleNameLower);

    if (is_dir($langPath)) {
      $this->loadTranslationsFrom($langPath, $this->moduleNameLower);
      $this->loadJsonTranslationsFrom($langPath, $this->moduleNameLower);
    } else {
      $this->loadTranslationsFrom(module_path($this->moduleName, 'Resources/lang'), $this->moduleNameLower);
      $this->loadJsonTranslationsFrom(module_path($this->moduleName, 'Resources/lang'), $this->moduleNameLower);
    }
  }

  /**
   * Get the services provided by the provider.
   *
   * @return array
   */
  public function provides()
  {
    return [];
  }

  private function getPublishableViewPaths(): array
  {
    $paths = [];
    foreach (\Config::get('view.paths') as $path) {
      if (is_dir($path . '/modules/' . $this->moduleNameLower)) {
        $paths[] = $path . '/modules/' . $this->moduleNameLower;
      }
    }
    return $paths;
  }
}
