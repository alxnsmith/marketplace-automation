<?php

namespace Modules\Core\Utils\LaravelNotify;

use Illuminate\Support\Collection;

enum NotificationType: string
{
  case SUCCESS = 'success';
  case WARNING = 'warning';
  case DANGER = 'danger';
  case INFO = 'info';
  case PRIMARY = 'primary';
  case SECONDARY = 'secondary';
  case DARK = 'dark';
  case LIGHT = 'light';
}

class LaravelNotify implements ILaravelNotify
{
  public function _construct()
  {
  }

  public function success($content): LaravelNotify
  {
    $this->push($content, NotificationType::SUCCESS);
    return $this;
  }
  public function warning($content): LaravelNotify
  {
    $this->push($content, NotificationType::WARNING);
    return $this;
  }
  public function danger($content): LaravelNotify
  {
    $this->push($content, NotificationType::DANGER);
    return $this;
  }
  public function info($content): LaravelNotify
  {
    $this->push($content, NotificationType::INFO);
    return $this;
  }
  public function primary($content): LaravelNotify
  {
    $this->push($content, NotificationType::PRIMARY);
    return $this;
  }
  public function secondary($content): LaravelNotify
  {
    $this->push($content, NotificationType::SECONDARY);
    return $this;
  }
  public function dark($content): LaravelNotify
  {
    $this->push($content, NotificationType::DARK);
    return $this;
  }
  public function light($content): LaravelNotify
  {
    $this->push($content, NotificationType::LIGHT);
    return $this;
  }

  public function push(string $content, NotificationType $type = NotificationType::SUCCESS): void
  {
    $notification = new Notification($content, $type);
    session()->push(self::KEY, $notification->__toArray());
  }

  public function pull(): array
  {
    return session()->pull(self::KEY, $this->getDefault());
  }
  public function pullAll(): Collection
  {
    $notifications = $this->getNotifications();
    $this->clear();
    return $notifications;
  }
  public function clear(): void
  {
    session()->forget(self::KEY);
  }
  public function hasNotifications(): bool
  {
    return $this->getNotifications()->isNotEmpty();
  }

  public function getNotifications(): Collection
  {
    return collect(session(self::KEY));
  }

  public function getDefault(): Collection
  {
    return new Collection();
  }
}

class Notification implements INotification
{
  public $content;
  public NotificationType $type;

  public function __construct(string $content, NotificationType $type)
  {
    $this->content = $content;
    $this->type = $type;
  }

  public function __toArray(): array
  {
    return [
      "content" => $this->content,
      "type" => $this->type->value
    ];
  }
}

interface ILaravelNotify
{
  const KEY = "notifications";
  public function _construct();
  public function push(string $content, NotificationType $type): void;
  public function pull(): array;
  public function getDefault(): Collection;
  public function getNotifications(): Collection;
  public function hasNotifications(): bool;
  public function pullAll(): Collection;
  public function clear(): void;

  public function success($content): LaravelNotify;
  public function warning($content): LaravelNotify;
  public function danger($content): LaravelNotify;
  public function info($content): LaravelNotify;
  public function primary($content): LaravelNotify;
  public function secondary($content): LaravelNotify;
  public function dark($content): LaravelNotify;
  public function light($content): LaravelNotify;
}

interface INotification
{
  public function __construct(string $content, NotificationType $type);
  public function __toArray(): array;
}
