<?php

namespace App\Notifications\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $url_prefix = "/verify-email/", $isNewUser, $token;

    public function __construct($token)
    {
        $this->token = $token;    
    }

    private function get_appname()
    {
        return parseName(getAppName(), 'en');
    }

    public function getUrl($email)
    {
        switch ($mailProvider = explode('.', explode('@', $email)[1])[0]) {
            case 'gmail':
                $url = 'https://mail.google.com/mail/u/0/#search/' . str_replace(' ', '+', $this->get_appname());
                break;
            case 'yahoo':
                $url = 'https://mail.yahoo.com/d/search/keyword=' . str_replace(' ', '%2520', $this->get_appname());
                break;
            default:
                $url = $mailProvider . '.com';
        }
        return $url;
    }

    public function getContent()
    {
        return $this->isNewUser ? 'welcome' : 'verifyEmailLinkResent';
    }

    public function via($notifiable)
    {
        $this->isNewUser = !!!count($notifiable->notifications);
        return ['database', 'broadcast', 'mail'];
    }

    public function toArray($notifiable)
    {
        return [
            'content' => $this->getContent(),
            'url' => $this->getUrl($notifiable->email),
        ];
    }

    public function toMail($notifiable)
    {
        $app = $this->get_appname();
        $url = $this->url_prefix . $this->token;

        if ($this->isNewUser) {
            return (new MailMessage)
                ->subject('Welcome to ' . $app)
                ->greeting('Hi, ' . firstName($notifiable->name))
                ->line('Welcome to ' . $app . ' !')
                ->line('Please confirm that you want to use this as your ' . $app . ' account email address.')
                ->action('Verify Your ' . $app . ' Email Address', url($url))
                ->line('Thank you for using our app ! ❤');
        }

        return (new MailMessage)
            ->subject('Verify your email')
            ->greeting('Hi, ' . firstName($notifiable->name))
            ->line('You have received this mail upon your request to resend email verification link. Please confirm that you want to use this as your ' . $app . ' account email address.')
            ->line('Please take into account that any verification link sent to you before is no longer valid.')
            ->action('Verify Your ' . $app . ' Email Address', url($url))
            ->line('Thank you for using our app ! ❤');
    }
}
