<?php

namespace App\Notifications\Master;

use App\Jobs\RemoveFilesJob;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CollectedLogsNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private $logs;
    private $delete_files;
    
    public function __construct($logs, $delete_files = false)
    {
        $this->logs = $logs;
        $this->delete_files = $delete_files;
    }
    
    public function via($notifiable)
    {
        return ['mail'];
    }
    
    public function toMail($notifiable)
    {
        $logs = $this->logs;

        $mail = (new MailMessage)
            ->subject("(". count($logs) .") log files detected")
            ->greeting("Hello, {$notifiable->name}")
            ->line("We have detected new log files that may contain some issues that some customers are experiencing. Check the attached files to be able to easily identify the problems.")
            ->line("Affected customers are listed below:");

        foreach ($logs as $name => $log) {
            $mail->line("- $name");
            $mail->attach($log);
        }

        if ($this->delete_files) {
            RemoveFilesJob::dispatch($logs)->onQueue('default');
        }

        return $mail;
    }
}
