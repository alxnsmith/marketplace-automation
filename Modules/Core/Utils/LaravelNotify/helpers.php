<?php

use Modules\Core\Utils\LaravelNotify\LaravelNotify;

if (!function_exists('notify')) {
  function notify($message = null): LaravelNotify
  {
    $notifier = app('notify');

    if (!is_null($message)) {
      return $notifier->success($message);
    }

    return $notifier;
  }
}
