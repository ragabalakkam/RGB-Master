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
use App\Notifications\Master\ClientApps\UpdatedAppNotification;
use App\Notifications\Master\ClientApps\AppUpdateFailedNotification;

class UpdateAppJob implements ShouldQueue
{
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

	private $app,	$old_version,	$new_version;

	public $tries = 1;
	public $timeout = 86400;
	public $failOnTimeout = true;
	public $deleteWhenMissingModels = true;

	public function __construct($app, $new_version = null)
	{
		$this->app = $app;
		$this->old_version = $app->version;
		$this->new_version = $new_version ?? null;
	}

	public function handle()
	{
		$app = $this->app;
		$app = $app->app_model() ?? $app;

		# update app
		$app->start_process('update');
		if (method_exists($app, 'updateVersion'))
			$app->updateVersion($this->new_version);

		# set as updateed & end process
		$app->end_process('update', true);

		# notify all masters
		NotifyMastersJob::dispatch(UpdatedAppNotification::class, $app, $this->old_version, $this->new_version);
	}

	public function failed(Exception $exception)
	{
		$app = $this->app;
		$app = $app->app_model() ?? $app;

		# set as updateed & end process
		$app->end_process('update', false);

		# notify all masters
		NotifyMastersJob::dispatch(AppUpdateFailedNotification::class, $app, $this->old_version, $this->new_version, (string) $exception);
	}
}
