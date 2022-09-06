<?php

# controllers
use App\Http\Controllers\API\Clients\ClientAppsController;

# requests
use Illuminate\Http\Request;

# models
use App\Models\Apps\App;
use App\Models\Apps\AppClient;
use App\Models\Versions\Version;

# jobs
use App\Jobs\Clients\UpdateAppJob;
use App\Models\Apps\RGBOnline;

#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=

function test_version_on_online($app_id = '6192645541', $from_id = 17, $to_id = 18)
{
  unlink(public_path("storage/logs/installations/$app_id.log"));
  $app = AppClient::find($app_id);
  $app->update(['version_id' => $from_id]);
  $app->app_db()->delete('migrations', ['migration' => '2022_06_17_211246_v_1_6_2']);
  UpdateAppJob::dispatch(['app' => $app, 'version' => Version::find($to_id)])->onQueue('client_apps');
}

function update_all_client_versions($version_id = null)
{
  if (!$version_id)
    $version_id = App::find(1)->latest_version_id;

  foreach (get_active_apps() as $app) {
    app(ClientAppsController::class)->update_version($app, new Request());
  }

  foreach (RGBOnline::whereNotIn('id', get_active_apps()->pluck('id')->toArray())->whereNotNull('installed_at')->whereNotNull('version_id')->get() as $app) {
    app(ClientAppsController::class)->update_version($app, new Request());
  }
}
