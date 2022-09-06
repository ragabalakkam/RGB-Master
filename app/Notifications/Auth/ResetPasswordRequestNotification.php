<?php

namespace App\Notifications\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function getContent()
    {
        return 'passwordResetLinkSent';
    }

    public function getUrl()
    {
        return '/';
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast', 'mail'];
    }

    public function toMail($notifiable)
    {
        $app = parseName(getAppName(), 'en');
        return (new MailMessage)
            ->subject('Reset Your Password')
            ->greeting('Hi, ' . firstName($notifiable->name))
            ->line("You are receiving this email upon your request to reset your $app account password. In case you have not requested a password reset please dismiss this email.")
            ->line('Please take into account that any reset password link sent to you before is no longer valid.')
            ->action('Reset Your ' . ucfirst($app) . ' Password', url('/auth/reset-password/' . $this->token))
            ->line('Thank you for using our application! ❤');
    }

    public function toArray($notifiable)
    {
        return [
            'content' => $this->getContent(),
            'url' => $this->getUrl()
        ];
    }
}
