<?php

namespace App\Notifications\Master\ClientApps;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UninstalledAppNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private $app_client;

    public function getContent()
    {
        return 'UninstalledClientApp';
    }

    public function getUrl()
    {
        return getConfig('url') . '/master/clients/' . $this->app_client->client_id;
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

        $app = $app_client->app;
        $app_name = parseName($app->name, 'en');
        
        $client = $app_client->client;
        $client_name = parseName($client->name, 'en');

        $mail = (new MailMessage)
            ->subject("$app_name uninstalled !")
            ->greeting("Dear, " . $notifiable->name)
            ->line("$app_name version of $client_name has been uninstalled successfully.")
            ->line("app id : " . $app_client->id)
            ->line("version : " . $app_client->version->number)
            ->line("business : " . parseName($app_client->business_type->name, 'en') . ' - ' . parseName($app_client->business_type->name, 'ar'))
            ->action("GO TO CLIENT PAGE", url($this->getUrl()));

        # log file
        $log_file = public_path("storage/logs/installations/{$app_client->id}.log");
        if (file_exists($log_file)) $mail->attach($log_file, ['as' => 'Uninstallation.log']);

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
