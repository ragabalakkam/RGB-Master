<?php

namespace App\Helpers\Classes\RemoteManagement;

use Pusher\Pusher as PusherClient;

class Pusher extends PusherClient
{
  public function __construct()
  {
    $config = config('broadcasting.connections.pusher');
    parent::__construct($config['key'], $config['secret'], $config['app_id'], $config['options']);
  }

  public function getChannelSubscribersCount($channel)
  {
    return $this->get_channel_info($channel, ['info' => 'subscription_count'])->subscription_count;
  }

  public function getSubscribersCount()
  {
    $subscription_counts = [];
    foreach ($this->get_channels()->channels as $channel => $v) {
      $subscription_counts[$channel] = $this->getChannelSubscribersCount($channel);
    }
    return $subscription_counts;
  }
}
