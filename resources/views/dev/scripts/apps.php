<?php

use App\Models\Apps\App;
use App\Models\Apps\AppClient;
use App\Models\Apps\RGBOnline;
use App\Models\Clients\Client;

#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=

function foreach_app(callable $callable)
{
  $clients_root_dir = getConfig('clients_root_dir');
  $dirs = [...array_filter(scandir_clear($clients_root_dir), fn ($d) => !endsWith($d, '.zip') && RGBOnline::where('root_dir', "$clients_root_dir/$d"))];

  if (!isset($_GET['dir']))
    redirect_to(getConfig('url') . "/dev@afaqrgb/test?" . http_build_query(array_merge($_GET, ['dir' => $dirs[0]])));

  $dir = $_GET['dir'];
  $callable(RGBOnline::where('root_dir', "$clients_root_dir/$dir")->first());

  $index = array_search($dir, $dirs);
  if ($index !== false && $index + 1 < count($dirs))
    redirect_to(getConfig('url') . "/dev@afaqrgb/test?" . http_build_query(array_merge($_GET, ['dir' => $dirs[$index + 1]])));
}

function foreach_installed_app(callable $callable)
{
  foreach_app(fn ($app) => $app && $app->installed_at ? $callable($app) : null);
}

function get_temp_app($database = 'temp', $dir = 'temp')
{
  $client = Client::withTrashed()->updateOrCreate([
    'email'       => 'temp@gmail.com',
  ], [
    'name'        => localize(['temp', 'temp']),
  ]);
  $client->delete();

  return RGBOnline::withTrashed()->updateOrCreate(
    [
      'id'          => '999999999',
    ],
    [
      'name'        => localize(['temp', 'temp']),
      'secret'      => "s1df56s1d65f1sdf1s65df1sfsdf",
      'root_dir'    => "/home/rgbksaco/RGB/$dir",

      'client_id'   => $client->id,
      'app_id'      => 1,
      'version_id'  => 12,

      'db_driver'   => 'mysql',
      'db_host'     => 'localhost',
      'db_database' => "rgbksaco_$database",
      'db_username' => "rgbksaco_$database",
      'db_password' => 'afaqrgb2000',
    ]
  );
}

function get_stats()
{
  $results = [];

  # today's invoices
  foreach (RGBOnline::installed()->whereNotNull('db_database')->get() as $app) {
    $date = date('Y-m-d');
    $results['invoices'][$app->id . ' | ' . parseName($app->name)] = count($app->app_db()->query("SELECT `id` FROM `invoices` WHERE `created_at` BETWEEN '$date 00:00:00' AND '$date 23:59:59'"));
  }

  # apps & versions 
  foreach (App::all() as $app) {
    $key = parseName($app->name, 'en');

    foreach ($app->versions as $version) {
      $client_apps = AppClient::where('app_id', $app->id)
        ->where('version_id', $version->id)
        ->get();

      $installed_apps = $client_apps->where('installation_time', '>', 0);
      $uninstalled_apps = $client_apps->where('uninstallation_time', '>', 0);
      $updated_apps = $client_apps->where('update_time', '>', 0);

      $avg_install_time = $installed_apps->average('installation_time');
      $avg_uninstall_time = $uninstalled_apps->average('uninstallation_time');
      $avg_update_time = $updated_apps->average('update_time');

      $results[$key][$version->number] = [
        'apps count'                  => $client_apps->count(),

        'average installation time'   => floor($avg_install_time / 60) . ':' . ($avg_install_time % 60) . ' minutes (' . $installed_apps->count() . ' times)',
        'installed apps'              => $installed_apps->map(fn ($app) => parseName($app->name, 'ar'))->toArray(),

        'average uninstallation time' => floor($avg_uninstall_time / 60) . ':' . ($avg_uninstall_time % 60) . ' minutes (' . $uninstalled_apps->count() . ' times)',
        'uninstalled apps'            => $uninstalled_apps->map(fn ($app) => parseName($app->name, 'ar'))->toArray(),

        'average update time'         => floor($avg_update_time / 60) . ':' . ($avg_update_time % 60) . ' minutes (' . $updated_apps->count() . ' times)',
        'updated apps'                => $updated_apps->map(fn ($app) => parseName($app->name, 'ar'))->toArray(),

        'installations today'         => $client_apps->whereNotNull('installed_at')->whereBetween('installed_at', ["$date 00:00:00", "$date 23:59:59"])->count(),
      ];
    }
  }

  return $results;
}

function list_apps($app_id)
{
  return array_map(
    fn ($id) => str_replace(getConfig('clients_root_dir') . '/', '', $id),
    AppClient::where('app_id', $app_id)->pluck('id')->toArray()
  );
}

function get_active_apps()
{
  $ids = [
    '1255397717', // newrawasialbarakh
    '2778990069', // newrubalraqi
    '3029362302', // newalbassmastore
    '6395991058', // newfahadbaokbah
    '6808865584', // newdallahcoffe
    '7265715153', // newhassanalitawi
    '8754430996', // newagwadalkher

    # salons
    '3552618528', // laylatisalon

    # opticals
    '7973665153', // newmillenniumoptic
    '2178638015', // newzarqaoptical

    # petrol-stations
    '4122708328', // newbinadlan
    '9670897330', // newqasbanpetrol
    '9681959604', // newalshuhaybi
  ];
  return RGBOnline::whereIn('id', $ids)->installed()->get();
}
