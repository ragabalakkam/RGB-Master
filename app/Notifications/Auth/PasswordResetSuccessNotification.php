<?php

namespace App\Notifications\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordResetSuccessNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function getContent()
    {
        return 'passwordResetSuccess';
    }

    public function getUrl()
    {
        return '/';
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast', 'mail'];
    }

    public function toArray($notifiable)
    {
        return [
            'content' => $this->getContent(),
            'url' => $this->getUrl()
        ];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Password Reset Successfully')
            ->greeting('Hi, ' . firstName($notifiable->name))
            ->line('Your password has been reset successfully. You may be missing some security options that keeps you safe.')
            ->action('Advanced Security Options', url('/'))
            ->line('Thank you for using our application! ❤');
    }
}
