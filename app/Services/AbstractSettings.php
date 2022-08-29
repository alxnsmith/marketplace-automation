<?php

namespace App\Services;

use Illuminate\Support\Arr;

interface IAbstractSettings
{
  const SESSION_KEY = "YourSessionKey";
  const DEFAULT_SETTINGS = [];

  public static function set_defaults(): void;
  public static function sess_key(null|string $key): string;
  public static function get(null|string $option): array|string|null;
  public static function set(array|string $a, mixed $b): void;
  public static function has(null|string $option): bool;
  public static function update(array $settings): void;
  public static function clean(null|string $option): void;
}

abstract class AbstractSettings implements IAbstractSettings
{
  static function set_defaults(): void
  {
    static::set(static::get()); // set defaults
  }

  static function sess_key($key = null): string
  {
    return empty($key) ? static::SESSION_KEY : static::SESSION_KEY . '.' . $key;
  }

  static function get($option = null): array|string|null
  {
    $settings = session(static::sess_key(), static::DEFAULT_SETTINGS);
    if ($option === null) return $settings;
    return Arr::get($settings, $option);
  }

  static function has($option): bool
  {
    return session()->has(static::sess_key($option));
  }

  static function set($a, $b = null): void
  {
    if (is_array($a)) session([static::sess_key() => $a]);
    if (is_string($a)) session()->push(static::sess_key($a), $b);
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
