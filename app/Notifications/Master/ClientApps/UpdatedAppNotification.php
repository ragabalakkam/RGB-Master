<?php

namespace App\Notifications\Master\ClientApps;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

# models
use App\Models\Apps\AppClient;
use App\Models\Versions\Version;

class UpdatedAppNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private $app_client, $old_version, $new_version;

    public function getContent()
    {
        return 'UpdatedClientApp';
    }

    public function getUrl()
    {
        return $this->app_client->domain;
    }
    
    public function __construct(AppClient $app, Version $old_version, Version $new_version)
    {
        $this->app_client = $app;
        $this->old_version = $old_version;
        $this->new_version = $new_version ?? $app->version;
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
            ->subject("$client_name update successful")
            ->greeting("Dear, " . $notifiable->name)
            ->line("We have just updated $app_name app successfully from {$this->old_version->number} to {$this->new_version->number} for $client_name at ". date("Y-m-d h:i") . ".")
            ->action("visit app", url($this->getUrl()))
            ->line("app id : " . $app_client->id)
            ->line("version : " . $this->new_version->number)
            ->line("business : " . parseName($app_client->business_type->name, 'en') . ' - ' . parseName($app_client->business_type->name, 'ar'));

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
            'from_version'  => $this->old_version->number,
            'to_version'    => $this->new_version->number,
        ];
    }
}
