<?php

namespace App\Notifications\Admins;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ChangedConfigurationsNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private $user, $configutations;
    
    public function __construct($user, $configutations)
    {
        $this->user = $user;
        $this->configutations = $configutations;
    }
    
    public function via($notifiable)
    {
        return ['broadcast', 'database'];
    }
    
    public function toArray($notifiable)
    {
        return [
            'content' => 'changedConfigurations',
            'url' => '/settings/network-sharing',
            'user' => [
                'name' => $this->user->name,
                'image' => $this->user->image()->src ?? null,
            ],
            'changes' => $this->configutations
        ];
    }
}
