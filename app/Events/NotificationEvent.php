<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationEvent implements ShouldBroadcast
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  private $notification;

  public function __construct($notification)
  {
    $this->notification = $notification;
  }

  public function broadcastOn()
  {
    return new PrivateChannel('App.User.' . $this->notification->user_id);
  }

  public function broadcastWith()
  {
    return [
      'notification' => $this->notification
    ];
  }

  public function broadcastAs()
  {
    return 'Notification';
  }
}
