<?php

namespace Modules\Core\Traits;

trait EnumValueFromName
{
  public static function fromName(string $name): string
  {
    foreach (self::cases() as $key) {
      if ($name === $key->name) return $key->value;
    }
    throw new \ValueError("$name is not a valid backing value for enum " . self::class);
  }
}
