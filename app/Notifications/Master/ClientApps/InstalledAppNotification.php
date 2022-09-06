<?php

namespace App\Notifications\Master\ClientApps;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InstalledAppNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private $app_client;

    public function getContent()
    {
        return 'InstalledClientApp';
    }

    public function getUrl()
    {
        return $this->app_client->domain ?? getConfig('url') . '/master/clients/' . $this->app_client->client->id;
    }
    
    public function __construct($app)
    {
        $this->app_client = $app;
    }
    
    public function via($notifiable)
    {
        return ['database', 'broadcast', 'mail'];
    }
    
    public function toMail($notifiable)
    {
        $app_client = $this->app_client;

        $creator = $app_client->creator;
        $creator_name = $creator ? ($creator->id == $notifiable->id ? 'you' : $creator->name) : 'the client';

        $app = $app_client->app;
        $app_name = parseName($app->name, 'en');
        
        $client = $app_client->client;
        $client_name = parseName($client->name, 'en');

        $mail = (new MailMessage)
            ->subject("New $app_name installation")
            ->greeting("Dear, " . $notifiable->name)
            ->line("We have just installed $app_name for $client_name successfully.")
            ->line("This app has been installed by $creator_name at {$app_client->installed_at}.")
            ->line("app id : " . $app_client->id)
            ->line("version : " . $app_client->version->number)
            ->line("business : " . parseName($app_client->business_type->name, 'en') . ' - ' . parseName($app_client->business_type->name, 'ar'))
            ->action("GO TO APP", url($this->getUrl()));

        # log file
        $log_file = public_path("storage/logs/installations/{$app_client->id}.log");
        if (file_exists($log_file)) $mail->attach($log_file, ['as' => 'Installations.log']);

        return $mail;
    }
    
    public function toArray($notifiable)
    {
        $app_client = $this->app_client;
        $client = $app_client->client;
        $app = $app_client->app;
        
        return [
            'content'       => $this->getContent(),
            'url'           => $this->getUrl(),
            'app_client'    => $app_client->only([
                'id',
                'domain',
                'installed_at',
            ]),
            'app'           => [
                'name'  => $app->name,
                'image' => $app->image,
            ],
            'client'        => [
                'id'    => $client->id,
                'name'  => $client->name,
                'image' => $client->image,
            ],
        ];
    }
}
