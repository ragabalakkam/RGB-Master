<?php

namespace App\Notifications\Master;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DatabaseBackupNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private $backup;

    public function getContent()
    {
        return 'DatabaseBackup';
    }

    public function getUrl()
    {
        return getConfig('url');
    }

    public function __construct($backup)
    {
        $this->backup = $backup;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject("Master database has been backed up !")
            ->greeting("Dear, " . $notifiable->name)
            ->line("We have just finished taking a backup of the master database containing the data of all clients. This procedure is scheduled to take place twice at 12:00 morning and evening of each day.")
            ->line("SQL file for backup is added as an attachment to this email.")
            ->action("go to master panel", url($this->getUrl()))
            ->attach($this->backup);
    }

    public function toArray($notifiable)
    {
        return [
            'content'   => $this->getContent(),
            'url'       => $this->getUrl(),
        ];
    }
}
