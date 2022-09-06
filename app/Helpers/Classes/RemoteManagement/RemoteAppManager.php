<?php

namespace App\Helpers\Classes\RemoteManagement;

use App\Helpers\Classes\RemoteManagement\Pusher;
use App\Helpers\Classes\File;
use Illuminate\Support\Str;

class RemoteAppManager
{
  private $pusher, $app, $command;

  public function __construct($app, $command = null)
  {
    $this->pusher = new Pusher;

    $this->app = $app;
    $this->command = $command;
  }

  public function broadcastOn(): string
  {
    return "private-Master.Clients.{$this->app->client_id}.{$this->app->id}";
  }

  public function broadcastAs(): string
  {
    return 'MasterCommand';
  }

  public function broadcastWith()
  {
    return [
      'id'          => time() . '-' . Str::random(10),
      'app_id'      => $this->app->id,
      'command'     => $this->command,
      'created_at'  => now()->format('Y-m-d H:i:s'),
    ];
  }

  public function trigger()
  {
    $data = $this->broadcastWith();
    $this->pusher->trigger($this->broadcastOn(), $this->broadcastAs(), File::encrypt(json_encode($data)));
    return $data;
  }

  public function getSubscribersCount()
  {
    return $this->pusher->getChannelSubscribersCount($this->broadcastOn());
  }

  // Static

  public static function sendCommand($app, $command)
  {
    return (new self($app, $command))->trigger();
  }

  public static function artisan($app, $command)
  {
    return self::sendCommand($app, "artisan $command");
  }
}
