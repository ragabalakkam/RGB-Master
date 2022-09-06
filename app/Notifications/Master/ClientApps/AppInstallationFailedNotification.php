<?php

namespace App\Notifications\Master\ClientApps;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppInstallationFailedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private $app_client;
    private $exception;

    public function getContent()
    {
        return 'AppInstallationFailed';
    }

    public function getUrl()
    {
        return getConfig('url') . '/master/clients/' . $this->app_client->client->id;
    }
    
    public function __construct($app, $exception)
    {
        $this->app_client = $app;
        $this->exception = $exception;
    }
    
    public function via($notifiable)
    {
        return ['database', 'broadcast', 'mail'];
    }
    
    public function toMail($notifiable)
    {
        $app_client = $this->app_client;

        $creator = $app_client->creator;
        $creator_name = $creator ? ($creator->id == $notifiable->id ? 'your' : "{$creator->name}'s") : 'the client';

        $app = $app_client->app;
        $app_name = parseName($app->name, 'en');
        
        $client = $app_client->client;
        $client_name = parseName($client->name, 'en');

        $mail = (new MailMessage)
            ->subject("Error occurred while installing $app_name app")
            ->greeting("Dear, " . $notifiable->name)
            ->line("We are sorry to inform you that $creator_name attempt to install $app_name app for $client_name was unsuccessful.")
            ->line("Kindly have a look on attachments. These files contain the error message in addition to installation logs of all time for this client.")
            ->line('Try to do some changes to client settings and re-install the app.')
            ->action("MODIFY CLIENT SETTINGS", url($this->getUrl()));

        # log file
        $log_file = public_path("storage/logs/installations/{$app_client->id}.log");
        if (file_exists($log_file)) $mail->attach($log_file, ['as' => 'Installations.log']);
        
        # exception
        $exception_file = public_path("storage/logs/exceptions/{$app_client->id}.log");
        create_dir_if_not_exist(dirname($exception_file));
        file_put_contents($exception_file, $this->exception);
        $mail->attach($exception_file, ['as' => 'Exception.log']);
        
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
