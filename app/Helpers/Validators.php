<?php

namespace App\Helpers;

class Validators
{
  static function checkKeys($hay_stack, $fail_message = "%s - Не верный атрибут"): \Closure
  {
    return fn ($attribute, $_, $fail) => in_array(last(explode('.', $attribute)), $hay_stack) ?: $fail(sprintf($fail_message, $attribute));
  }
}
