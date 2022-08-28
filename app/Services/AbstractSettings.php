<?php

namespace App\Services;

use Illuminate\Support\Arr;

interface IAbstractSettings
{
  const SESSION_KEY = "YourSessionKey";
  const DEFAULT_SETTINGS = [];

  public static function sess_key(null|string $key): string;
  public static function get(null|string $option): array|string;
  public static function set(array|string $a, mixed $b): void;
  public static function update(array $settings): void;
  public static function clean(null|string $option): void;
}

abstract class AbstractSettings implements IAbstractSettings
{
  static function sess_key($key = null): string
  {
    return $key ? static::SESSION_KEY . '.' . $key : static::SESSION_KEY;
  }

  static function get($option = null): array|string
  {
    $settings = session(static::sess_key(), static::DEFAULT_SETTINGS);
    if ($option === null) return $settings;
    return Arr::get($settings, $option);
  }


  static function set($a, $b = null): void
  {
    if (is_array($a)) session([static::sess_key() => $a]);
    if (is_string($a))  session([static::sess_key() . '.' . $a => $b]);
  }

  static function update($settings): void
  {
    $current = static::get();
    $current = array_merge($current, $settings);
    static::set($current);
  }

  static function clean($option = null): void
  {
    $default = static::DEFAULT_SETTINGS;
    if (empty($key)) static::set($default);
    else session([static::sess_key($key) => $default[$key]]);
  }
}
