<?php

use App\Models\Apps\AppClient;
use App\Models\Apps\RGBOnline;

#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=

function clear_master_logs()
{
  $apps = AppClient::installed()->get();

  # installations
  $ids = $apps->pluck('id')->toArray();
  $dir = public_path('storage/logs/installations');
  $dir_length = strlen($dir);
  $installations = [...array_filter(glob("$dir/*"), fn ($log) => !in_array(substr($log, $dir_length + 1, -4), $ids))];

  # client-logs
  $names = $apps->pluck('name.en')->toArray();
  $dir = public_path('storage/logs/client-logs');
  $dir_length = strlen($dir);
  $client_logs = [...array_filter(glob("$dir/*"), fn ($log) => !in_array(substr($log, $dir_length + 1, -4), $names))];

  # storage
  $dir = base_path('storage/logs');
  $dir_length = strlen($dir);
  $storage = glob("$dir/*");

  # show laravel.log contents
  $laravel_log_path = storage_path('logs/laravel.log');
  $laravel_log_contents = file_exists($laravel_log_path) ? file_get_contents($laravel_log_path) : null;

  # delete all
  $files = [...$installations, ...$client_logs, ...$storage];
  array_map('unlink', $files);
  return compact('installations', 'client_logs', 'storage', 'laravel_log_contents');
}

function collect_all_logs()
{
  $dest_dir = "/home/rgbksaco/RGB/logs";
  create_dir_if_not_exist($dest_dir);

  # client logs
  $paths = array_map(
    fn ($dir) => "$dir/storage/logs/laravel.log",
    RGBOnline::installed()->whereNotNull('root_dir')->pluck('root_dir', 'name->en AS name')->toArray()
  );

  # master logs
  $paths['Master'] = base_path('storage/logs/laravel.log');

  # logs
  $logs = array_filter($paths, 'file_exists');

  # collect them to $dest
  foreach ($logs as $name => $log) {
    rename($log, "$dest_dir/$name.log");
  }

  return count($logs) ? array_keys($logs) : 'No logs found';
}
