<?php

namespace Modules\Core\Rules;

use Illuminate\Contracts\Validation\Rule;

class CheckKeys implements Rule
{
  /**
   * Create a new rule instance.
   *
   * @return void
   */
  public function __construct($allowed_keys, $fail_message = "%s - Не верный атрибут")
  {
    $this->allowed_keys = $allowed_keys;
    $this->fail_message = $fail_message;
  }

  /**
   * Determine if the validation rule passes.
   *
   * @param  string  $attribute
   * @param  mixed  $value
   * @return bool
   */
  public function passes($attribute, $value)
  {
    // Splite attribute by dot and get last part
    $attribute = last(explode('.', $attribute));

    // Return true if attribute is in allowed keys
    return in_array($attribute, $this->allowed_keys);
  }

  /**
   * Get the validation error message.
   *
   * @return string
   */
  public function message()
  {
    return 'The validation error message.';
  }
}
