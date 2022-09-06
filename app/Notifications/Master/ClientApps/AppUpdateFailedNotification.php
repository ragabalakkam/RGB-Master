<?php

namespace App\Notifications\Master\ClientApps;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

# models
use App\Models\Apps\AppClient;
use App\Models\Versions\Version;

class AppUpdateFailedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private $app_client, $old_version, $new_version, $exception;

    public function getContent()
    {
        return 'AppUpdateFailed';
    }

    public function getUrl()
    {
        return getConfig('url') . '/master/clients/' . $this->app_client->client->id;
    }
    
    public function __construct(AppClient $app, Version $old_version, Version $new_version, $exception)
    {
        $this->app_client = $app;
        $this->old_version = $old_version;
        $this->new_version = $new_version ?? $app->version;
        $this->exception = $exception;
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
            ->subject("Error while updating $client_name")
            ->greeting("Dear, " . $notifiable->name)
            ->line("We are sorry to inform you that the attempt to update $app_name app from {$this->old_version->number} to {$this->new_version->number} for the client \"$client_name\" was unsuccessful.")
            ->action("modify client settings", url($this->getUrl()))
            ->line("Kindly have a look on attachments. These files contain the error message in addition to installation logs of all time for this client.")
            ->line('Try to do some changes to client settings and re-update the app.');

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
            'from_version'  => $this->old_version->number ?? 'v?.?.?',
            'to_version'    => $this->new_version->number ?? 'v?.?.?',
        ];
    }
}
