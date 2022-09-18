<?php

namespace Modules\Dashboard\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Modules\Dashboard\Services\Yandex;
use Modules\Dashboard\Services\Yandex\YandexSettingsRepository;

class ProcessYandexMarketOrders implements ShouldQueue, ShouldBeUnique
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
  protected User $user;
  protected array $orders;
  protected string $action;

  /**
   * The unique ID of the job.
   *
   * @return string
   */
  public function uniqueId()
  {
    Auth::login($this->user);
    $campaign_id = YandexSettingsRepository::getInstance()->getCampaignId();

    return $campaign_id;
  }

  /**
   * Create a new job instance.
   *
   * @return void
   */
  public function __construct(User $user,  array $orders, string $action)
  {
    $this->onQueue('ym_orders');

    $this->user = $user;
    $this->orders = $orders;
    $this->action = $action;
  }

  /**
   * Execute the job.
   *
   * @return void
   */
  public function handle()
  {
    Auth::login($this->user);
    YandexSettingsRepository::getInstance();
    switch ($this->action) {
      case 'get_labels':
        $result = Yandex::Market::get_labels($this->orders, true);
        // Send raw mail to user with download url
        $message = "Заказы успешно обработаны. Скачать файл с этикетками можно по ссылке: {$result['url']}";
        Mail::raw($message, function (Message $message) {
          $message
            ->to(auth()->user()->email)
            ->subject('Этикетки для заказов с Яндекс.Маркета');
        });
        break;
      case 'ready_to_ship':
        Yandex::Market::ready_to_ship($this->orders, true);
        break;
    }
  }
}
