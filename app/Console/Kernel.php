<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [];


    /**
     * Get the timezone that should be used by default for scheduled events.
     *
     * @return \DateTimeZone|string|null
     */
    protected function scheduleTimezone()
    {
        return config('app.timezone');
    }

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $email = 'minaalfy8@gmail.com';

        # master
        $schedule->command('queue:work --daemon --stop-when-empty')->withoutOverlapping()->emailOutputOnFailure($email);
        $schedule->command('queue:work --queue=client_apps --daemon --stop-when-empty')->withoutOverlapping()->emailOutputOnFailure($email);

        # backups
        $schedule->command('database:backup')->dailyAt('12:00')->daily()->emailOutputOnFailure($email);
        $schedule->command('apps:database-backup')->everyFourHours()->between('11:00', '23:00')->emailOutputOnFailure($email);
        $schedule->command('logs:collect')->hourly()->emailOutputOnFailure($email);
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
