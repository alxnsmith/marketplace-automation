<?php

namespace Modules\Dashboard\Http\Requests;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Http\FormRequest;
use Modules\Dashboard\Services\Yandex\YandexMarketRepository;

class YandexMarketOrdersShowRequest extends FormRequest
{
  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules()
  {
    return [
      'status' => 'nullable|in:PROCESSING',
      'substatus' => 'nullable|in:STARTED',
      'pages' => 'nullable|integer|min:0|max:10',
      'supplierShipmentDateFrom' => 'nullable|date',
      'supplierShipmentDateTo' => 'nullable|date',
      'fake' => 'nullable',
    ];
  }

  /**
   * Determine if the user is authorized to make this request.
   *
   * @return bool
   */
  public function authorize()
  {
    return YandexMarketRepository::getInstance()->canRequestActions();
  }

  public function failedAuthorization()
  {
    throw new AuthorizationException('Необходимо авторизоваться в Yandex и указать ID кампании в настройках');
  }
}
