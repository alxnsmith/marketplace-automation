<?php

namespace Modules\Dashboard\Http\Requests;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Modules\Dashboard\Services\Yandex\YandexMarketRepository;

class YandexMarketActionRequest extends FormRequest
{
  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules()
  {
    return [
      'actions' => 'required|array',
      'actions.*' => 'required|in:get_labels,ready_to_ship',

      'orders' =>  'required|array',
      'orders.*' => 'required|array',
      'orders.*.id' => 'required|integer',
      'orders.*.fake' => 'nullable|boolean',
      'orders.*.status' => ['required', new Enum(Status::class)],
      'orders.*.substatus' => ['required', new Enum(Substatus::class)],
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

enum Status: string
{
  case CANCELLED = 'CANCELLED';
  case DELIVERED = 'DELIVERED';
  case DELIVERY = 'DELIVERY';
  case PICKUP = 'PICKUP';
  case PROCESSING = 'PROCESSING';
  case REJECTED = 'REJECTED';
  case UNPAID = 'UNPAID';
}

enum Substatus: string
{
  case STARTED = 'STARTED';
  case READY_TO_SHIP = 'READY_TO_SHIP';
  case SHIPPED = 'SHIPPED';
  case CUSTOM = 'CUSTOM';
  case FULL_NOT_RANSOM = 'FULL_NOT_RANSOM';
  case PROCESSING_EXPIRED = 'PROCESSING_EXPIRED';
  case REPLACING_ORDER = 'REPLACING_ORDER';
  case RESERVATION_EXPIRED = 'RESERVATION_EXPIRED';
  case SHOP_FAILED = 'SHOP_FAILED';
  case USER_BOUGHT_CHEAPER = 'USER_BOUGHT_CHEAPER';
  case USER_CHANGED_MIND = 'USER_CHANGED_MIND';
  case USER_NOT_PAID = 'USER_NOT_PAID';
  case USER_REFUSED_DELIVERY = 'USER_REFUSED_DELIVERY';
  case USER_REFUSED_PRODUCT = 'USER_REFUSED_PRODUCT';
  case USER_REFUSED_QUALITY = 'USER_REFUSED_QUALITY';
  case USER_UNREACHABLE = 'USER_UNREACHABLE';
  case USER_WANTS_TO_CHANGE_ADDRESS = 'USER_WANTS_TO_CHANGE_ADDRESS';
  case USER_WANTS_TO_CHANGE_DELIVERY_DATE = 'USER_WANTS_TO_CHANGE_DELIVERY_DATE';
}
