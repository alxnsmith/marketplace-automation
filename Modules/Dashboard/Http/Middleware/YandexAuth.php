<?php

namespace Modules\Dashboard\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Dashboard\Services\Yandex;

class YandexAuth
{
  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure  $next
   * @return mixed
   */
  public function handle(Request $request, Closure $next)
  {
    if (Yandex::checkAuth(false)) {
      return redirect()
        ->route('dashboard')
        ->withErrors(['Необходима авторизация в Yandex']);
    }

    return $next($request);
  }
}
