<?php

namespace Modules\Dashboard\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SendJobProgress implements ShouldBroadcastNow
{
  use SerializesModels, Dispatchable, InteractsWithSockets;
  /**
   * Type of message
   *
   * @return Type
   */
  const Type = Type::class;
  /**
   * Status of message
   *
   * @return Status
   */
  const Status = Status::class;

  /**
   * The user that created the server.
   *
   * @var \App\Models\User
   */
  public $user;

  /**
   * Message to send to the user.
   * 
   * @var string|array
   */
  public string|array $Message;

  /**
   * Type of event.
   * 
   * @var Type
   */
  public Type $type;

  /**
   * Status of event.
   * 
   * @var Status
   */
  public Status $status;


  /**
   * Create a new event instance.
   *
   * @param  \App\Models\User  $user
   * @return void
   */
  public function __construct(int $user_id, string $event, string|array $message, Type $type = Type::Text, Status $status = Status::Processing)
  {
    $this->user = User::find($user_id);
    $this->event = $event;
    $this->message = $message;
    $this->type = $type;
    $this->status = $status;
  }

  /**
   * Get the channels the event should broadcast on.
   *
   * @return Channel|array
   */
  public function broadcastOn()
  {
    return new PrivateChannel('JobProgress.' . $this->user->id);
  }

  public function broadcastWith()
  {
    return [
      'type' => $this->type->name,
      'status' => $this->status->name,
      'message' => $this->message,
    ];
  }

  public function broadcastAs()
  {
    return $this->event;
  }
}

enum Type
{
  case Text;
  case Array;
}

enum Status
{
  case Processing;
  case Success;
  case Error;
}
