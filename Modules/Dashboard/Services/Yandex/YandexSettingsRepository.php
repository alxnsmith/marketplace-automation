<?php

namespace Modules\Dashboard\Services\Yandex;

use App\Models\User;
use Illuminate\Support\Arr;
use Modules\Core\Traits\Singleton;
use Modules\Dashboard\Entities\DashboardOption;

class YandexSettingsRepository
{
  use Singleton;

  const STATUS_UPDATE_MAX_ORDERS_COUNT = 30;
  const name = 'YANDEX_SETTINGS';
  const defaults = [
    'access_token' => null,
    'campaign_id' => null,
  ];

  protected $values = null;
  public DashboardOption $model;

  function __construct(User $user)
  {
    $this->model = $user->dashboard_options()->firstOrCreate(['name' => self::name], ['values' => self::defaults]);
  }
  static function getArgsForConstructor(): array
  {
    return [auth()->user()];
  }

  public function get($key, $default = null)
  {
    return Arr::get($this->model->values, $key, $default);
  }

  public function getAccessToken(): string | null
  {
    return $this->get('access_token');
  }
  public function setAccessToken(string $token): bool
  {
    return $this->model->update(['values->access_token' => $token]);
  }

  public function getCampaignId(): string | null
  {
    return $this->get('campaign_id');
  }
  public function setCampaignId(string $campaign_id): bool
  {
    return $this->model->update(['values->campaign_id' => $campaign_id]);
  }

  public function updateValues(array $values)
  {
    return $this->model->update(['values' => array_merge($this->model->values, $values)]);
  }

  public function getValuesArray(): array
  {
    return $this->model->values;
  }


  public function reset(): bool
  {
    return $this->model->update(['values' => self::defaults]);
  }
  public function hasAccessToken(): bool
  {
    return !empty($this->getAccessToken());
  }
  public function hasCampaignId(): bool
  {
    return !empty($this->getCampaignId());
  }
}
