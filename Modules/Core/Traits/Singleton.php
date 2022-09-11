<?php

namespace Modules\Core\Traits;

use Illuminate\Support\Facades\Log;

trait Singleton
{
  protected static $instance = null;

  /** call this method to get instance */
  public static function getInstance($args = null)
  {
    if (static::$instance === null) {
      $args = $args ?? self::getArgsForConstructor();
      static::$instance = new static(...$args);
    }
    return static::$instance;
  }

  /** protected to prevent cloning */
  protected function __clone()
  {
  }

  /** protected to prevent instantiation from outside of the class */
  protected function __construct()
  {
  }

  static function getArgsForConstructor()
  {
    return [];
  }
}
