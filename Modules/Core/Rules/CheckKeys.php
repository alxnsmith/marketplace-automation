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
    $attribute = last(explode('.', $attribute)); // Split attribute by dot and get last part
    $is_valid = in_array($attribute, $this->allowed_keys); // Return true if attribute is in allowed keys

    // If attribute is not in allowed keys - set fail message
    if (!$is_valid) $this->fail_message = sprintf($this->fail_message, $attribute);

    return $is_valid;
  }

  /**
   * Get the validation error message.
   *
   * @return string
   */
  public function message()
  {
    return $this->fail_message;
  }
}
