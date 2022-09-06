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
use App\Notifications\Master\ClientApps\InstalledAppNotification;
use App\Notifications\Master\ClientApps\AppInstallationFailedNotification;

class InstallAppJob implements ShouldQueue
{
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

	private $client_app;
	
	public $tries = 1;
	public $timeout = 86400;
	public $failOnTimeout = true;
	public $deleteWhenMissingModels = true;

	public function __construct($app)
	{
		$this->client_app = $app;
	}

	public function handle()
	{
		$app = $this->client_app;
		$app = $app->app_model() ?? $app;

		# install app
		if (method_exists($app, 'install'))
		{
			$app->start_process('installation');
			$app->install();
		}

		# set as installed & end process
		$app->end_process('installation', true);
		$app->setInstalled();

		# notify all masters
		NotifyMastersJob::dispatch(InstalledAppNotification::class, $app);
	}
	
	public function failed(Exception $exception)
	{
		$app = $this->client_app;
		$app = $app->app_model() ?? $app;

		# uninstall app
		$app->end_process('installation', false);
		$app->setInstalled(false);
		if (method_exists($app, 'uninstall')) $app->uninstall();
		
		# notify all masters
		NotifyMastersJob::dispatch(AppInstallationFailedNotification::class, $app, (string) $exception);
	}
}
