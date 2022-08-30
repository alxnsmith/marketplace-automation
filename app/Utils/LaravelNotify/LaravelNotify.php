<?php

namespace App\Utils\LaravelNotify;

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
  public function __construct()
  {
  }

  public function success($message): LaravelNotify
  {
    $this->push($message, NotificationType::SUCCESS);
    return $this;
  }
  public function warning($message): LaravelNotify
  {
    $this->push($message, NotificationType::WARNING);
    return $this;
  }
  public function danger($message): LaravelNotify
  {
    $this->push($message, NotificationType::DANGER);
    return $this;
  }
  public function info($message): LaravelNotify
  {
    $this->push($message, NotificationType::INFO);
    return $this;
  }
  public function primary($message): LaravelNotify
  {
    $this->push($message, NotificationType::PRIMARY);
    return $this;
  }
  public function secondary($message): LaravelNotify
  {
    $this->push($message, NotificationType::SECONDARY);
    return $this;
  }
  public function dark($message): LaravelNotify
  {
    $this->push($message, NotificationType::DARK);
    return $this;
  }
  public function light($message): LaravelNotify
  {
    $this->push($message, NotificationType::LIGHT);
    return $this;
  }

  public function push(string $message, NotificationType $type = NotificationType::SUCCESS): void
  {
    session()->push(self::KEY, (new Notification($message, $type))->__toArray());
  }

  public function pull(): array
  {
    return session()->pull(self::KEY, $this->get_default());
  }
  public function get(): Collection
  {
    return session(self::KEY);
  }

  public function get_default(): Collection
  {
    return new Collection();
  }
}

class Notification implements INotification
{
  public string $message;
  public NotificationType $type;
  public function __construct(string $message, NotificationType $type)
  {
    $this->message = $message;
    $this->type = $type;
  }

  public function __toArray(): array
  {
    return [
      "message" => $this->message,
      "type" => $this->type->value
    ];
  }
}

interface ILaravelNotify
{
  const KEY = "notifies";
  public function __construct();
  public function push(string $message, NotificationType $type): void;
  public function pull(): array;
  public function get(): Collection;
  public function get_default(): Collection;

  public function success($message): LaravelNotify;
  public function warning($message): LaravelNotify;
  public function danger($message): LaravelNotify;
  public function info($message): LaravelNotify;
  public function primary($message): LaravelNotify;
  public function secondary($message): LaravelNotify;
  public function dark($message): LaravelNotify;
  public function light($message): LaravelNotify;
}

interface INotification
{
  public function __construct(string $message, NotificationType $type);
  public function __toArray(): array;
}
