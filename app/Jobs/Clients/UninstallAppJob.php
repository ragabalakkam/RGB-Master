<?php

namespace App\Jobs\Clients;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Exception;

# jobs
use App\Jobs\Roles\NotifyMastersJob;

# notifications
use App\Notifications\Master\ClientApps\UninstalledAppNotification;
use App\Notifications\Master\ClientApps\AppUninstallationFailedNotification;

class UninstallAppJob implements ShouldQueue
{
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

	private $app_client;
	
	public $tries = 1;
	public $timeout = 86400;
	public $failOnTimeout = true;
	public $deleteWhenMissingModels = true;

	public function __construct($app)
	{
		$this->app_client = $app;
	}

	public function handle()
	{
		$app = $this->app_client;
		$app = $app->app_model() ?? $app;

		# uninstall app
		if (method_exists($app, 'uninstall'))
		{
			$app->start_process('uninstallation');
			$app->uninstall();
		}

		# set as uninstalled & end process
		$app->end_process('uninstallation', true);
		$app->setInstalled(false);

		# notify all masters
		NotifyMastersJob::dispatch(UninstalledAppNotification::class, $app);
	}
	
	public function failed(Exception $exception)
	{
		$app = $this->app_client;
		$app = $app->app_model() ?? $app;

		# mark app as ununinstalled anyways
		$app->end_process('uninstallation', false);
		$app->setInstalled(false);
		
		# notify all masters
		NotifyMastersJob::dispatch(AppUninstallationFailedNotification::class, $app, (string) $exception);
	}
}
