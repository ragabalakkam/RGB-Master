<?php

namespace App\Notifications\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailVerifiedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function getUrl()
    {
        return '/';
    }

    public function getContent()
    {
        return 'emailVerified';
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toArray($notifiable)
    {
        return [
            'content' => $this->getContent(),
            'url' => $this->getUrl(),
        ];
    }
}
