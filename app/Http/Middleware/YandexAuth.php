<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

use App\Services\Yandex;


class YandexAuth
{
  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
   * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
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
