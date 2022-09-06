<?php

namespace App\Notifications\Master\ClientApps;

use App\Models\Apps\AppClient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppsBackedUpNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private $backups;

    public function getContent()
    {
        return 'AppsBackedUp';
    }

    public function getUrl()
    {
        return getConfig('url') . '/master/clients';
    }

    public function __construct($backups)
    {
        $this->backups = $backups;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast', 'mail'];
    }

    public function toMail($notifiable)
    {
        $mail = (new MailMessage)
            ->subject("Backup done for all clients !")
            ->greeting("Dear, " . $notifiable->name)
            ->line("We have just backed up all your clients' databases. This action is scheduled to be done every 4 hours starting from 10:00 AM everyday.")
            ->action("go to clients", url($this->getUrl()))
            ->line("To download any backup, just copy its link to your browser address bar. Backups are as follows:");

        $url = getConfig('url');

        $index = 1;
        foreach ($this->backups as $app_id => $backup) {
            $app = AppClient::find($app_id);
            $mail->line("#" . $index++ . " | " . ($app ? parseName($app->name, 'en') : $app_id) . " : $url/storage/" . str_replace(' ', '%20', $backup));
        }

        return $mail;
    }

    public function toArray($notifiable)
    {
        return [
            'content'   => $this->getContent(),
            'url'       => $this->getUrl(),
        ];
    }
}
